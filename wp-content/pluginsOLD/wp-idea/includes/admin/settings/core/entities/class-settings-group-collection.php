<?php

namespace bpmj\wpidea\admin\settings\core\entities;

use Iterator;

class Settings_Group_Collection implements Iterator
{
    private int $current_position = 0;

    private array $groups = [];

    public function add(Abstract_Settings_Group $group): self
    {
        $this->groups[] = $group;

        return $this;
    }

    public function size(): int
    {
        return sizeof($this->groups);
    }

    public function get_first(): Abstract_Settings_Group
    {
        return $this->groups[array_key_first($this->groups)];
    }

    public function current(): Abstract_Settings_Group
    {
        return $this->groups[$this->current_position];
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
        return isset($this->groups[$this->current_position]);
    }

    public function rewind(): void
    {
        $this->current_position = 0;
    }
}
