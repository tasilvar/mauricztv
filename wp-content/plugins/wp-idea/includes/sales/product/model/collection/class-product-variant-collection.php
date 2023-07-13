<?php

namespace bpmj\wpidea\sales\product\model\collection;

use Iterator;
use Countable;
use bpmj\wpidea\sales\product\model\Product_Variant;

class Product_Variant_Collection implements Iterator, Countable
{
    private array $variants = [];
    private int $current_position = 0;

    public function count(): int
    {
        return count($this->variants);
    }

    public function add(Product_Variant $variant): self
    {
        $this->variants[] = $variant;

        return $this;
    }

    public function current(): Product_Variant
    {
        return $this->variants[$this->current_position];
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
        return isset($this->variants[$this->current_position]);
    }

    public function rewind(): void
    {
        $this->current_position = 0;
    }

    public function get_first(): ?Product_Variant
    {
        return $this->variants[array_key_first($this->variants)] ?? null;
    }
}
