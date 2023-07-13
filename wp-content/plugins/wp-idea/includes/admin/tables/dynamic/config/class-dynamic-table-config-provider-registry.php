<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\admin\tables\dynamic\config;

use bpmj\wpidea\admin\tables\dynamic\config\exceptions\Config_Registry_Exception;

class Dynamic_Table_Config_Provider_Registry
{
    private Dynamic_Table_Config_Collection $collection;

    public function set_up(): void
    {
        if (isset($this->collection)) {
            throw new Config_Registry_Exception(Config_Registry_Exception::MESSAGE_REGISTRY_SHOULD_BE_SET_UP_ONLY_ONCE);
        }

        $this->collection = new Dynamic_Table_Config_Collection();
    }

    public function register_provider(Interface_Dynamic_Table_Config_Provider $config_provider): void
    {
        $this->validate_integrity();

        $this->collection->add($config_provider);
    }

    public function get_provider_by_table_id(string $config_id): ?Interface_Dynamic_Table_Config_Provider
    {
        $this->validate_integrity();

        return $this->collection->get_by_config_id($config_id);
    }

    private function validate_integrity(): void
    {
        if (!isset($this->collection)) {
            throw new Config_Registry_Exception(Config_Registry_Exception::MESSAGE_REGISTRY_IS_NOT_SET_UP);
        }
    }
}