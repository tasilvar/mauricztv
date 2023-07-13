<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\sales\discount_codes\core\collections;

use bpmj\wpidea\sales\discount_codes\core\entities\Discount;
use Iterator;

class Discount_Collection implements Iterator
{
    private array $items = [];

    private int $current_position = 0;

    public static function create(): self
    {
        return new self();
    }

    public function add(Discount $item): Discount_Collection
    {
        $this->items[] = $item;

        return $this;
    }

    public function current(): Discount
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

    public function first(): ?Discount
    {
        return $this->items[0] ?? null;
    }
}