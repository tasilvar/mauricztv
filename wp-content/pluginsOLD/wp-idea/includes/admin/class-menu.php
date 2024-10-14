<?php

// Exit if accessed directly
namespace bpmj\wpidea\admin;

if (!defined('ABSPATH')) {
    exit;
}

use bpmj\wpidea\admin\pages\quiz_editor\Quiz_Editor_Page_Renderer;
use bpmj\wpidea\admin\affiliate_program\Affiliate_Program_Partners_View;
use bpmj\wpidea\admin\affiliate_program\Affiliate_Program_View;
use bpmj\wpidea\admin\discounts\Discounts_View;
use bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug;
use bpmj\wpidea\admin\menu\Site_Admin_Menu_Mode_Toggle;
use bpmj\wpidea\admin\pages\affiliate_program_redirections\Affiliate_Program_Redirections_Page_Renderer;
use bpmj\wpidea\admin\pages\bundle_editor\Bundle_Editor_Page_Renderer;
use bpmj\wpidea\admin\pages\bundle_list\Bundles_List_Page_Renderer;
use bpmj\wpidea\admin\pages\certificates\Certificate_Pages_Renderer;
use bpmj\wpidea\admin\pages\course_editor\Course_Editor_Page_Renderer;
use bpmj\wpidea\admin\pages\course_list\Course_List_Page_Renderer;
use bpmj\wpidea\admin\pages\customers\Customers;
use bpmj\wpidea\admin\pages\customers\Customers_Page_Renderer;
use bpmj\wpidea\admin\pages\digital_product_editor\Digital_Product_Editor_Page_Renderer;
use bpmj\wpidea\admin\pages\digital_products_list\Digital_Products_List_Page_Renderer;
use bpmj\wpidea\admin\pages\expiring_customers\Expiring_Customers_Page_Renderer;
use bpmj\wpidea\admin\pages\increasing_sales\Increasing_Sales_Page_Renderer;
use bpmj\wpidea\admin\pages\logs\Logs_Page_Renderer;
use bpmj\wpidea\admin\pages\notifications\Notifications_Page_Renderer;
use bpmj\wpidea\admin\pages\opinions\Opinions_Page_Renderer;
use bpmj\wpidea\admin\pages\payments_history\Payments_Page_Renderer;
use bpmj\wpidea\admin\pages\physical_product_editor\Physical_Product_Editor_Page_Renderer;
use bpmj\wpidea\admin\pages\physical_products\Physical_Products_Page_Renderer;
use bpmj\wpidea\admin\pages\price_history\Price_History_Page_Renderer;
use bpmj\wpidea\admin\pages\quizzes\Quizzes_Page_Renderer;
use bpmj\wpidea\admin\pages\service_creator\Service_Creator_Page_Renderer;
use bpmj\wpidea\admin\pages\service_editor\Service_Editor_Page_Renderer;
use bpmj\wpidea\admin\pages\services\Services_Page_Renderer;
use bpmj\wpidea\admin\pages\settings\Settings_Page_Renderer;
use bpmj\wpidea\admin\pages\students\Student_Page_Renderer;
use bpmj\wpidea\admin\pages\users\User_Page_Renderer;
use bpmj\wpidea\admin\pages\video_settings\Video_Settings_Page_Renderer;
use bpmj\wpidea\admin\pages\video_uploader\Video_Uploader_Page_Renderer;
use bpmj\wpidea\admin\pages\webhooks\Webhooks_Page_Renderer;
use bpmj\wpidea\admin\purchase_redirections\Purchase_Redirections_View;
use bpmj\wpidea\admin\renderers\Interface_Page_Renderer;
use bpmj\wpidea\admin\reports\Reports_View;
use bpmj\wpidea\admin\settings\Settings_Const;
use bpmj\wpidea\admin\subscription\models\Subscription;
use bpmj\wpidea\admin\subscription\models\Subscription_Const;
use bpmj\wpidea\admin\video\Videos_View;
use bpmj\wpidea\Caps;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\Helper;
use bpmj\wpidea\learning\quiz\Cached_Not_Rated_Quizzes_Counter;
use bpmj\wpidea\modules\opinions\api\Opinions_API;
use bpmj\wpidea\modules\videos\core\services\Bunny_Net_Video_List_Sync_Service;
use bpmj\wpidea\modules\videos\core\services\Vimeo_Videos_Locator_Service;
use bpmj\wpidea\modules\videos\Videos_Module;
use bpmj\wpidea\Packages;
use bpmj\wpidea\packages\Interface_Packages_API;
use bpmj\wpidea\settings\Interface_Settings;
use bpmj\wpidea\Software_Variant;
use bpmj\wpidea\templates_system\admin\modules\Interface_Templates_System_Modules_Factory;
use bpmj\wpidea\templates_system\admin\modules\settings_handlers\Interface_Templates_Settings_Handler;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\user\Interface_Current_User_Getter;
use bpmj\wpidea\user\Interface_User_Permissions_Service;
use bpmj\wpidea\user\User_Capability_Factory;
use bpmj\wpidea\view\Admin_View_Names;
use bpmj\wpidea\view\Interface_View_Provider;
use bpmj\wpidea\wolverine\user\User;
use function bpmj_eddcm_enable_edd;

class Menu
{
    private Quiz_Editor_Page_Renderer $quiz_editor_page_renderer;
    private Increasing_Sales_Page_Renderer $increasing_sales_page_renderer;

    private Digital_Products_List_Page_Renderer $digital_products_list_page_renderer;

    protected Certificate_Pages_Renderer $certificate_pages_renderer;

    private Interface_Templates_Settings_Handler $templates_settings_renderer;

    private Logs_Page_Renderer $logs_page_renderer;

    private Webhooks_Page_Renderer $webhooks_page_renderer;

    private Payments_Page_Renderer $payments_page_renderer;

    private Customers_Page_Renderer $customers_page_renderer;

    private Student_Page_Renderer $students_page_renderer;

    private Quizzes_Page_Renderer $quizzes_page_renderer;

    private Interface_Translator $translator;

    private Site_Admin_Menu_Mode_Toggle $admin_menu_mode_toggle;

    private Interface_Page_Renderer $digital_product_creator_page_renderer;

    private Interface_Page_Renderer $bundle_creator_page_renderer;

    private Interface_Page_Renderer $services_page_renderer;

    private Interface_Settings $settings;
    private Interface_View_Provider $view_provider;

    private Subscription $subscription;

    private Cached_Not_Rated_Quizzes_Counter $not_rated_quizzes_counter;

    private Interface_Current_User_Getter $current_user_getter;

    private Interface_User_Permissions_Service $user_permissions_service;

    private User_Capability_Factory $capability_factory;

    private Discounts_View $discounts_view;
    private Purchase_Redirections_View $purchase_redirections_view;
    private Service_Creator_Page_Renderer $service_creator_page_renderer;

    private Affiliate_Program_View $affiliate_program_view;
    private Affiliate_Program_Partners_View $affiliate_program_partners_view;
    private Video_Uploader_Page_Renderer $video_uploader_page_renderer;

    private Videos_View $videos_view;
    private Video_Settings_Page_Renderer $video_settings_page_renderer;

    private Vimeo_Videos_Locator_Service $locator_service;
    private Bunny_Net_Video_List_Sync_Service $bunny_sync_service;
    private Current_Request $current_request;
    private Bundles_List_Page_Renderer $bundles_list_page_renderer;
    private Videos_Module $videos_module;
    private Notifications_Page_Renderer $notifications_page_renderer;
    private Settings_Page_Renderer $settings_page_renderer;
    private Service_Editor_Page_Renderer $service_editor_page_renderer;
    private Affiliate_Program_Redirections_Page_Renderer $affiliate_program_redirections_page_renderer;
    private Course_List_Page_Renderer $course_list_page_renderer;
    private User_Page_Renderer $user_page_renderer;
    private Expiring_Customers_Page_Renderer $expiring_customers_page_renderer;
    private Digital_Product_Editor_Page_Renderer $digital_product_editor_page_renderer;
    private Course_Editor_Page_Renderer $course_editor_page_renderer;
    private Bundle_Editor_Page_Renderer $bundle_editor_page_renderer;
    private Price_History_Page_Renderer $price_history_page_renderer;
    private Physical_Products_Page_Renderer $physical_products_page_renderer;
    private Physical_Product_Editor_Page_Renderer $physical_product_editor_page_renderer;
    private Interface_Packages_API $packages_api;
    private Opinions_Page_Renderer $opinions_page_renderer;
    private Opinions_API $opinions_api;

    public function __construct(
        Quiz_Editor_Page_Renderer $quiz_editor_page_renderer,
        Increasing_Sales_Page_Renderer $increasing_sales_page_renderer,
        Certificate_Pages_Renderer $certificate_pages_renderer,
        Interface_Templates_System_Modules_Factory $templates_system_modules_factory,
        Logs_Page_Renderer $logs_page_renderer,
        Webhooks_Page_Renderer $webhooks_page_renderer,
        Payments_Page_Renderer $payments_page_renderer,
        Customers_Page_Renderer $customers_page_renderer,
        Quizzes_Page_Renderer $quizzes_page_renderer,
        Interface_Translator $translator,
        Student_Page_Renderer $students_page_renderer,
        Site_Admin_Menu_Mode_Toggle $admin_menu_mode_toggle,
        Subscription $subscription,
        Interface_Page_Renderer $digital_product_creator_page_renderer,
        Interface_Page_Renderer $bundle_creator_page_renderer,
        Interface_Settings $settings,
        Interface_View_Provider $view_provider,
        Cached_Not_Rated_Quizzes_Counter $not_rated_quizzes_counter,
        Interface_Current_User_Getter $current_user_getter,
        Interface_User_Permissions_Service $user_permissions_service,
        User_Capability_Factory $capability_factory,
        Affiliate_Program_View $affiliate_program_view,
        Affiliate_Program_Partners_View $affiliate_program_partners_view,
        Discounts_View $discounts_view,
        Purchase_Redirections_View $purchase_redirections_view,
        Services_Page_Renderer $services_page_renderer,
        Service_Creator_Page_Renderer $service_creator_page_renderer,
        Vimeo_Videos_Locator_Service $locator_service,
        Videos_View $videos_view,
        Video_Uploader_Page_Renderer $video_uploader_page_renderer,
        Bunny_Net_Video_List_Sync_Service $bunny_sync_service,
        Current_Request $current_request,
        Videos_Module $videos_module,
        Video_Settings_Page_Renderer $video_settings_page_renderer,
        Bundles_List_Page_Renderer $bundles_list_page_renderer,
        Notifications_Page_Renderer $notifications_page_renderer,
        Settings_Page_Renderer $settings_page_renderer,
        Affiliate_Program_Redirections_Page_Renderer $affiliate_program_redirections_page_renderer,
        Course_List_Page_Renderer $course_list_page_renderer,
        Digital_Products_List_Page_Renderer $digital_products_list_page_renderer,
        User_Page_Renderer $user_page_renderer,
        Expiring_Customers_Page_Renderer $expiring_customers_page_renderer,
        Service_Editor_Page_Renderer $service_editor_page_renderer,
        Digital_Product_Editor_Page_Renderer $digital_product_editor_page_renderer,
        Course_Editor_Page_Renderer $course_editor_page_renderer,
        Bundle_Editor_Page_Renderer $bundle_editor_page_renderer,
        Price_History_Page_Renderer $price_history_page_renderer,
        Physical_Products_Page_Renderer $physical_products_page_renderer,
        Physical_Product_Editor_Page_Renderer $physical_product_editor_page_renderer,
        Interface_Packages_API $packages_api,
        Opinions_Page_Renderer $opinions_page_renderer,
        Opinions_API $opinions_api
    ) {
        $this->quiz_editor_page_renderer = $quiz_editor_page_renderer;
        $this->increasing_sales_page_renderer = $increasing_sales_page_renderer;
        $this->certificate_pages_renderer = $certificate_pages_renderer;
        $this->templates_settings_renderer = $templates_system_modules_factory->get_settings_handler();
        $this->logs_page_renderer = $logs_page_renderer;
        $this->webhooks_page_renderer = $webhooks_page_renderer;
        $this->payments_page_renderer = $payments_page_renderer;
        $this->customers_page_renderer = $customers_page_renderer;
        $this->translator = $translator;
        $this->students_page_renderer = $students_page_renderer;
        $this->quizzes_page_renderer = $quizzes_page_renderer;
        $this->admin_menu_mode_toggle = $admin_menu_mode_toggle;
        $this->subscription = $subscription;
        $this->digital_product_creator_page_renderer = $digital_product_creator_page_renderer;
        $this->bundle_creator_page_renderer = $bundle_creator_page_renderer;
        $this->settings = $settings;
        $this->view_provider = $view_provider;
        $this->not_rated_quizzes_counter = $not_rated_quizzes_counter;
        $this->current_user_getter = $current_user_getter;
        $this->user_permissions_service = $user_permissions_service;
        $this->capability_factory = $capability_factory;
        $this->discounts_view = $discounts_view;
        $this->purchase_redirections_view = $purchase_redirections_view;
        $this->services_page_renderer = $services_page_renderer;
        $this->service_creator_page_renderer = $service_creator_page_renderer;
        $this->affiliate_program_view = $affiliate_program_view;
        $this->affiliate_program_partners_view = $affiliate_program_partners_view;
        $this->locator_service = $locator_service;
        $this->videos_view = $videos_view;
        $this->video_uploader_page_renderer = $video_uploader_page_renderer;
        $this->bunny_sync_service = $bunny_sync_service;
        $this->current_request = $current_request;
        $this->videos_module = $videos_module;
        $this->video_settings_page_renderer = $video_settings_page_renderer;
        $this->bundles_list_page_renderer = $bundles_list_page_renderer;
        $this->notifications_page_renderer = $notifications_page_renderer;
        $this->settings_page_renderer = $settings_page_renderer;
        $this->affiliate_program_redirections_page_renderer = $affiliate_program_redirections_page_renderer;
        $this->course_list_page_renderer = $course_list_page_renderer;
        $this->digital_products_list_page_renderer = $digital_products_list_page_renderer;
        $this->user_page_renderer = $user_page_renderer;
        $this->expiring_customers_page_renderer = $expiring_customers_page_renderer;
        $this->service_editor_page_renderer = $service_editor_page_renderer;
        $this->digital_product_editor_page_renderer = $digital_product_editor_page_renderer;
        $this->course_editor_page_renderer = $course_editor_page_renderer;
        $this->bundle_editor_page_renderer = $bundle_editor_page_renderer;
        $this->price_history_page_renderer = $price_history_page_renderer;
        $this->physical_products_page_renderer = $physical_products_page_renderer;
        $this->physical_product_editor_page_renderer = $physical_product_editor_page_renderer;
        $this->packages_api = $packages_api;
        $this->opinions_page_renderer = $opinions_page_renderer;
        $this->opinions_api = $opinions_api;

        $this->init();
    }

    // Class initize
    public function init()
    {
        add_action('admin_menu', [$this, 'admin_menu'], 9);
        add_action('admin_menu', [$this, 'admin_menu_low_priority_items'], 100);
    }

    /**
     * @return string
     */
    public function get_first_time_message()
    {
        return __('To start, please enter the license key for WP Idea!', BPMJ_EDDCM_DOMAIN);
    }

    /**
     * Add EDD Courses Manager to
     * WordPress Admin Menu
     */
    public function admin_menu()
    {
        global $submenu;

        $vkey = get_option('bmpj_wpidea_vkey');

        $software_name = Software_Variant::get_name();

        $caps_allowing_see_course_menu = [Caps::CAP_MANAGE_PRODUCTS, Caps::CAP_MANAGE_SETTINGS];

        $courses_functionality_enabled = $this->settings->get(Settings_Const::COURSES_ENABLED) ?? true;

        add_submenu_page(
            null,
            'vimeo videos',
            'wpi-vimeo-video',
            Caps::ROLE_SITE_ADMIN,
            'wpi-vimeo-video',
            function () {
                if ($this->current_request->get_query_arg('wpi_run_sync') === 'true') {
                    $this->bunny_sync_service->sync();
                    echo "Synchronizacja zakonczona.";
                }
                if ($this->current_request->get_query_arg('wpi_run_replace') === 'true') {
                    $this->locator_service->replace_vimeo_in_all_posts();
                    echo "Podmiana zakonczona.";
                }
                $this->locator_service->render();
            }
        );

        if (empty($vkey)) {
            // Main menu
            add_menu_page(
                $this->get_first_time_message(), __($software_name, BPMJ_EDDCM_DOMAIN), Caps::CAP_MANAGE_SETTINGS,
                Admin_Menu_Item_Slug::SETTINGS, [$this->settings_page_renderer, 'render_page'], 'dashicons-lightbulb', 25
            );
        } else {
            $toggle_button_options = $this->admin_menu_mode_toggle->get_toggle_button_add_menu_page_options();
            call_user_func_array('add_menu_page', $toggle_button_options);

            add_menu_page(
                __($software_name, BPMJ_EDDCM_DOMAIN), __($software_name, BPMJ_EDDCM_DOMAIN),
                User::oneOfCurrentUserCapsIs($caps_allowing_see_course_menu), Admin_Menu_Item_Slug::WP_IDEA,
                [$this, 'course_list'], 'dashicons-lightbulb', 25
            );

            if (User::currentUserHasAnyOfTheCapabilities($caps_allowing_see_course_menu)) {


                if ($courses_functionality_enabled) {
                    // Submenu Dashboard
                    add_submenu_page(
                        Admin_Menu_Item_Slug::WP_IDEA, __('All Courses', BPMJ_EDDCM_DOMAIN),
                        __('All Courses', BPMJ_EDDCM_DOMAIN),
                        User::oneOfCurrentUserCapsIs($caps_allowing_see_course_menu),
                        Admin_Menu_Item_Slug::COURSES, [$this, 'course_list']
                    );

                    add_submenu_page(
                        Admin_Menu_Item_Slug::WP_IDEA,
                        $this->translator->translate('students.menu_title'),
                        $this->translator->translate('students.menu_title'),
                        Caps::CAP_MANAGE_STUDENTS,
                        Admin_Menu_Item_Slug::STUDENTS,
                        [$this->students_page_renderer, 'render_page']
                    );

                    add_submenu_page(Admin_Menu_Item_Slug::WP_IDEA,
                        $this->translator->translate('course_editor.page_title'),
                        $this->translator->translate('course_editor.page_title'),
                        User::oneOfCurrentUserCapsIs($caps_allowing_see_course_menu),
                        Admin_Menu_Item_Slug::EDITOR_COURSE, [$this->course_editor_page_renderer, 'render_page']
                    );

                    add_submenu_page(Admin_Menu_Item_Slug::WP_IDEA,
                        $this->translator->translate('quiz_editor.page_title'),
                        $this->translator->translate('quiz_editor.page_title'),
                        User::oneOfCurrentUserCapsIs($caps_allowing_see_course_menu),
                        Admin_Menu_Item_Slug::EDITOR_QUIZ, [$this->quiz_editor_page_renderer, 'render_page']
                    );

                }

                if ($this->settings->get(Settings_Const::DIGITAL_PRODUCTS_ENABLED)) {
                    // All digital products
                    add_submenu_page(
                        Admin_Menu_Item_Slug::WP_IDEA, __('Digital products', BPMJ_EDDCM_DOMAIN),
                        __('Digital products', BPMJ_EDDCM_DOMAIN),
                        User::oneOfCurrentUserCapsIs($caps_allowing_see_course_menu),
                        Admin_Menu_Item_Slug::DIGITAL_PRODUCTS, [$this, 'digital_products_list']
                    );

                    add_submenu_page(Admin_Menu_Item_Slug::DIGITAL_PRODUCTS,
                        $this->translator->translate('digital_product_editor.page_title'),
                        $this->translator->translate('digital_product_editor.page_title'),
                        User::oneOfCurrentUserCapsIs($caps_allowing_see_course_menu),
                        Admin_Menu_Item_Slug::EDITOR_DIGITAL_PRODUCT, [$this->digital_product_editor_page_renderer, 'render_page']
                    );

                }

                if ($this->settings->get(Settings_Const::SERVICES_ENABLED)) {
                    add_submenu_page(Admin_Menu_Item_Slug::WP_IDEA,
                        $this->translator->translate('services.services'),
                        $this->translator->translate('services.services'),
                        User::oneOfCurrentUserCapsIs($caps_allowing_see_course_menu),
                        Admin_Menu_Item_Slug::SERVICES,
                        [$this->services_page_renderer, 'render_page']
                    );

                    add_submenu_page(Admin_Menu_Item_Slug::SERVICES,
                        $this->translator->translate('service_editor.page_title'),
                        $this->translator->translate('service_editor.page_title'),
                        User::oneOfCurrentUserCapsIs($caps_allowing_see_course_menu),
                        Admin_Menu_Item_Slug::EDITOR_SERVICE, [$this->service_editor_page_renderer, 'render_page']
                    );

                }
            }

                    add_submenu_page(Admin_Menu_Item_Slug::WP_IDEA,
                        $this->translator->translate('bundles_list.page_title'),
                        $this->translator->translate('bundles_list.page_title'),
                        User::oneOfCurrentUserCapsIs($caps_allowing_see_course_menu),
                        Admin_Menu_Item_Slug::PACKAGES, [$this->bundles_list_page_renderer, 'render_page']
                    );

                   add_submenu_page(Admin_Menu_Item_Slug::PACKAGES,
                       $this->translator->translate('bundle_editor.page_title'),
                       $this->translator->translate('bundle_editor.page_title'),
                       User::oneOfCurrentUserCapsIs($caps_allowing_see_course_menu),
                       Admin_Menu_Item_Slug::EDITOR_PACKAGES, [$this->bundle_editor_page_renderer, 'render_page']
                   );

            // EDD - PC
            add_submenu_page(null, __('Add reminder', 'edd-paid-content'), __('Add reminder', 'edd-paid-content'),
                'manage_shop_settings', 'wp-idea-add-renewal', [$this, 'add_renewal']);
            add_submenu_page(null, __('Edit reminder', 'edd-paid-content'), __('Edit reminder', 'edd-paid-content'),
                'manage_shop_settings', 'wp-idea-edit-renewal', [$this, 'edit_renewal']);

            $certificate_pages_renderer = $this->certificate_pages_renderer;

            if (User::currentUserHasAnyOfTheCapabilities($caps_allowing_see_course_menu)) {
                foreach ($submenu['edit.php?post_type=download'] as $sub) {
                    if ($sub[1] == 'manage_product_terms') {
                        $submenu[Admin_Menu_Item_Slug::WP_IDEA][] = $sub;
                    }
                }
            }


            add_submenu_page(null, __('Add certificate', BPMJ_EDDCM_DOMAIN), __('Add certificate', BPMJ_EDDCM_DOMAIN),
                Caps::CAP_MANAGE_SETTINGS, 'wp-idea-add-certificate-template',
                [$certificate_pages_renderer, 'add_certificate_template']);
            add_submenu_page(null, __('Edit certificate', BPMJ_EDDCM_DOMAIN), __('Edit certificate', BPMJ_EDDCM_DOMAIN),
                Caps::CAP_MANAGE_SETTINGS, 'wp-idea-edit-certificate-template',
                [$certificate_pages_renderer, 'edit_certificate_template']);
            add_submenu_page(null, __('Preview certificate', BPMJ_EDDCM_DOMAIN),
                __('Preview certificate', BPMJ_EDDCM_DOMAIN), Caps::CAP_MANAGE_SETTINGS,
                'wp-idea-preview-certificate-template', [$certificate_pages_renderer, 'preview_certificate_template']);
            add_submenu_page(null, __('Generate certificate', BPMJ_EDDCM_DOMAIN),
                __('Generate certificate', BPMJ_EDDCM_DOMAIN), Caps::CAP_MANAGE_CERTIFICATES,
                'wp-idea-generate-certificate-template',
                [$certificate_pages_renderer, 'generate_certificate_template']);
            add_submenu_page(null, __('Enable new certificate template', BPMJ_EDDCM_DOMAIN),
                __('Enable new certificate template', BPMJ_EDDCM_DOMAIN), Caps::CAP_MANAGE_SETTINGS,
                'wp-idea-enable-new-certificate-template',
                [$certificate_pages_renderer, 'enable_new_certificate_template']);


            add_submenu_page(
                Admin_Menu_Item_Slug::WP_IDEA,
                $this->translator->translate('orders.menu_title'),
                $this->translator->translate('orders.menu_title'),
                Caps::CAP_MANAGE_ORDERS,
                Admin_Menu_Item_Slug::PAYMENTS_HISTORY,
                [$this->payments_page_renderer, 'render_page']
            );

            if (($this->subscription->get_plan() === Subscription_Const::PLAN_PRO) && $this->settings->get(Settings_Const::INCREASING_SALES_ENABLED)) {
                add_submenu_page(
                    Admin_Menu_Item_Slug::WP_IDEA,
                    $this->translator->translate('increasing_sales.menu_title'),
                    $this->translator->translate('increasing_sales.menu_title'),
                    Caps::CAP_MANAGE_ORDERS,
                    Admin_Menu_Item_Slug::INCREASING_SALES,
                    [$this->increasing_sales_page_renderer, 'render_page']
                );
            }

            add_submenu_page(Admin_Menu_Item_Slug::WP_IDEA, __('Discounts', BPMJ_EDDCM_DOMAIN),
                __('Discounts', BPMJ_EDDCM_DOMAIN), Caps::CAP_MANAGE_SETTINGS, 'wp-idea-discounts',
                [$this, 'discounts']);


            if (($this->subscription->get_plan() === Subscription_Const::PLAN_PRO) && $this->settings->get(Settings_Const::PARTNER_PROGRAM)) {
                add_submenu_page(
                    Admin_Menu_Item_Slug::WP_IDEA,
                    $this->translator->translate('affiliate_program.partners_menu_title'),
                    $this->translator->translate('affiliate_program.partners_menu_title'),
                    Caps::CAP_MANAGE_SETTINGS,
                    Admin_Menu_Item_Slug::AFFILIATE_PROGRAM_PARTNERS,
                    [$this, 'affiliate_program_partners']
                );
                add_submenu_page(
                    Admin_Menu_Item_Slug::WP_IDEA,
                    $this->translator->translate('affiliate_program.menu_title'),
                    $this->translator->translate('affiliate_program.menu_title'),
                    Caps::CAP_MANAGE_SETTINGS,
                    Admin_Menu_Item_Slug::AFFILIATE_PROGRAM,
                    [$this, 'affiliate_program']
                );
                add_submenu_page(
                    Admin_Menu_Item_Slug::WP_IDEA,
                    $this->translator->translate('affiliate_program_redirections.menu_title'),
                    $this->translator->translate('affiliate_program_redirections.menu_title'),
                    Caps::CAP_MANAGE_SETTINGS,
                    Admin_Menu_Item_Slug::AFFILIATE_PROGRAM_REDIRECTIONS,
                    [$this, 'affiliate_program_redirections']
                );
            }

            add_submenu_page(
                Admin_Menu_Item_Slug::WP_IDEA,
                $this->translator->translate('customers.menu_title'),
                $this->translator->translate('customers.menu_title'),
                Caps::CAP_MANAGE_CUSTOMERS,
                Customers::PAGE,
                [$this->customers_page_renderer, 'render_page']
            );

            if ($courses_functionality_enabled) {

                add_submenu_page(
                    Admin_Menu_Item_Slug::WP_IDEA,
                    $this->translator->translate('quizzes.page_title'),
                    $this->get_quizzes_page_title(),
                    Caps::CAP_MANAGE_QUIZZES,
                    'wp-idea-tests',
                    [$this->quizzes_page_renderer, 'render_page']
                );

                // Solved quizzes
                add_submenu_page(
                    Admin_Menu_Item_Slug::WP_IDEA,
                    $this->translator->translate('quizzes.page_title'),
                    $this->get_quizzes_page_title(),
                    Caps::CAP_MANAGE_QUIZZES,
                    'wp-idea-tests',
                    [$this->quizzes_page_renderer, 'render_page']
                );

                // Certificates
                add_submenu_page(
                    Admin_Menu_Item_Slug::WP_IDEA,
                    __('Certificates', BPMJ_EDDCM_DOMAIN),
                    __('Certificates', BPMJ_EDDCM_DOMAIN),
                    Caps::CAP_MANAGE_CERTIFICATES,
                    'wp-idea-certificates',
                    [$this->certificate_pages_renderer, 'render_table']
                );

            }

            if ($this->videos_module->is_enabled()) {
                add_submenu_page(
                    Admin_Menu_Item_Slug::WP_IDEA,
                    $this->translator->translate('videos.page_title'),
                    $this->translator->translate('videos.menu_title'),
                    User::oneOfCurrentUserCapsIs($caps_allowing_see_course_menu),
                    Admin_Menu_Item_Slug::VIDEOS,
                    [$this, 'videos']
                );

                add_submenu_page(
                    null,
                    $this->translator->translate('video_uploader.page_title'),
                    $this->translator->translate('video_uploader.page_title'),
                    User::oneOfCurrentUserCapsIs($caps_allowing_see_course_menu),
                    Admin_Menu_Item_Slug::VIDEO_UPLOADER,
                    [$this->video_uploader_page_renderer, 'render_page']
                );

                add_submenu_page(
                    null,
                    $this->translator->translate('video_settings.page_title'),
                    $this->translator->translate('video_settings.page_title'),
                    User::oneOfCurrentUserCapsIs($caps_allowing_see_course_menu),
                    Admin_Menu_Item_Slug::VIDEO_SETTINGS,
                    [$this->video_settings_page_renderer, 'render_page']
                );
            }


            add_submenu_page(
                Admin_Menu_Item_Slug::WP_IDEA,
                $this->translator->translate('purchase_redirections.page_title'),
                $this->translator->translate('purchase_redirections.menu_title'),
                Caps::CAP_MANAGE_ORDERS,
                Admin_Menu_Item_Slug::PURCHASE_REDIRECTIONS,
                [$this, 'purchase_redirections']
            );

            // Submenu Settings
            add_submenu_page(Admin_Menu_Item_Slug::WP_IDEA,
                             $this->translator->translate('settings.menu_title'),
                             $this->translator->translate('settings.menu_title'),
                             Caps::CAP_MANAGE_SETTINGS,
                             Admin_Menu_Item_Slug::SETTINGS,
                             [$this->settings_page_renderer, 'render_page']
            );

            $this->templates_settings_renderer->add_menu_pages();

            // Tools
            add_submenu_page(
                Admin_Menu_Item_Slug::WP_IDEA, __('Tools', BPMJ_EDDCM_DOMAIN), __('Tools', BPMJ_EDDCM_DOMAIN),
                Caps::CAP_MANAGE_SETTINGS, 'wp-idea-tools', [$this, 'tools']
            );

            add_submenu_page(
                null, __('Expiring customers', BPMJ_EDDCM_DOMAIN), __('Expiring customers', BPMJ_EDDCM_DOMAIN),
                Caps::CAP_MANAGE_SETTINGS, 'wp-idea-expiring-customers', [$this->expiring_customers_page_renderer, 'render_page']
            );

            // Reports
            add_submenu_page(
                Admin_Menu_Item_Slug::WP_IDEA, __('Reports', BPMJ_EDDCM_DOMAIN), __('Reports', BPMJ_EDDCM_DOMAIN),
                Caps::CAP_MANAGE_SETTINGS, 'wp-idea-reports', [$this, 'reports']
            );

            add_submenu_page(
                Admin_Menu_Item_Slug::WP_IDEA,
                $this->translator->translate('notifications.menu_title'),
                $this->translator->translate('notifications.menu_title'),
                Caps::CAP_MANAGE_SETTINGS,
                Admin_Menu_Item_Slug::NOTIFICATIONS,
                [$this->notifications_page_renderer, 'render_page']
            );

            add_submenu_page(
                Admin_Menu_Item_Slug::WP_IDEA,
                $this->translator->translate('logs.menu_title'),
                $this->translator->translate('logs.menu_title'),
                Caps::CAP_MANAGE_SETTINGS,
                Admin_Menu_Item_Slug::LOGS,
                [$this->logs_page_renderer, 'render_page']
            );

            add_submenu_page(
                Admin_Menu_Item_Slug::WP_IDEA,
                $this->translator->translate('webhooks.menu_title'),
                $this->translator->translate('webhooks.menu_title'),
                Caps::CAP_MANAGE_SETTINGS,
                Admin_Menu_Item_Slug::WEBHOOKS,
                [
                    $this->webhooks_page_renderer,
                    $this->packages_api->has_access_to_feature(Packages::FEAT_WEBHOOKS) ? 'render_page' : 'render_page_wrong_plan'
                ]
            );

            add_submenu_page(
                Admin_Menu_Item_Slug::WP_IDEA,
                $this->translator->translate('users.menu_title'),
                $this->translator->translate('users.menu_title'),
                Caps::CAP_MANAGE_SETTINGS,
                Admin_Menu_Item_Slug::USERS,
                [$this->user_page_renderer, 'render_page']
            );

            add_submenu_page(
                Admin_Menu_Item_Slug::WP_IDEA,
                null,
                null,
                Caps::CAP_MANAGE_SETTINGS,
                Admin_Menu_Item_Slug::USERS_PROXY
            );

            add_submenu_page(
                Admin_Menu_Item_Slug::WP_IDEA,
                $this->translator->translate('price_history.menu_title'),
                $this->translator->translate('price_history.menu_title'),
                Caps::CAP_MANAGE_PRODUCTS,
                Admin_Menu_Item_Slug::PRICE_HISTORY,
                [$this->price_history_page_renderer, 'render_page']
            );

            if ($this->settings->get(Settings_Const::PHYSICAL_PRODUCTS_ENABLED)) {
                add_submenu_page(Admin_Menu_Item_Slug::WP_IDEA,
                    $this->translator->translate('physical_products.physical_products'),
                    $this->translator->translate('physical_products.physical_products'),
                    User::oneOfCurrentUserCapsIs($caps_allowing_see_course_menu),
                    Admin_Menu_Item_Slug::PHYSICAL_PRODUCTS,
                    [$this->physical_products_page_renderer, 'render_page']
                );

                add_submenu_page(Admin_Menu_Item_Slug::PHYSICAL_PRODUCTS,
                    $this->translator->translate('physical_product_editor.page_title'),
                    $this->translator->translate('physical_product_editor.page_title'),
                    User::oneOfCurrentUserCapsIs($caps_allowing_see_course_menu),
                    Admin_Menu_Item_Slug::EDITOR_PHYSICAL_PRODUCT, [$this->physical_product_editor_page_renderer, 'render_page']
                );
            }

            if ($this->opinions_api->is_enabled()) {
                add_submenu_page(
                    Admin_Menu_Item_Slug::WP_IDEA,
                    $this->translator->translate('opinions.menu_title'),
                    $this->get_opinions_title(),
                    Caps::CAP_MANAGE_PRODUCTS,
                    Admin_Menu_Item_Slug::OPINIONS,
                    [$this->opinions_page_renderer, 'render_page']
                );
            }
        }

        if (!Helper::is_dev()) {
            // clean up
            if (!bpmj_eddcm_enable_edd()) {
                remove_menu_page('edit.php?post_type=download');
            } else {
                remove_submenu_page('edit.php?post_type=download', 'edd-settings');
                remove_submenu_page('edit.php?post_type=download', 'edd-tools');
                remove_submenu_page('edit.php?post_type=download', 'edd-addons');
            }

            remove_menu_page('edit.php?post_type=bpmj_wp_fakturownia');
            remove_menu_page('edit.php?post_type=bpmj_wp_ifirma');
            remove_menu_page('edit.php?post_type=bpmj_wp_wfirma');
            remove_menu_page('edit.php?post_type=bpmj_wp_taxe');
            remove_menu_page('edit.php?post_type=bpmj_wp_infakt');
            if (defined('BPMJ_EDDACT_FILE')) {
                remove_menu_page(plugin_basename(BPMJ_EDDACT_FILE));
            }
            if (defined('BPMJ_EDDRES_FILE')) {
                remove_menu_page(plugin_basename(BPMJ_EDDRES_FILE));
            }
        }
    }

    public function admin_menu_low_priority_items()
    {
        if (!Software_Variant::is_international()) {
            add_submenu_page(
                Admin_Menu_Item_Slug::WP_IDEA, __('Support', BPMJ_EDDCM_DOMAIN), __('Support', BPMJ_EDDCM_DOMAIN),
                Caps::CAP_MANAGE_SETTINGS, 'wp-idea-support', [$this, 'support']
            );
        }
    }


    public function digital_products_list(): void
    {
        $this->digital_products_list_page_renderer->render_page();
    }

    public function affiliate_program(): void
    {
        $this->affiliate_program_view->render();
    }

    public function affiliate_program_redirections(): void
    {
        $this->affiliate_program_redirections_page_renderer->render_page();
    }

    public function affiliate_program_partners(): void
    {
        $this->affiliate_program_partners_view->render();
    }

    public function discounts(): void
    {
        $this->discounts_view->render();
    }

    public function purchase_redirections(): void
    {
        $this->purchase_redirections_view->render();
    }

    public function videos(): void
    {
        $this->videos_view->render();
    }

    /**
     * Show course list page
     */
    public function course_list(): void
    {
        $this->course_list_page_renderer->render_page();
    }

    /**
     * Show Diagnostic page
     */
    public function diagnostic()
    {
        require_once BPMJ_EDDCM_DIR . 'includes/admin/views/diagnostic.php';
    }

    /**
     * Show Course creator page
     */
    public function creator()
    {
        echo $this->view_provider->get_admin(Admin_View_Names::COURSE_CREATOR, [
            'invoices_enabled' => $this->settings->get(Settings_Const::INVOICES_ENABLED)
        ]);
    }

    /**
     * Show Bundle creator page
     */
    public function creator_bundle(): void
    {
        $this->bundle_creator_page_renderer->render_page();
    }

    /**
     * Show Tools page
     */
    public function tools()
    {
        require_once BPMJ_EDDCM_DIR . 'includes/admin/views/tools.php';
    }

    /**
     * Show Support page
     */
    public function support()
    {
        $support = WPI()->support;
        require_once BPMJ_EDDCM_DIR . 'includes/admin/views/support/index.php';
    }

    /**
     * Show Tools page
     */
    public function tests()
    {
        require_once BPMJ_EDDCM_DIR . 'includes/admin/views/tests.php';
    }

    /**
     * Show Certificates page
     */
    public function certificates()
    {
        require_once BPMJ_EDDCM_DIR . 'includes/admin/views/certificates.php';
    }

    /**
     * Show reports page
     */
    public function reports()
    {
        (new Reports_View())->render();
    }

    /**
     * Show Settings page
     */
    public function add_renewal()
    {
        include BPMJ_EDDCM_DIR . 'includes/admin/integrations/edd-pc-add-renewal.php';
    }

    /**
     * Show Settings page
     */
    public function edit_renewal()
    {
        include BPMJ_EDDCM_DIR . 'includes/admin/integrations/edd-pc-edit-renewal.php';
    }

    private function get_quizzes_page_title(): string
    {
        $not_rated_quizzes_count = $this->not_rated_quizzes_counter->count();

        $title_text = $this->translator->translate('quizzes.menu_title');

        if ($not_rated_quizzes_count === 0) {
            return $title_text;
        }

        $title_long = $this->translator->translate('quizzes.not_rated_quizzes');

        return "{$title_text} <span class='not-rated-count-menu-badge' title='{$title_long}'>{$not_rated_quizzes_count}</span>";
    }

    private function get_opinions_title(): string
    {
        $count_waiting_opinions = $this->opinions_api->count_waiting_opinions();
        $title_text = $this->translator->translate('opinions.menu_title');

        if ($count_waiting_opinions === 0) {
            return $title_text;
        }

        $title_long = $this->translator->translate('opinions.menu_title.waiting_opinions');
        return "{$title_text} <span class='waiting-opinions-count-menu-badge' title='{$title_long}'>{$count_waiting_opinions}</span>";
    }
}
