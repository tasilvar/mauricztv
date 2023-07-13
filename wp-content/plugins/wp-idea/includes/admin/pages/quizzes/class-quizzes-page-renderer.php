<?php
declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\quizzes;

use bpmj\wpidea\admin\renderers\Abstract_Page_Renderer;
use bpmj\wpidea\admin\tables\dynamic\Interface_Dynamic_Table;
use bpmj\wpidea\admin\tables\dynamic\module\Dynamic_Tables_Module;
use bpmj\wpidea\controllers\admin\Admin_Quizzes_Ajax_Controller;
use bpmj\wpidea\Packages;
use bpmj\wpidea\packages\Interface_Packages_API;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\view\Interface_View_Provider;

class Quizzes_Page_Renderer extends Abstract_Page_Renderer
{
    private Interface_View_Provider $view_provider;
    private Interface_Translator $translator;
    private Quiz_Table_Config_Provider $config_provider;
    private Dynamic_Tables_Module $dynamic_tables_module;
    private Interface_Packages_API $packages_api;

    public function __construct(
        Interface_View_Provider $view_provider,
        Interface_Translator $translator,
        Quiz_Table_Config_Provider $config_provider,
        Dynamic_Tables_Module $dynamic_tables_module,
        Interface_Packages_API $packages_api
    ) {
        $this->view_provider = $view_provider;
        $this->translator = $translator;
        $this->config_provider = $config_provider;
        $this->dynamic_tables_module = $dynamic_tables_module;
        $this->packages_api = $packages_api;
    }

    public function get_rendered_page(): string
    {
        return $this->view_provider->get_admin('/pages/quizzes/index', [
            'table' => $this->prepare_table(),
            'page_title' => $this->translator->translate('quizzes.page_title'),
            'no_access_to_feature' => $this->maybe_get_no_access_to_feature_message()
        ]);
    }

    private function maybe_get_no_access_to_feature_message(): ?string
    {
        if($this->packages_api->has_access_to_feature(Packages::FEAT_TESTS)) {
            return null;
        }

        return $this->packages_api->render_no_access_to_feature_info(
            Packages::FEAT_TESTS,
            $this->translator->translate('course_editor.sections.structure.quiz.upgrade_needed')
        );
    }

    private function prepare_table(): Interface_Dynamic_Table
    {
        return $this->dynamic_tables_module->create_table_from_config(
            $this->config_provider->get_config()
        );
    }
}