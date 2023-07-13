<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\digital_products\model;

use bpmj\wpidea\sales\product\model\Product;
use Iterator;

class Digital_Product_Collection implements Iterator
{
    private array $digital_products = [];

    private int $current_position = 0;

    public function add(Digital_Product $product): Digital_Product_Collection
    {
        $this->digital_products[] = $product;

        return $this;
    }

    public function current(): Digital_Product
    {
        return $this->digital_products[$this->current_position];
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
        return isset($this->digital_products[$this->current_position]);
    }

    public function rewind(): void
    {
        $this->current_position = 0;
    }

    public function first(): ?Digital_Product
    {
        return $this->digital_products[0] ?? null;
    }
}
