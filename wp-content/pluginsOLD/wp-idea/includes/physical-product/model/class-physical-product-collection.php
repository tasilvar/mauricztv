<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\physical_product\model;

use Iterator;

class Physical_Product_Collection implements Iterator
{
    private array $items = [];

    private int $current_position = 0;

    public function add(Physical_Product $item): self
    {
        $this->items[] = $item;

        return $this;
    }

    public function current(): Physical_Product
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

    public function first(): ?Physical_Product
    {
        return $this->items[0] ?? null;
    }
}
