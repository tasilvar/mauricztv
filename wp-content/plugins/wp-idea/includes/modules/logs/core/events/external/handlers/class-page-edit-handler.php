<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\modules\logs\core\events\external\handlers;

use bpmj\wpidea\Current_Request;
use bpmj\wpidea\Courses;
use bpmj\wpidea\sales\product\api\Interface_Product_API;
use bpmj\wpidea\events\actions\Action_Name;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\events\Interface_Event_Handler;
use bpmj\wpidea\learning\course\Interface_Readable_Course_Repository;
use bpmj\wpidea\learning\course\Page_ID;
use bpmj\wpidea\settings\LMS_Settings;
use bpmj\wpidea\templates_system\groups\settings\Template_Group_Settings;
use bpmj\wpidea\templates_system\groups\Template_Group;
use bpmj\wpidea\templates_system\groups\Template_Groups_Repository;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\user\Interface_Current_User_Getter;
use Psr\Log\LoggerInterface;
use WP_Post;

class Page_Edit_Handler implements Interface_Event_Handler
{
    private const PROFILE_EDITOR_PAGE_OPTION_NAME = 'profile_editor_page';
    private const PAGE_TYPE_PRODUCT_OFFER_DESCRIPTION = 'product_offer_description';
    private const PAGE_TYPE_COURSE_PANEL_CONTENT = 'course_panel_content';
    private const PAGE_TYPE_COURSE_MODULE = 'course_module';
    private const PAGE_TYPE_COURSE_LESSON = 'course_lesson';
    private const PAGE_TYPE_COURSE_TEST = 'course_test';
    private const PAGE_TYPE_USER_ACCOUNT_PAGE = 'user_account_page';
    private const PAGE_TYPE_COURSES_PAGE = 'courses_page';
    private const PAGE_TYPE_OTHER = 'other';
    private Courses $courses;
    private Interface_Product_API $product_api;
    private Interface_Current_User_Getter $current_user_getter;
    private LoggerInterface $logger;
    private Interface_Translator $translator;
    private Interface_Actions $actions;
    private Interface_Readable_Course_Repository $course_repository;
    private ?Template_Group $active_template_group;
    private LMS_Settings $lms_settings;
    private Current_Request $current_request;


    public function __construct(
        Courses $courses,
        Interface_Product_API $product_api,
        Interface_Current_User_Getter $current_user_getter,
        LoggerInterface $logger,
        Interface_Translator $translator,
        Interface_Actions $actions,
        Interface_Readable_Course_Repository $course_repository,
        Template_Groups_Repository $template_groups_repository,
        LMS_Settings $lms_settings,
        Current_Request $current_request
    ) {
        $this->courses = $courses;
        $this->product_api = $product_api;
        $this->current_user_getter = $current_user_getter;
        $this->logger = $logger;
        $this->translator = $translator;
        $this->actions = $actions;
        $this->course_repository = $course_repository;
        $this->lms_settings = $lms_settings;
        $this->current_request = $current_request;
        $this->active_template_group = $template_groups_repository->find_active();
    }

    public function init(): void
    {
        $this->actions->add(Action_Name::POST_UPDATED, function (int $post_id, WP_Post $post_after, WP_Post $post_before) {
            $this->handle_log_page_updated($post_id, $post_after);
        }, 10, 3);
    }

    private function handle_log_page_updated(int $post_id, WP_Post $post_after): void
    {
        if(!$this->is_in_page_edit_mode()){
            return;
        }

        $page_type = $this->get_page_type($post_id, $post_after);

        if ($page_type === self::PAGE_TYPE_OTHER) {
            return;
        }

        $current_user_login = $this->get_current_user_login();
        $product_id = $this->get_product_id($page_type, $post_id);
        $course_id = $this->get_course_id($product_id, $post_id);

        $resource_type = $this->get_resource_type_for_product_id($product_id);
        $resource_type_name = $resource_type ? '.' . $resource_type : '';

        $this->logger->info(
            sprintf(
                $this->translator->translate('logs.log_message.page_updated.' . $page_type . $resource_type_name),
                $post_id,
                $course_id ?? $product_id,
                $current_user_login,
            )
        );
    }

    private function get_page_type(int $post_id, WP_Post $post): string
    {
        if ($post->post_type === 'download') {
            return self::PAGE_TYPE_PRODUCT_OFFER_DESCRIPTION;
        }

        if ($post->post_type === 'page') {
            if ($this->course_repository->is_course_panel_page($post_id)) {
                return self::PAGE_TYPE_COURSE_PANEL_CONTENT;
            }

            if ($this->course_repository->is_course_module_page($post_id)) {
                return self::PAGE_TYPE_COURSE_MODULE;
            }

            if ($this->course_repository->is_course_lesson_page($post_id)) {
                return self::PAGE_TYPE_COURSE_LESSON;
            }

            if ($this->course_repository->is_course_test_page($post_id)) {
                return self::PAGE_TYPE_COURSE_TEST;
            }

            if ($this->is_user_account_page($post_id)) {
                return self::PAGE_TYPE_USER_ACCOUNT_PAGE;
            }

            if ($this->is_courses_page($post_id)) {
                return self::PAGE_TYPE_COURSES_PAGE;
            }
        }

        return self::PAGE_TYPE_OTHER;
    }

    private function get_course_id(?int $product_id, int $post_id): ?int
    {
        $course_by_product = null;

        if ($product_id) {
            $course_by_product = $this->courses->get_course_by_product($product_id);
            $course_by_product = $course_by_product ? $course_by_product->ID : null;
        }

        $page_id = $this->courses->get_course_top_page($post_id);
        $course_by_page = $this->courses->get_course_by_page($page_id);
        $course_by_page = $course_by_page ? $course_by_page->ID : null;

        return $course_by_product ?? $course_by_page;
    }

    private function get_product_id(string $page_type, int $post_id): ?int
    {
        if ($page_type === self::PAGE_TYPE_PRODUCT_OFFER_DESCRIPTION) {
            return $post_id;
        }

        if ($page_type === self::PAGE_TYPE_COURSE_PANEL_CONTENT) {
            $course = $this->course_repository->find_by_page_id(new Page_ID($post_id));

            if (!$course) {
                return null;
            }

            return $course->get_product_id() ? $course->get_product_id()->to_int() : null;
        }

        return null;
    }

    private function get_current_user_login(): ?string
    {
        $current_user = $this->current_user_getter->get();

        return $current_user ? $current_user->get_login() : '';
    }

    private function get_resource_type_for_product_id(?int $product_id): string
    {
        $resource_type = $this->product_api->get_resource_type_for_product_id($product_id);

        return $resource_type ? $resource_type->get_name() : '';
    }

    private function is_user_account_page(int $post_id): bool
    {
        $legacy_option = (int)$this->lms_settings->get(self::PROFILE_EDITOR_PAGE_OPTION_NAME);

        if ($post_id === $legacy_option) {
            return true;
        }

        $user_account_page_id_option = $this->active_template_group->get_option(Template_Group_Settings::OPTION_USER_ACCOUNT_PAGE);
        if (empty($user_account_page_id_option) || !is_numeric($user_account_page_id_option)) {
            return false;
        }

        if ($post_id === (int)$user_account_page_id_option) {
            return true;
        }

        return false;
    }

    private function is_courses_page(int $post_id): bool
    {
        $courses_page_id = $this->active_template_group->get_option(Template_Group_Settings::OPTION_COURSES_PAGE);
        if (empty($courses_page_id) || !is_numeric($courses_page_id)) {
            return false;
        }

        if ($post_id === (int)$courses_page_id) {
            return true;
        }

        return false;
    }

    private function is_in_page_edit_mode(): bool
    {
        $uri = $this->current_request->get_request_uri();

        return (strpos($uri, 'wp-json') || strpos($uri, 'action=edit'));
    }

}