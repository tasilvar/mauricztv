<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\course_editor;

use bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug;
use bpmj\wpidea\admin\pages\course_editor\core\configuration\Course_Structure_Group;
use bpmj\wpidea\admin\renderers\Abstract_Page_Renderer;
use bpmj\wpidea\admin\settings\core\services\Interface_Settings_Tab_Scripts;
use bpmj\wpidea\admin\settings\Settings_Const;
use bpmj\wpidea\controllers\admin\Admin_Settings_Fields_Ajax_Controller;
use bpmj\wpidea\Courses;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\infrastructure\assets\Interface_Script_Loader;
use bpmj\wpidea\learning\course\Course_ID;
use bpmj\wpidea\learning\course\Interface_Readable_Course_Repository;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\sales\product\service\Interface_Url_Resolver;
use bpmj\wpidea\settings\Interface_Settings;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\view\Admin_View_Names;
use bpmj\wpidea\view\Interface_View_Provider;


class Course_Editor_Page_Renderer extends Abstract_Page_Renderer
{
    public const COURSE_ID_QUERY_ARG_NAME = 'edit_course_id';

    private Courses $courses;
    private Interface_View_Provider $view_provider;
    private Interface_Translator $translator;
    private Interface_Url_Generator $url_generator;
    private Current_Request $current_request;
    private Interface_Settings $settings;
    private Interface_Readable_Course_Repository $course_repository;
    private Interface_Url_Resolver $url_resolver;
    private Interface_Settings_Tab_Scripts $settings_tab_scripts;
    private Interface_Script_Loader $script_loader;
    private Interface_Actions $actions;

    public function __construct(
        Courses $courses,
        Interface_View_Provider $view_provider,
        Interface_Translator $translator,
        Interface_Url_Generator $url_generator,
        Current_Request $current_request,
        Interface_Settings $settings,
        Interface_Readable_Course_Repository $course_repository,
        Interface_Url_Resolver $url_resolver,
        Interface_Settings_Tab_Scripts $settings_tab_scripts,
        Interface_Script_Loader $script_loader,
        Interface_Actions $actions
    ) {
        $this->courses = $courses;
        $this->view_provider = $view_provider;
        $this->translator = $translator;
        $this->url_generator = $url_generator;
        $this->current_request = $current_request;
        $this->settings = $settings;
        $this->course_repository = $course_repository;
        $this->url_resolver = $url_resolver;
        $this->settings_tab_scripts = $settings_tab_scripts;
        $this->script_loader = $script_loader;
        $this->actions = $actions;

        $this->fix_edd_scripts_loading_for_variants_table();
    }

    public function get_rendered_page(): string
    {
        if (!$this->course_exists()) {
            return $this->view_provider->get_admin(Admin_View_Names::PRODUCTS_EDITOR_ERROR, [
                'message' => $this->translator->translate('course_editor.course_does_not_exist')
            ]);
        }

        $this->settings_tab_scripts->register_script(
            $this->get_save_single_field_url(),
            $this->get_save_configuration_group_fields_url()
        );

        $this->register_scripts();

        return $this->view_provider->get_admin(Admin_View_Names::PRODUCTS_EDITOR, [
            'page_title' => $this->translator->translate('course_editor.page_title'),
            'sections' => $this->get_sections(),
            'get_tab_ajax_url' => $this->prepare_get_tab_ajax_url(),
            'preview_text' => $this->translator->translate('course_editor.preview_button.course'),
            'preview_url' => $this->get_preview_course_url(),
            'preview_course_panel_text' => $this->translator->translate('course_editor.preview_button.course_panel'),
            'preview_course_panel_url' => $this->get_preview_course_panel_url(),
            'product_name' => $this->get_course_name(),
            'translator' => $this->translator
        ]);
    }

    private function get_sections(): array
    {
        $sections = [
            [
                'id' => 'general',
                'title' => $this->translator->translate('course_editor.sections.general')
            ],
            [
                'id' => Course_Structure_Group::GROUP_NAME,
                'title' => $this->translator->translate('course_editor.sections.structure')
            ],
            [
                'id' => 'link_generator',
                'title' => $this->translator->translate('course_editor.sections.link_generator')
            ],
        ];

        if ($this->settings->get(Settings_Const::INVOICES_ENABLED)) {
            $sections[] = [
                'id' => 'invoices',
                'title' => $this->translator->translate('course_editor.sections.invoices')
            ];
        }

        if ($this->is_mailer_integration_enabled()) {
            $sections[] = [
                'id' => 'mailings',
                'title' => $this->translator->translate('course_editor.sections.mailings')
            ];
        }

        if ($this->settings->get(Settings_Const::SELL_DISCOUNTS_ENABLED)) {
            $sections[] = [
                'id' => 'discount_code',
                'title' => $this->translator->translate('course_editor.sections.discount_code')
            ];
        }

        return $sections;
    }

    public function get_page_url(): string
    {
        return $this->url_generator->generate_admin_page_url('admin.php', [
            'page' => Admin_Menu_Item_Slug::EDITOR_COURSE
        ]);
    }

    private function get_course_id(): ?int
    {
        $id = $this->current_request->get_query_arg(self::COURSE_ID_QUERY_ARG_NAME);

        if (!is_numeric($id)) {
            return null;
        }

        return (int)$id;
    }

    private function prepare_get_tab_ajax_url(): string
    {
        return $this->url_generator->generate(Admin_Settings_Fields_Ajax_Controller::class, 'get_tab_content', [
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create(),
            self::COURSE_ID_QUERY_ARG_NAME => $this->get_course_id()
        ]);
    }

    private function get_save_single_field_url(): string
    {
        return $this->url_generator->generate(Admin_Settings_Fields_Ajax_Controller::class, 'save_field_value', [
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create(),
            self::COURSE_ID_QUERY_ARG_NAME => $this->get_course_id()
        ]);
    }

    private function get_save_configuration_group_fields_url(): string
    {
        return $this->url_generator->generate(Admin_Settings_Fields_Ajax_Controller::class, 'save_configuration_group_fields_value', [
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create(),
            self::COURSE_ID_QUERY_ARG_NAME => $this->get_course_id()
        ]);
    }

    private function is_mailer_integration_enabled(): bool
    {
        return WPI()->diagnostic->mailer_integration();
    }

    private function get_course_name(): string
    {
        $course = $this->course_repository->find_by_id(new Course_ID($this->get_course_id()));

        return $course ? $course->get_title() : '';
    }

    private function get_preview_course_url(): string
    {
        $course_id = $this->get_course_id();

        if (!$course_id) {
            return '';
        }

        $product_id = $this->courses->get_product_by_course($course_id);

        if (!$product_id) {
            return '';
        }

        return $this->url_resolver->get_by_product_id(new Product_ID((int)$product_id))->get_value();
    }

    private function get_preview_course_panel_url(): string
    {
        $course_id = $this->get_course_id();

        if (!$course_id) {
            return '';
        }

        return $this->url_resolver->get_by_product_id(new Product_ID($course_id))->get_value();
    }

    private function register_scripts(): void
    {
        $this->script_loader->enqueue_script('files_table_script', BPMJ_EDDCM_URL . 'assets/admin/js/product-files.js', [
            'jquery',
        ], BPMJ_EDDCM_VERSION);
    }

    private function course_exists(): bool
    {
        $course_id = $this->get_course_id();

        if (!$course_id) {
            return false;
        }

        $course = $this->course_repository->find_by_id(new Course_ID($course_id));

        if (!$course) {
            return false;
        }

        if (!$course->get_product_id()) {
            return false;
        }

        return true;
    }

    private function fix_edd_scripts_loading_for_variants_table(): void
    {
        $this->actions->add('admin_enqueue_scripts', function () {
            $js_dir = EDD_PLUGIN_URL . 'assets/js/';
            $css_dir = EDD_PLUGIN_URL . 'assets/css/';

            $suffix = '.min';

            // These have to be global
            wp_register_style('jquery-chosen', $css_dir . 'chosen' . $suffix . '.css', [], EDD_VERSION);
            wp_enqueue_style('jquery-chosen');

            wp_register_script('jquery-chosen', $js_dir . 'chosen.jquery' . $suffix . '.js', ['jquery'], EDD_VERSION);
            wp_enqueue_script('jquery-chosen');

            wp_register_script('edd-admin-scripts', $js_dir . 'admin-scripts' . $suffix . '.js', ['jquery'], EDD_VERSION, false);
            wp_enqueue_script('edd-admin-scripts');

            wp_localize_script('edd-admin-scripts', 'edd_vars', [
                'one_price_min' => $this->translator->translate('course_editor.sections.general.variable_prices.edit.table.message.one_price_min'),
                'one_field_min' => $this->translator->translate('course_editor.sections.general.variable_prices.edit.table.message.one_field_min')
            ]);

            wp_register_style('edd-admin', $css_dir . 'edd-admin' . $suffix . '.css', EDD_VERSION);
            wp_enqueue_style('edd-admin');
        }, 100);
    }

}