<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\admin\tables\dynamic\module;

use bpmj\wpidea\admin\tables\dynamic\config\Dynamic_Table_Config;
use bpmj\wpidea\admin\tables\dynamic\config\Dynamic_Table_Config_Provider_Registry;
use bpmj\wpidea\admin\tables\dynamic\controllers\Admin_Dynamic_Table_Ajax_Controller;
use bpmj\wpidea\admin\tables\dynamic\controllers\Admin_User_Table_Settings_Ajax_Controller;
use bpmj\wpidea\admin\tables\dynamic\controllers\Dynamic_Table_Export_Controller;
use bpmj\wpidea\admin\tables\dynamic\data\Interface_Dynamic_Table_Data_Provider;
use bpmj\wpidea\admin\tables\dynamic\Dynamic_Table;
use bpmj\wpidea\admin\tables\dynamic\Interface_Dynamic_Table_Factory;
use bpmj\wpidea\admin\tables\dynamic\module\exceptions\Dynamic_Tables_Module_Exception;
use bpmj\wpidea\routing\Router;
use bpmj\wpidea\user\User_Capability_Collection;

class Dynamic_Tables_Module
{
    private Dynamic_Table_Config_Provider_Registry $dynamic_table_config_registry;
    private Router $router;
    private Interface_Dynamic_Table_Factory $dynamic_table_factory;

    private bool $is_initialized = false;

    public function __construct(
        Dynamic_Table_Config_Provider_Registry $dynamic_table_config_registry,
        Router                                 $router,
        Interface_Dynamic_Table_Factory $dynamic_table_factory
    )
    {
        $this->dynamic_table_config_registry = $dynamic_table_config_registry;
        $this->router = $router;
        $this->dynamic_table_factory = $dynamic_table_factory;
    }

    public function init(): void
    {
        if($this->is_initialized) {
            throw new Dynamic_Tables_Module_Exception(Dynamic_Tables_Module_Exception::MESSAGE_MODULE_SHOULD_BE_INITIALIZED_ONLY_ONCE);
        }

        $this->dynamic_table_config_registry->set_up();

        $this->register_controllers();
    }

    public function create_table_config(
        string $table_id,
        Interface_Dynamic_Table_Data_Provider $data_provider,
        array $columns_config
    ): Dynamic_Table_Config
    {
        return new Dynamic_Table_Config($table_id, $data_provider, $columns_config);
    }

    private function register_controllers(): void
    {
        $this->router->register_controller('admin/dynamic_table_ajax', Admin_Dynamic_Table_Ajax_Controller::class);
        $this->router->register_controller('admin/user_table_settings_ajax', Admin_User_Table_Settings_Ajax_Controller::class);
    }

    public function create_table_from_config(Dynamic_Table_Config $config): Dynamic_Table
    {
        return $this->dynamic_table_factory->create($config);
    }
}