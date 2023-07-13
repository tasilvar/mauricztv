<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\bundle_editor;

use bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug;
use bpmj\wpidea\admin\renderers\Abstract_Page_Renderer;
use bpmj\wpidea\admin\settings\core\services\Interface_Settings_Tab_Scripts;
use bpmj\wpidea\admin\settings\Settings_Const;
use bpmj\wpidea\controllers\admin\Admin_Settings_Fields_Ajax_Controller;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\sales\product\repository\Interface_Product_Repository;
use bpmj\wpidea\sales\product\service\Interface_Url_Resolver;
use bpmj\wpidea\settings\Interface_Settings;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\view\Admin_View_Names;
use bpmj\wpidea\view\Interface_View_Provider;
use bpmj\wpidea\infrastructure\assets\Interface_Script_Loader;
use bpmj\wpidea\admin\pages\bundle_editor\core\configuration\Content_Bundle_Group;

class Bundle_Editor_Page_Renderer extends Abstract_Page_Renderer
{
    public const BUNDLE_ID_QUERY_ARG_NAME = 'edit_bundle_id';

    private Interface_View_Provider $view_provider;
    private Interface_Translator $translator;
    private Interface_Url_Generator $url_generator;
    private Current_Request $current_request;
    private Interface_Settings $settings;
    private Interface_Product_Repository $product_repository;
    private Interface_Url_Resolver $url_resolver;
    private Interface_Settings_Tab_Scripts $settings_tab_scripts;
    private Interface_Script_Loader $script_loader;

    public function __construct(
        Interface_View_Provider $view_provider,
        Interface_Translator $translator,
        Interface_Url_Generator $url_generator,
        Current_Request $current_request,
        Interface_Settings $settings,
        Interface_Product_Repository $product_repository,
        Interface_Url_Resolver $url_resolver,
        Interface_Settings_Tab_Scripts $settings_tab_scripts,
        Interface_Script_Loader $script_loader
    ) {
        $this->view_provider = $view_provider;
        $this->translator = $translator;
        $this->url_generator = $url_generator;
        $this->current_request = $current_request;
        $this->settings = $settings;
        $this->product_repository = $product_repository;
        $this->url_resolver = $url_resolver;
        $this->settings_tab_scripts = $settings_tab_scripts;
        $this->script_loader = $script_loader;
    }

    public function get_rendered_page(): string
    {

        if (!$this->product_exists()) {
            return $this->view_provider->get_admin(Admin_View_Names::PRODUCTS_EDITOR_ERROR, [
                'message' => $this->translator->translate('product_editor.product_does_not_exist')
            ]);
        }

        $this->settings_tab_scripts->register_script(
            $this->get_save_single_field_url(),
            $this->get_save_configuration_group_fields_url()
        );

        $this->register_scripts();

        return $this->view_provider->get_admin(Admin_View_Names::PRODUCTS_EDITOR, [
            'page_title' => $this->translator->translate('bundle_editor.page_title'),
            'sections' => $this->get_sections(),
            'get_tab_ajax_url' => $this->prepare_get_tab_ajax_url(),
            'preview_text' => $this->translator->translate('bundle_editor.preview_button'),
            'preview_url' => $this->get_preview_url(),
            'product_name' => $this->get_product_name(),
            'product_exists' => $this->product_exists(),
            'translator' => $this->translator
        ]);
    }

    private function get_sections(): array
    {
        $sections = [
            [
                'id' => 'general',
                'title' => $this->translator->translate('bundle_editor.sections.general')
            ],
            [
                'id' => Content_Bundle_Group::GROUP_NAME,
                'title' => $this->translator->translate('bundle_editor.sections.package_contents')
            ],
        ];

        if ($this->settings->get(Settings_Const::INVOICES_ENABLED)) {
            $sections[] = [
                'id' => 'invoices',
                'title' => $this->translator->translate('bundle_editor.sections.invoices')
            ];
        }

        if ($this->is_mailer_integration_enabled()) {
            $sections[] = [
                'id' => 'mailings',
                'title' => $this->translator->translate('bundle_editor.sections.mailings')
            ];
        }

        return $sections;
    }

    public function get_page_url(): string
    {
        return $this->url_generator->generate_admin_page_url('admin.php', [
            'page' => Admin_Menu_Item_Slug::EDITOR_PACKAGES
        ]);
    }

    private function get_product_id(): ?int
    {
        $id = $this->current_request->get_query_arg(self::BUNDLE_ID_QUERY_ARG_NAME);

        if (!is_numeric($id)) {
            return null;
        }

        return (int)$id;
    }

    private function prepare_get_tab_ajax_url(): string
    {
        return $this->url_generator->generate(Admin_Settings_Fields_Ajax_Controller::class, 'get_tab_content', [
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create(),
            self::BUNDLE_ID_QUERY_ARG_NAME => $this->get_product_id()
        ]);
    }

    private function get_save_single_field_url(): string
    {
        return $this->url_generator->generate(Admin_Settings_Fields_Ajax_Controller::class, 'save_field_value', [
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create(),
            self::BUNDLE_ID_QUERY_ARG_NAME => $this->get_product_id()
        ]);
    }

    private function get_save_configuration_group_fields_url(): string
    {
        return $this->url_generator->generate(Admin_Settings_Fields_Ajax_Controller::class, 'save_configuration_group_fields_value', [
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create(),
            self::BUNDLE_ID_QUERY_ARG_NAME => $this->get_product_id()
        ]);
    }

    private function is_mailer_integration_enabled(): bool
    {
        return WPI()->diagnostic->mailer_integration();
    }

    private function get_product_name(): string
    {
        $product = $this->product_repository->find(new Product_ID($this->get_product_id()));

        return $product ? $product->get_name()->get_value() : '';
    }

    private function get_preview_url(): string
    {
        $product_id = $this->get_product_id();

        if (!$product_id) {
            return '';
        }

        return $this->url_resolver->get_by_product_id(new Product_ID($product_id))->get_value();
    }

    private function register_scripts(): void
    {
        $this->script_loader->enqueue_script('files_table_script', BPMJ_EDDCM_URL . 'assets/admin/js/bundle-content.js', [
            'jquery',
        ], BPMJ_EDDCM_VERSION);
    }

    private function product_exists(): bool
    {
        $product_id = $this->get_product_id();

        if (!$product_id) {
            return false;
        }

        $product = $this->product_repository->find(new Product_ID($product_id));

        if (!$product) {
            return false;
        }

        return true;
    }

}