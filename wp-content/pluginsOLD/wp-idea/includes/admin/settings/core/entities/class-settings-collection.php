<?php

namespace bpmj\wpidea\admin\settings\core\entities;

use bpmj\wpidea\admin\settings\core\entities\fields\Abstract_Setting_Field;
use Iterator;

class Settings_Collection implements Iterator
{
    private int $current_position = 0;

    private array $fields = [];

    public function add(Abstract_Setting_Field $field): self
    {
        $this->fields[] = $field;

        return $this;
    }

    public function size(): int
    {
        return sizeof($this->fields);
    }

    public function get_first(): Abstract_Setting_Field
    {
        return $this->fields[array_key_first($this->fields)];
    }

    public function current(): Abstract_Setting_Field
    {
        return $this->fields[$this->current_position];
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
        return isset($this->fields[$this->current_position]);
    }

    public function rewind(): void
    {
        $this->current_position = 0;
    }
}