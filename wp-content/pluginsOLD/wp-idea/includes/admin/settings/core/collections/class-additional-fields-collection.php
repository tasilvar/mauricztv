<?php

namespace bpmj\wpidea\admin\settings\core\collections;

use Iterator;
use bpmj\wpidea\admin\settings\core\entities\fields\Abstract_Setting_Field;

class Additional_Fields_Collection implements Iterator
{
    private array $items = [];

    private int $current_position = 0;

    public function add(Abstract_Setting_Field $item): self
    {
        $this->items[] = $item;

        return $this;
    }

    public function current(): Abstract_Setting_Field
    {
        return $this->items[$this->current_position];
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
        return isset($this->items[$this->current_position]);
    }

    public function rewind(): void
    {
        $this->current_position = 0;
    }
}