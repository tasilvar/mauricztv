<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\admin\tables\dynamic\config;

use Iterator;

class Dynamic_Table_Config_Collection implements Iterator
{
    private int $current_position = 0;

    private array $configs = [];

    public function add(Interface_Dynamic_Table_Config_Provider $provider): self
    {
        $this->configs[$provider->get_table_id()] = $provider;

        return $this;
    }

    public function current(): Interface_Dynamic_Table_Config_Provider
    {
        return array_values($this->configs)[$this->current_position];
    }

    public function next(): void
    {
        ++$this->current_position;
    }

    public function key(): int
    {
        return $this->current_position;
    }

    public function valid(): bool
    {
        return isset(array_values($this->configs)[$this->current_position]);
    }

    public function rewind(): void
    {
        $this->current_position = 0;
    }

    public function get_by_config_id(string $config_id): ?Interface_Dynamic_Table_Config_Provider
    {
        return $this->configs[$config_id] ?? null;
    }
}