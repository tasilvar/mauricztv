<?php
declare(strict_types = 1);

namespace bpmj\wpidea\sales\order;

class Order_Collection implements \IteratorAggregate
{
    public $items ;

    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    public function getIterator()
    {
        return yield from $this->items;
    }

    public function get_first(): ?Order
    {
        return $this->items[array_key_first($this->items)] ?? null;
    }
}