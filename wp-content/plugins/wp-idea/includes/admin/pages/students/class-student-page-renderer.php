<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\students;

use bpmj\wpidea\admin\renderers\Abstract_Page_Renderer;
use bpmj\wpidea\admin\tables\dynamic\Interface_Dynamic_Table;
use bpmj\wpidea\admin\tables\dynamic\module\Dynamic_Tables_Module;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\view\Interface_View_Provider;

class Student_Page_Renderer extends Abstract_Page_Renderer
{
    private Interface_Translator $translator;
    private Interface_View_Provider $view_provider;
    private Student_Table_Config_Provider $table_config_provider;
    private Dynamic_Tables_Module $dynamic_tables_module;

    public function __construct(
        Interface_Translator $translator,
        Interface_View_Provider $view_provider,
        Student_Table_Config_Provider $table_config_provider,
        Dynamic_Tables_Module $dynamic_tables_module
    ) {
        $this->translator = $translator;
        $this->view_provider = $view_provider;
        $this->table_config_provider = $table_config_provider;
        $this->dynamic_tables_module = $dynamic_tables_module;
    }

    public function get_rendered_page(): string
    {
        return $this->view_provider->get_admin('/pages/students/index', [
            'table' => $this->prepare_table(),
            'page_title' => $this->translator->translate('students.page_title')
        ]);
    }

    private function prepare_table(): Interface_Dynamic_Table
    {
        return $this->dynamic_tables_module->create_table_from_config(
            $this->table_config_provider->get_config()
        );
    }
}
