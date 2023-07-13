<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\common;

use bpmj\wpidea\admin\tables\dynamic\module\Dynamic_Tables_Module;
use bpmj\wpidea\instantiator\Interface_Initiable;

class Admin_Dynamic_Tables_Initiator implements Interface_Initiable
{
    private Dynamic_Tables_Module $dynamic_tables_module;

    private Dynamic_Table_Configs_Registrator $dynamic_table_configs_registrator;

    public function __construct(
        Dynamic_Tables_Module $dynamic_tables_module,
        Dynamic_Table_Configs_Registrator $dynamic_table_configs_registrator
    )
    {
        $this->dynamic_tables_module = $dynamic_tables_module;
        $this->dynamic_table_configs_registrator = $dynamic_table_configs_registrator;
    }

    public function init(): void
    {
        $this->dynamic_tables_module->init();
        $this->dynamic_table_configs_registrator->init();
    }
}