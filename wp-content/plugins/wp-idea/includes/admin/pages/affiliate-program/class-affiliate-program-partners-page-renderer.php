<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\affiliate_program;

use bpmj\wpidea\admin\renderers\Abstract_Page_Renderer;
use bpmj\wpidea\view\Interface_View_Provider;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\admin\tables\dynamic\module\Dynamic_Tables_Module;
use bpmj\wpidea\admin\tables\dynamic\Dynamic_Table;

class Affiliate_Program_Partners_Page_Renderer extends Abstract_Page_Renderer
{
    private Interface_View_Provider $view_provider;
    private Interface_Translator $translator;
    private Dynamic_Tables_Module $dynamic_tables_module;
    private Affiliate_Program_Partners_Table_Config_Provider $config_provider;

    public function __construct(
        Interface_View_Provider $view_provider,
        Interface_Translator $translator,
        Dynamic_Tables_Module $dynamic_tables_module,
        Affiliate_Program_Partners_Table_Config_Provider $config_provider
    ) {
        $this->view_provider = $view_provider;
        $this->translator = $translator;
        $this->dynamic_tables_module = $dynamic_tables_module;
        $this->config_provider = $config_provider;
    }

    public function get_rendered_page(): string
    {
        return $this->view_provider->get_admin('/pages/affiliate-program/partners', [
            'page_title' => $this->translator->translate('affiliate_program.partners_menu_title'),
            'table' => $this->prepare_table()
        ]);
    }

    private function prepare_table(): Dynamic_Table
    {
        return $this->dynamic_tables_module->create_table_from_config(
            $this->config_provider->get_config()
        );
    }
}
