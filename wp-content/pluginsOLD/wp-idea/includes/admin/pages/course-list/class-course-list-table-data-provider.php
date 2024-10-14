<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\course_list;

use bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug;
use bpmj\wpidea\admin\pages\course_editor\Course_Editor_Page_Renderer;
use bpmj\wpidea\admin\tables\dynamic\data\Dynamic_Table_Data_Usage_Context;
use bpmj\wpidea\admin\tables\dynamic\data\Interface_Dynamic_Table_Data_Provider;
use bpmj\wpidea\controllers\admin\Admin_Courses_Controller;
use bpmj\wpidea\data_types\course\Course_Sales_Status;
use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\learning\course\content\Course_Content_ID;
use bpmj\wpidea\learning\course\Course;
use bpmj\wpidea\learning\course\Interface_Readable_Course_Repository;
use bpmj\wpidea\learning\services\Interface_Url_Resolver;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\translator\Interface_Translator;

class Course_List_Table_Data_Provider implements Interface_Dynamic_Table_Data_Provider
{
    private const NO_SALES_LIMIT = '-';

    private Interface_Url_Generator $url_generator;
    private Interface_Translator $translator;

    private Interface_Readable_Course_Repository $course_repository;
    private Interface_Url_Resolver $url_resolver;

    public function __construct(
        Interface_Url_Generator $url_generator,
        Interface_Translator $translator,
        Interface_Readable_Course_Repository $course_repository,
        Interface_Url_Resolver $url_resolver
    ) {
        $this->url_generator = $url_generator;
        $this->translator = $translator;
        $this->course_repository = $course_repository;
        $this->url_resolver = $url_resolver;
    }

    public function get_rows(
        array $filters,
        Sort_By_Clause $sort_by,
        int $per_page,
        int $page,
        Dynamic_Table_Data_Usage_Context $context
    ): array {
        $entities = $this->course_repository->find_all();
        $rows = [];

        foreach ($entities as $entity) {
            $course_sales_status = $this->get_course_toggled_sales_status($entity);

            $rows[] = [
                'id' => $entity->get_id()->to_int(),
                'edit_url' => $this->get_edit_course_url($entity),
                'name' => $entity->get_title(),
                'sales' => $course_sales_status,
                'sales_label' => $this->get_name_course_sales_status_label($course_sales_status),
                'sales_limit_status' => $this->get_sales_limit_status($entity),
                'edit_course' => $this->get_edit_course_url($entity),
                'duplicate_course' => $this->get_duplicate_course_url($entity),
                'delete_course' => $this->get_delete_course_url($entity),
                'change_course_sales' => $this->get_change_course_sales_status_url($entity),
                'course_panel' => $this->get_course_panel_url($entity),
                'course_stats' => $this->get_course_stats_url($entity),
                'course_students' => $this->get_course_students_url($entity),
                'purchase_links' => $this->get_purchase_links_url($entity),
                'expiring_customers' => $this->get_expiring_customers_url($entity),
                'disabled_expiring_customers' => $this->get_disabled_expiring_customers($entity, $course_sales_status),
            ];
        }

        return $rows;
    }

    public function get_total(array $filters): int
    {
        return 0;
    }

    public function get_name_course_sales_status_label(string $status): string
    {
        $course_status = new Course_Sales_Status($status);

        return $this->translator->translate('course_list.sales.status.' . $course_status->get_name());
    }

    private function get_edit_course_url(Course $entity): string
    {
        return $this->url_generator->generate_admin_page_url('admin.php', [
            'page' => Admin_Menu_Item_Slug::EDITOR_COURSE,
            Course_Editor_Page_Renderer::COURSE_ID_QUERY_ARG_NAME => $entity->get_id()->to_int()
        ]);
    }

    private function get_duplicate_course_url(Course $entity): string
    {
        return $this->url_generator->generate(Admin_Courses_Controller::class, 'duplicate_course', [
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create(),
            'id' => $entity->get_id()->to_int()
        ]);
    }

    private function get_course_panel_url(Course $entity): string
    {
        $url = $this->url_resolver->get_by_course_content_id(new Course_Content_ID($entity->get_id()->to_int()));
        if (null === $url) {
            return '';
        }
        return $url->get_value() ?? '';
    }

    private function get_course_stats_url(Course $entity): string
    {
        return $this->url_generator->generate(Admin_Courses_Controller::class, 'course_stats', [
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create(),
            'id' => $entity->get_id()->to_int()
        ]);
    }

    private function get_course_students_url(Course $entity): string
    {
        return $this->url_generator->generate(Admin_Courses_Controller::class, 'course_students', [
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create(),
            'id' => $entity->get_id()->to_int()
        ]);
    }

    private function get_purchase_links_url(Course $entity): string
    {
        return $this->url_generator->generate(Admin_Courses_Controller::class, 'purchase_links', [
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create(),
            'id' => $entity->get_id()->to_int()
        ]);
    }

    private function get_expiring_customers_url(Course $entity): string
    {
        return $this->url_generator->generate_admin_page_url('admin.php', [
            'page' => Admin_Menu_Item_Slug::EXPIRING_CUSTOMERS,
            'filter_course' => $entity->get_id()->to_int()
        ]);
    }

    private function get_disabled_expiring_customers(Course $entity, string $course_sales_status): bool
    {
        return !WPI()->dashboard->course_have_access_time([
            'id' => $entity->get_id()->to_int(),
            'status' => $course_sales_status
        ]);
    }

    private function get_delete_course_url(Course $entity): string
    {
        return $this->url_generator->generate(Admin_Courses_Controller::class, 'delete_course', [
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create(),
            'id' => $entity->get_id()->to_int()
        ]);
    }

    private function get_change_course_sales_status_url(Course $entity): string
    {
        return $this->url_generator->generate(Admin_Courses_Controller::class, 'disable_sales', [
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create(),
            'id' => $entity->get_id()->to_int(),
            'value' => $this->get_course_toggled_sales_status($entity)
        ]);
    }

    private function get_course_toggled_sales_status(Course $entity): string
    {
        $sales_disabled = get_post_meta($entity->get_id()->to_int(), 'sales_disabled', true);

        if ($sales_disabled === Course_Sales_Status::ENABLED) {
            $sales_disabled = Course_Sales_Status::DISABLED;
        } else {
            $sales_disabled = Course_Sales_Status::ENABLED;
        }

        return $sales_disabled;
    }

    private function get_sales_limit_status(Course $entity): string
    {
        $product_id = $entity->get_product_id();

        if (!$product_id) {
            return self::NO_SALES_LIMIT;
        }

        $purchase_limit = (int)get_post_meta($product_id->to_int(), '_bpmj_eddcm_purchase_limit', true);
        $purchase_limit_items_left = (int)get_post_meta($product_id->to_int(), '_bpmj_eddcm_purchase_limit_items_left', true);
        $purchase_limit_unlimited = '1' === get_post_meta($product_id->to_int(), '_bpmj_eddcm_purchase_limit_unlimited', true);

        if ($purchase_limit <= 0) {
            return self::NO_SALES_LIMIT;
        }

        return sprintf(
            $this->translator->translate('course_list.sales_limit_status.available'),
            $purchase_limit_items_left . '/' . $purchase_limit . ($purchase_limit_unlimited ? ' (&infin;)' : '')
        );
    }
}