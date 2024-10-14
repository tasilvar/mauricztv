<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\price_history;

use bpmj\wpidea\admin\renderers\Abstract_Page_Renderer;
use bpmj\wpidea\admin\tables\dynamic\Interface_Dynamic_Table;
use bpmj\wpidea\admin\tables\dynamic\module\Dynamic_Tables_Module;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\view\Admin_View_Names;
use bpmj\wpidea\view\Interface_View_Provider;

class Price_History_Page_Renderer extends Abstract_Page_Renderer
{
    private Interface_View_Provider $view_provider;
    private Interface_Translator $translator;
    private Dynamic_Tables_Module $dynamic_tables_module;
    private Price_History_Table_Config_Provider $config_provider;

    public function __construct(
        Interface_View_Provider $view_provider,
        Interface_Translator $translator,
        Dynamic_Tables_Module $dynamic_tables_module,
        Price_History_Table_Config_Provider $config_provider
    )
    {
        $this->view_provider = $view_provider;
        $this->translator = $translator;
        $this->dynamic_tables_module = $dynamic_tables_module;
        $this->config_provider = $config_provider;
    }

    public function get_rendered_page(): string
    {
        return $this->view_provider->get_admin(Admin_View_Names::PRICE_HISTORY_PAGE, [
            'table' => $this->prepare_table(),
            'page_title' => $this->translator->translate('price_history.page_title')
        ]);
    }

    private function prepare_table(): Interface_Dynamic_Table
    {
        return $this->dynamic_tables_module->create_table_from_config(
            $this->config_provider->get_config()
        );
    }
}