<?php

namespace bpmj\wpidea\modules\app_view;

use bpmj\wpidea\environment\Interface_Site;
use bpmj\wpidea\events\actions\Action_Name;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\events\filters\Interface_Filters;
use bpmj\wpidea\infrastructure\assets\Interface_Script_Loader;
use bpmj\wpidea\infrastructure\theme\Interface_Theme_Support;
use bpmj\wpidea\modules\app_view\ams\api\controllers\AMS_Rest_Routes_Controller;
use bpmj\wpidea\modules\app_view\api\{App_View_API, App_View_API_Static_Helper};
use bpmj\wpidea\modules\app_view\core\services\App_View_Cookie_Setter;
use bpmj\wpidea\modules\app_view\web\PDF_Viewer_Renderer;
use bpmj\wpidea\shared\abstractions\modules\Interface_Module;
use bpmj\wpidea\translator\Interface_Translator;

class App_View_Module implements Interface_Module
{
    public const APP_VIEW_MODE_URL_PARAM_NAME = 'pbg_mode';
    public const APP_VIEW_MODE_URL_PARAM_VALUE = 'app';
    public const APP_VIEW_COOKIE_NAME = 'publigo_app_view_cookie';
    public const APP_VIEW_COOKIE_VALUE = 'app_view';
    public const APP_VIEW_COOKIE_LIFE_TIME = 365 * 24 * 60 * 60;

    private Interface_Site $site;
    private Interface_Translator $translator;
    private App_View_API $app_view_api;
    private Interface_Actions $actions;
    private App_View_Cookie_Setter $cookie_setter;
    private AMS_Rest_Routes_Controller $ams_rest_routes_controller;
    private PDF_Viewer_Renderer $pdf_viewer_renderer;
    private Interface_Script_Loader $script_loader;
    private Interface_Theme_Support $theme_support;
    private Interface_Filters $filters;

    public function __construct(
        Interface_Site $site,
        Interface_Translator $translator,
        App_View_API $app_view_api,
        Interface_Actions $actions,
        App_View_Cookie_Setter $cookie_setter,
        AMS_Rest_Routes_Controller $ams_rest_routes_controller,
        PDF_Viewer_Renderer $pdf_viewer_renderer,
        Interface_Script_Loader $script_loader,
        Interface_Theme_Support $theme_support,
        Interface_Filters $filters
    ) {
        $this->site = $site;
        $this->translator = $translator;
        $this->app_view_api = $app_view_api;
        $this->actions = $actions;
        $this->cookie_setter = $cookie_setter;
        $this->ams_rest_routes_controller = $ams_rest_routes_controller;
        $this->pdf_viewer_renderer = $pdf_viewer_renderer;
        $this->script_loader = $script_loader;
        $this->theme_support = $theme_support;
        $this->filters = $filters;
    }

    public function init(): void
    {
        $this->ams_rest_routes_controller->init();

        App_View_API_Static_Helper::init($this->app_view_api);
        $this->cookie_setter->init();

        if ($this->app_view_api->is_active()) {
            $this->enqueue_assets();
            $this->remove_admin_bar_padding();
            $this->pdf_viewer_renderer->init();
            $this->disable_file_links_encryption();
        }

    }

    public function get_routes(): array
    {
        return [];
    }

    public function get_translations(): array
    {
        return [
            'pl_PL' => [
                'app_view.clipboard.success' => 'Skopiowano link do schowka',
                'app_view.clipboard.error_format_file' => 'Ten format jest nieobsługiwany z poziomu aplikacji. Obejrzyj ten plik korzystając z przeglądarki internetowej.',
                'app_view.pdf_viewer.close' => 'Zamknij'
            ],
            'en_US' => [
                'app_view.clipboard.success' => 'Copied link to clipboard',
                'app_view.clipboard.error_format_file' => 'This format is not supported from within the application. View this file using a web browser.',
                'app_view.pdf_viewer.close' => 'Close'
            ]
        ];
    }

    private function enqueue_assets(): void
    {
        $this->actions->add(Action_Name::ENQUEUE_SCRIPTS, function () {
            $this->script_loader->enqueue_style(
                'app_view_style',
                BPMJ_EDDCM_URL . 'includes/modules/app-view/web/assets/css/app-view.css',
                [],
                BPMJ_EDDCM_VERSION
            );

            $this->script_loader->enqueue_style(
                'pdf_viewer_style',
                BPMJ_EDDCM_URL . 'includes/modules/app-view/web/assets/css/pdf_viewer.min.css',
                [],
                BPMJ_EDDCM_VERSION
            );
        });

        $this->actions->add(Action_Name::ENQUEUE_SCRIPTS, function () {
            $this->script_loader->enqueue_script(
                'app_view_script',
                BPMJ_EDDCM_URL . 'includes/modules/app-view/web/assets/js/app-view.js',
                ['jquery'],
                BPMJ_EDDCM_VERSION,
                true
            );

            $this->script_loader->localize_script(
                'app_view_script',
                'i18n',
                [
                    'clipboard' => [
                        'success' => $this->translator->translate('app_view.clipboard.success'),
                        'error_format_file' => $this->translator->translate('app_view.clipboard.error_format_file'),
                        'base_url' => $this->site->get_base_url(),
                        'file_types' => ['png', 'gif', 'jpg', 'pdf', 'gif', 'jpeg', 'doc', 'docx', 'odt', 'txt', 'mp3', 'xls', 'odt']
                    ],
                    'pdf' => [
                        'param_name' => PDF_Viewer_Renderer::PDF_VIEWER_URL_PARAM_NAME
                    ]
                ]
            );

            $this->script_loader->enqueue_script(
                'pdf_script',
                BPMJ_EDDCM_URL . 'includes/modules/app-view/web/assets/js/pdf.min.js',
                ['jquery'],
                BPMJ_EDDCM_VERSION
            );

            $this->script_loader->enqueue_script(
                'pdf_viewer_script',
                BPMJ_EDDCM_URL . 'includes/modules/app-view/web/assets/js/pdf.viewer.min.js',
                ['jquery'],
                BPMJ_EDDCM_VERSION
            );
        });
    }

    private function remove_admin_bar_padding(): void
    {
        $this->theme_support->remove_theme_support(Interface_Theme_Support::ADMIN_BAR);
    }

    private function disable_file_links_encryption(): void
    {
        $this->filters->add('bpmj_eddpc_enable_encrypt_link', function (bool $encrypt) {
            return false;
        });
    }
}