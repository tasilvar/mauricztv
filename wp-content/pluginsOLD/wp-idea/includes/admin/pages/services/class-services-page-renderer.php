<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\services;

use bpmj\wpidea\admin\renderers\Abstract_Page_Renderer;
use bpmj\wpidea\app\services\Services_App_Service;
use bpmj\wpidea\view\Interface_View_Provider;
use bpmj\wpidea\view\Admin_View_Names;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug;
use bpmj\wpidea\admin\tables\dynamic\Interface_Dynamic_Table;
use bpmj\wpidea\admin\tables\dynamic\module\Dynamic_Tables_Module;

class Services_Page_Renderer extends Abstract_Page_Renderer
{
    private Interface_View_Provider $view_provider;
    private Interface_Translator $translator;
    private Interface_Url_Generator $url_generator;
    private Dynamic_Tables_Module $dynamic_tables_module;
    private Services_Table_Config_Provider $config_provider;

    public function __construct(
        Interface_View_Provider $view_provider,
        Interface_Translator $translator,
        Interface_Url_Generator $url_generator,
        Dynamic_Tables_Module $dynamic_tables_module,
        Services_Table_Config_Provider $config_provider
    )
    {
        $this->view_provider = $view_provider;
        $this->translator = $translator;
        $this->url_generator = $url_generator;
        $this->dynamic_tables_module = $dynamic_tables_module;
        $this->config_provider = $config_provider;
    }

    public function get_rendered_page(): string
    {

        return $this->view_provider->get_admin(Admin_View_Names::SERVICES, [
            'table' => $this->prepare_table(),
            'page_title' => $this->translator->translate('services.page_title')
        ]);
    }

    private function prepare_table(): Interface_Dynamic_Table
    {
        return $this->dynamic_tables_module->create_table_from_config(
            $this->config_provider->get_config()
        );
    }

    public function get_page_url(): string
    {
        return $this->url_generator->generate_admin_page_url('admin.php', [
            'page' => Admin_Menu_Item_Slug::SERVICES
        ]);
    }
}