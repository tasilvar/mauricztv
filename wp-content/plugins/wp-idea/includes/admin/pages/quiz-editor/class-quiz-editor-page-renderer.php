<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\quiz_editor;

use bpmj\wpidea\admin\renderers\Abstract_Page_Renderer;
use bpmj\wpidea\admin\settings\core\services\Interface_Settings_Tab_Scripts;
use bpmj\wpidea\controllers\admin\Admin_Settings_Fields_Ajax_Controller;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\infrastructure\assets\Interface_Script_Loader;
use bpmj\wpidea\learning\quiz\Quiz_ID;
use bpmj\wpidea\learning\quiz\repository\Interface_Quiz_Settings_Repository;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\sales\product\service\Interface_Url_Resolver;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\view\Admin_View_Names;
use bpmj\wpidea\view\Interface_View_Provider;


class Quiz_Editor_Page_Renderer extends Abstract_Page_Renderer
{
    public const QUIZ_ID_QUERY_ARG_NAME = 'edit_quiz_id';

    private Interface_View_Provider $view_provider;
    private Interface_Translator $translator;
    private Interface_Url_Generator $url_generator;
    private Current_Request $current_request;
    private Interface_Quiz_Settings_Repository $quiz_settings_repository;
    private Interface_Settings_Tab_Scripts $settings_tab_scripts;
    private Interface_Script_Loader $script_loader;
    private Interface_Url_Resolver $url_resolver;

    public function __construct(
        Interface_View_Provider $view_provider,
        Interface_Translator $translator,
        Interface_Url_Generator $url_generator,
        Current_Request $current_request,
        Interface_Quiz_Settings_Repository $quiz_settings_repository,
        Interface_Settings_Tab_Scripts $settings_tab_scripts,
        Interface_Script_Loader $script_loader,
        Interface_Url_Resolver $url_resolver
    ) {
        $this->view_provider = $view_provider;
        $this->translator = $translator;
        $this->url_generator = $url_generator;
        $this->current_request = $current_request;
        $this->quiz_settings_repository = $quiz_settings_repository;
        $this->settings_tab_scripts = $settings_tab_scripts;
        $this->script_loader = $script_loader;
        $this->url_resolver = $url_resolver;
    }

    public function get_rendered_page(): string
    {
        if (!$this->quiz_exists()) {
            return $this->view_provider->get_admin(Admin_View_Names::PRODUCTS_EDITOR_ERROR, [
                'message' => $this->translator->translate('quiz_editor.quiz_does_not_exist')
            ]);
        }

        $this->settings_tab_scripts->register_script(
            $this->get_save_single_field_url(),
            $this->get_save_configuration_group_fields_url()
        );

        $this->register_scripts();

        return $this->view_provider->get_admin(Admin_View_Names::PRODUCTS_EDITOR, [
            'page_title' => $this->translator->translate('quiz_editor.page_title'),
            'sections' => $this->get_sections(),
            'get_tab_ajax_url' => $this->prepare_get_tab_ajax_url(),
            'preview_text' => $this->translator->translate('quiz_editor.preview_button'),
            'preview_url' => $this->get_preview_course_url(),
            'product_name' => $this->get_quiz_name(),
            'translator' => $this->translator
        ]);
    }

    private function get_sections(): array
    {
        return [
            [
                'id' => 'general',
                'title' => $this->translator->translate('quiz_editor.sections.general')
            ],
            [
                'id' => 'structure',
                'title' => $this->translator->translate('quiz_editor.sections.structure')
            ],
            [
                'id' => 'files',
                'title' => $this->translator->translate('quiz_editor.sections.files'),
            ],
        ];
    }

    private function get_quiz_id(): ?int
    {
        $id = $this->current_request->get_query_arg(self::QUIZ_ID_QUERY_ARG_NAME);

        if (!is_numeric($id)) {
            return null;
        }

        return (int)$id;
    }

    private function prepare_get_tab_ajax_url(): string
    {
        return $this->url_generator->generate(Admin_Settings_Fields_Ajax_Controller::class, 'get_tab_content', [
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create(),
            self::QUIZ_ID_QUERY_ARG_NAME => $this->get_quiz_id()
        ]);
    }

    private function get_save_single_field_url(): string
    {
        return $this->url_generator->generate(Admin_Settings_Fields_Ajax_Controller::class, 'save_field_value', [
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create(),
            self::QUIZ_ID_QUERY_ARG_NAME => $this->get_quiz_id()
        ]);
    }

    private function get_save_configuration_group_fields_url(): string
    {
        return $this->url_generator->generate(Admin_Settings_Fields_Ajax_Controller::class, 'save_configuration_group_fields_value', [
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create(),
            self::QUIZ_ID_QUERY_ARG_NAME => $this->get_quiz_id()
        ]);
    }

    private function get_quiz_name(): string
    {
        $quiz = $this->quiz_settings_repository->find_by_id(new Quiz_ID($this->get_quiz_id()));

        return $quiz ? $quiz->get_name() : '';
    }

    private function quiz_exists(): bool
    {
        $quiz_id = $this->get_quiz_id();

        if (!$quiz_id) {
            return false;
        }

        $quiz = $this->quiz_settings_repository->find_by_id(new Quiz_ID($this->get_quiz_id()));

        if (!$quiz) {
            return false;
        }

        return true;
    }

    private function register_scripts(): void
    {
        $this->script_loader->enqueue_script('files_table_script', BPMJ_EDDCM_URL . 'assets/admin/js/product-files.js', [
            'jquery',
        ], BPMJ_EDDCM_VERSION);
    }

    private function get_preview_course_url(): string
    {
        $quiz_id = $this->get_quiz_id();

        if (!$quiz_id) {
            return '';
        }

        return $this->url_resolver->get_by_product_id(new Product_ID((int)$quiz_id))->get_value();
    }
}