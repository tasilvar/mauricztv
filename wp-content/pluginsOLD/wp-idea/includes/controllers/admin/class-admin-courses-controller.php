<?php

namespace bpmj\wpidea\controllers\admin;

use bpmj\wpidea\admin\{Dashboard, Edit_Course};
use bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug;
use bpmj\wpidea\admin\pages\course_editor\Course_Editor_Page_Renderer;
use bpmj\wpidea\app\courses\duplicator\Course_Duplicator;
use bpmj\wpidea\Caps;
use bpmj\wpidea\controllers\Access_Control;
use bpmj\wpidea\controllers\Ajax_Controller;
use bpmj\wpidea\Courses;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\data_types\course\Course_Sales_Status;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\exceptions\No_Required_Variables_Exception;
use bpmj\wpidea\Interface_Redirector;
use bpmj\wpidea\learning\course\Course_ID;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\Request_Method;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\sales\product\core\event\Event_Name as Product_Event_Name;
use bpmj\wpidea\settings\Interface_Settings;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\user\Interface_Current_User_Getter;
use bpmj\wpidea\user\Interface_User_Permissions_Service;
use bpmj\wpidea\user\User_Capability_Factory;

class  Admin_Courses_Controller extends Ajax_Controller
{

    private Dashboard $dashboard;
    private Courses $courses;
    private Interface_Events $events;
    private Interface_Current_User_Getter $current_user_getter;
    private User_Capability_Factory $capability_factory;
    private Interface_User_Permissions_Service $user_permissions_service;
    private Interface_Settings $settings;
    private Interface_Url_Generator $url_generator;
    private Course_Duplicator $course_duplicator;

    public function __construct(
        Access_Control $access_control,
        Interface_Translator $translator,
        Interface_Redirector $redirector,
        Interface_Current_User_Getter $current_user_getter,
        User_Capability_Factory $capability_factory,
        Interface_User_Permissions_Service $user_permissions_service,
        Interface_Settings $settings,
        Interface_Url_Generator $url_generator,
        Course_Duplicator $course_duplicator,
        Interface_Events $events,
        Courses $courses,
        Dashboard $dashboard
    ) {
        $this->current_user_getter = $current_user_getter;
        $this->capability_factory = $capability_factory;
        $this->user_permissions_service = $user_permissions_service;
        $this->settings = $settings;
        $this->url_generator = $url_generator;
        $this->course_duplicator = $course_duplicator;
        $this->events = $events;
        $this->courses = $courses;
        $this->dashboard = $dashboard;

        parent::__construct($access_control, $translator, $redirector);
    }

    public function behaviors(): array
    {
        return [
            'roles' => Caps::ROLES_ADMINS_SUPPORT,
            'caps' => [Caps::CAP_MANAGE_PRODUCTS],
            'allowed_methods' => [Request_Method::POST],
        ];
    }

    public function get_popup_create_course_action(Current_Request $current_request): string
    {
        return $this->success(
            [
                'content' => $this->admin_view('/popup/create-product-popup-content', [
                    'title' => $this->translator->translate('course_list.actions.create_course'),
                    'form_id' => 'courses_popup_editor',
                    'create_product_url' => $this->get_create_course_url(),
                    'translator' => $this->translator
                ])
            ]
        );
    }

    public function course_stats_action(Current_Request $current_request): string
    {
        $id = $current_request->get_query_arg('id');

        if (!$id) {
            throw new No_Required_Variables_Exception($this->translator);
        }

        return $this->success(
            [
                'content' => $this->admin_view('/pages/course-list/course-stats-popup-content', [
                    'title' => $this->translator->translate('course_list.popup.course_stats.title'),
                    'content' => Edit_Course::get_show_stats_popup_html($id)
                ])
            ]
        );
    }

    public function course_students_action(Current_Request $current_request): string
    {
        $id = $current_request->get_query_arg('id');

        if (!$id) {
            throw new No_Required_Variables_Exception($this->translator);
        }

        $user = $this->current_user_getter->get();
        $can_view_sensitive_data = $this->user_permissions_service->has_capability(
            $user,
            $this->capability_factory->create_from_name(Caps::CAP_VIEW_SENSITIVE_DATA)
        );
        $courses = $this->dashboard->get_courses(true);
        $course = array_filter($courses, static function ($course) use ($id) {
            return (int)$course['id'] === (int)$id;
        });
        $course_item = reset($course);

        $disable_auto_stats = $this->settings->get('disable_auto_stats');

        return $this->success(
            [
                'content' => $this->admin_view('/pages/course-list/participants-popup-content', [
                    'can_view_sensitive_data' => $can_view_sensitive_data,
                    'course' => $course_item,
                    'disable_auto_stats' => $disable_auto_stats,
                    'title' => $this->translator->translate('admin_courses.participants')
                ])
            ]
        );
    }

    public function purchase_links_action(Current_Request $current_request): string
    {
        $id = $current_request->get_query_arg('id');

        if (!$id) {
            throw new No_Required_Variables_Exception($this->translator);
        }

        $product_id = $this->courses->get_product_by_course($id);

        return $this->success(
            [
                'content' => $this->admin_view('/pages/course-list/purchase-links-popup-content', [
                    'title' => $this->translator->translate('course_list.popup.purchase_links.title'),
                    'content' => Edit_Course::get_add_to_cart_popup_html($product_id)
                ])
            ]
        );
    }

    public function duplicate_course_action(Current_Request $current_request): string
    {
        $id = $current_request->get_query_arg('id');

        if (!$id) {
            throw new No_Required_Variables_Exception($this->translator);
        }

        $course_id = $this->course_duplicator->duplicate_course(new Course_ID((int)$id));

        if(!$course_id) {
            return $this->fail();
        }

        return $this->success(['data' => [
            'action' => 'redirect',
            'url' => $this->url_generator->generate_admin_page_url('admin.php', [
                'page' => Admin_Menu_Item_Slug::EDITOR_COURSE,
                Course_Editor_Page_Renderer::COURSE_ID_QUERY_ARG_NAME => $course_id->to_int()
            ])
        ]]);
    }

    public function delete_course_action(Current_Request $current_request): string
    {
        $id = $current_request->get_query_arg('id');

        if (!$id) {
            throw new No_Required_Variables_Exception($this->translator);
        }

        $product_id = $this->courses->get_product_by_course($id);

        if (!$this->courses->delete_course($id)) {
            return $this->fail($this->translator->translate('admin_courses.cant_remove_bundled_course'));
        }

        $this->events->emit(Product_Event_Name::PRODUCT_DELETED, $product_id);

        return $this->success();
    }

    public function delete_course_bulk_action(Current_Request $current_request): string
    {
        $request_body = $current_request->get_decoded_raw_post_data();
        $ids = $request_body['ids'] ?? [];
        $any_course_skipped_because_in_bundle = false;

        foreach ($ids as $id) {
            $product_id = $this->courses->get_product_by_course($id);

            if (!$this->courses->delete_course($id)) {
                $any_course_skipped_because_in_bundle = true;

                continue;
            }

            $this->events->emit(Product_Event_Name::PRODUCT_DELETED, $product_id);
        }

        if($any_course_skipped_because_in_bundle) {
            return $this->success([
                'message' => $this->translator->translate('admin_courses.cant_remove_bundled_course.bulk')
            ]);
        }

        return $this->success();
    }

    public function disable_sales_action(Current_Request $current_request): string
    {
        $id = $current_request->get_query_arg('id');
        $value = $current_request->get_query_arg('value');

        if (!$id) {
            throw new No_Required_Variables_Exception($this->translator);
        }

        $course_sales_status = new Course_Sales_Status($value);

        if (!$this->courses->disable_course_sales(new Course_ID((int)$id), $course_sales_status->get_value())) {
            return $this->fail();
        }

        return $this->success();
    }

    public function disable_sales_bulk_action(Current_Request $current_request): string
    {
        $request_body = $current_request->get_decoded_raw_post_data();
        $ids = $request_body['ids'] ?? [];

        foreach ($ids as $id) {
            $course_sales_status = $this->get_new_sales_status_by_course_id($id);

            if (!$this->courses->disable_course_sales(new Course_ID((int)$id), $course_sales_status)) {
                return $this->fail();
            }
        }

        return $this->success();
    }

    private function get_new_sales_status_by_course_id(int $id): string
    {
        $sales_disabled = get_post_meta($id, 'sales_disabled', true);

        if ($sales_disabled === Course_Sales_Status::ENABLED) {
            $sales_disabled = Course_Sales_Status::DISABLED;
        } else {
            $sales_disabled = Course_Sales_Status::ENABLED;
        }

        return $sales_disabled;
    }

    private function get_create_course_url(): string
    {
        return $this->url_generator->generate(Admin_Creator_Controller::class, 'create_course', [
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create()
        ]);
    }

}
