<?php

namespace bpmj\wpidea\templates_system\admin\renderers;

use bpmj\wpidea\admin\helpers\html\Button;
use bpmj\wpidea\admin\helpers\html\Dashicon;
use bpmj\wpidea\admin\helpers\html\Info_Box;
use bpmj\wpidea\admin\helpers\html\Link;
use bpmj\wpidea\admin\helpers\html\Popup;
use bpmj\wpidea\admin\renderers\Abstract_Page_Renderer;
use bpmj\wpidea\admin\renderers\Interface_Page_Renderer;
use bpmj\wpidea\admin\settings\Settings_Const;
use bpmj\wpidea\admin\tables\dynamic\Interface_Dynamic_Table;
use bpmj\wpidea\admin\tables\dynamic\module\Dynamic_Tables_Module;
use bpmj\wpidea\admin\tables\Enhanced_Table;
use bpmj\wpidea\admin\tables\Enhanced_Table_Items_Collection;
use bpmj\wpidea\admin\tables\styles\Wpi_Style;
use bpmj\wpidea\settings\LMS_Settings;
use bpmj\wpidea\templates_system\admin\Template_Groups_Page;
use bpmj\wpidea\templates_system\admin\Templates_List_Table_Config_Provider;
use bpmj\wpidea\templates_system\groups\Template_Group;
use bpmj\wpidea\templates_system\groups\Template_Group_Id;
use bpmj\wpidea\templates_system\templates\scarlet\Course_Lesson_Template;
use bpmj\wpidea\templates_system\templates\scarlet\Course_Module_Template;
use bpmj\wpidea\templates_system\templates\scarlet\Course_Panel_Template;
use bpmj\wpidea\templates_system\templates\scarlet\Course_Quiz_Template;
use bpmj\wpidea\templates_system\templates\Template;
use bpmj\wpidea\View;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\templates_system\admin\ajax\Group_Settings_Ajax_Handler;
use bpmj\wpidea\view\Interface_View_Provider;

class Templates_List_Renderer extends Abstract_Page_Renderer implements Interface_Templates_List_Renderer
{
    private const AJAX_NONCE_NAME = 'bpmj_template_groups_security_token';
    private const AJAX_PARAM_NAME_SECURITY_TOKEN = 'nonce';
    private const AJAX_PARAM_NAME_GROUP_ID = 'group_id';

    private Interface_Translator $translator;

    private LMS_Settings $lms_settings;

    private Dynamic_Tables_Module $dynamic_tables_module;

    private Interface_View_Provider $view_provider;

    private Templates_List_Table_Config_Provider $config_provider;

    public function __construct(
        LMS_Settings $lms_settings,
        Interface_Translator $translator,
        Dynamic_Tables_Module $dynamic_tables_module,
        Interface_View_Provider $view_provider,
        Templates_List_Table_Config_Provider $config_provider
    )
    {
        $this->lms_settings = $lms_settings;
        $this->translator = $translator;
        $this->dynamic_tables_module = $dynamic_tables_module;
        $this->view_provider = $view_provider;
        $this->config_provider = $config_provider;
    }

    public function get_rendered_page(): string
    {
        return $this->view_provider->get_admin('/pages/templates-list/index', [
            'table' => $this->prepare_table(),
            'page_title' => $this->translator->translate('templates_list.page_title'),
        ]);
    }

    private function prepare_table(): Interface_Dynamic_Table
    {
        return $this->dynamic_tables_module->create_table_from_config(
            $this->config_provider->get_config()
        );
    }
}
