<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\settings;

use bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug;
use bpmj\wpidea\admin\renderers\Abstract_Page_Renderer;
use bpmj\wpidea\admin\settings\core\services\Interface_Settings_Tab_Scripts;
use bpmj\wpidea\controllers\admin\Admin_Settings_Fields_Ajax_Controller;
use bpmj\wpidea\infrastructure\assets\Interface_Script_Loader;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\view\Admin_View_Names;
use bpmj\wpidea\view\Interface_View_Provider;

class Settings_Page_Renderer extends Abstract_Page_Renderer
{
    private Interface_View_Provider $view_provider;
    private Interface_Translator $translator;
    private Interface_Url_Generator $url_generator;
    private Interface_Settings_Tab_Scripts $settings_tab_scripts;
	private Interface_Script_Loader $script_loader;

	public function __construct(
        Interface_View_Provider $view_provider,
        Interface_Translator $translator,
        Interface_Url_Generator $url_generator,
        Interface_Settings_Tab_Scripts $settings_tab_scripts,
	    Interface_Script_Loader $script_loader
    ) {
        $this->view_provider = $view_provider;
        $this->translator = $translator;
        $this->url_generator = $url_generator;
        $this->settings_tab_scripts = $settings_tab_scripts;
		$this->script_loader = $script_loader;
	}

    public function get_rendered_page(): string
    {
        $this->settings_tab_scripts->register_script(
            $this->get_save_single_field_url(),
            $this->get_save_configuration_group_fields_url(),
            $this->get_license_key_info_url()
        );

		$this->register_field_scripts();

        return $this->view_provider->get_admin(Admin_View_Names::SETTINGS, [
            'page_title' => $this->translator->translate('settings.page_title'),
            'sections' => $this->get_sections(),
            'get_tab_ajax_url' => $this->prepare_get_tab_ajax_url()
        ]);
    }

    private function get_sections(): array
    {
        return [
            [
                'id' => 'general',
                'title' => $this->translator->translate('settings.sections.general')
            ],
            [
                'id' => 'accounting',
                'title' => $this->translator->translate('settings.sections.accounting')
            ],
            [
                'id' => 'payments',
                'title' => $this->translator->translate('settings.sections.payment')
            ],
            [
                'id' => 'design',
                'title' => $this->translator->translate('settings.sections.design')
            ],
            [
                'id' => 'integrations',
                'title' => $this->translator->translate('settings.sections.integrations')
            ],
            [
                'id' => 'cart',
                'title' => $this->translator->translate('settings.sections.cart')
            ],
            [
                'id' => 'messages',
                'title' => $this->translator->translate('settings.sections.messages')
            ],
            [
                'id' => 'gift',
                'title' => $this->translator->translate('settings.sections.gift')
            ],
            [
                'id' => 'certificate',
                'title' => $this->translator->translate('settings.sections.certificate')
            ],
            [
                'id' => 'analytics',
                'title' => $this->translator->translate('settings.sections.analytics')
            ],
            [
                'id' => 'modules',
                'title' => $this->translator->translate('settings.sections.modules')
            ],
            [
                'id' => 'advanced',
                'title' => $this->translator->translate('settings.sections.advanced')
            ]
        ];
    }

    public function get_page_url(): string
    {
        return $this->url_generator->generate_admin_page_url('admin.php', [
            'page' => Admin_Menu_Item_Slug::SETTINGS
        ]);
    }

    private function prepare_get_tab_ajax_url(): string
    {
        return $this->url_generator->generate(Admin_Settings_Fields_Ajax_Controller::class, 'get_tab_content', [
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create()
        ]);
    }

    private function get_save_single_field_url(): string
    {
        return $this->url_generator->generate(Admin_Settings_Fields_Ajax_Controller::class, 'save_field_value', [
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create()
        ]);
    }

    private function get_save_configuration_group_fields_url(): string
    {
        return $this->url_generator->generate(Admin_Settings_Fields_Ajax_Controller::class, 'save_configuration_group_fields_value', [
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create()
        ]);
    }

    private function get_license_key_info_url(): string
    {
        return $this->url_generator->generate(Admin_Settings_Fields_Ajax_Controller::class, 'get_license_field_info_html', [
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create()
        ]);
    }

	private function register_field_scripts(): void
	{
		$this->script_loader->enqueue_script('custom_sorting_script', BPMJ_EDDCM_URL . 'assets/admin/js/custom-sorting-table.js', [
			'jquery',
		], BPMJ_EDDCM_VERSION);
	}
}