<?php

declare(strict_types=1);

namespace bpmj\wpidea\app\bundles;

use Iterator;

class Bundle_Item_Display_Model_Collection implements Iterator
{
    private array $items = [];

    private int $current_position = 0;

    public function add(Bundle_Item_Display_Model $item): self
    {
        $this->items[] = $item;

        return $this;
    }

    public function current(): Bundle_Item_Display_Model
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
