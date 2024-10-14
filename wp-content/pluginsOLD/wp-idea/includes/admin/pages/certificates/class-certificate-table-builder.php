<?php namespace bpmj\wpidea\admin\pages\certificates;

use bpmj\wpidea\admin\tables\dynamic\Interface_Dynamic_Table;
use bpmj\wpidea\admin\tables\dynamic\module\Dynamic_Tables_Module;

class Certificate_Table_Builder
{
    private Certificates_Table_Config_Provider $config_provider;
    private Dynamic_Tables_Module $dynamic_tables_module;

    public function __construct(
        Certificates_Table_Config_Provider $config_provider,
        Dynamic_Tables_Module $dynamic_tables_module
    ) {
        $this->config_provider = $config_provider;
        $this->dynamic_tables_module = $dynamic_tables_module;
    }

    public function get_table(): Interface_Dynamic_Table
    {
        return $this->dynamic_tables_module->create_table_from_config(
            $this->config_provider->get_config()
        );
    }
}