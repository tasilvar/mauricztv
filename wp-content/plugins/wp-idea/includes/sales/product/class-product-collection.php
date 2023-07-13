<?php namespace bpmj\wpidea\sales\product;

use bpmj\wpidea\sales\product\model\Product;
use Iterator;

class Product_Collection implements Iterator
{
    private $products = [];
    private $current_position = 0;

    public function add(Product $product): Product_Collection
    {
        $this->products[] = $product;

        return $this;
    }

    public function to_array(): array
    {
        $result = [];
        foreach ($this->products as $product) {
            $result[] = (new Product_Presenter($product))->to_array();
        }

        return $result;
    }

    public function current(): Product
    {
        return $this->products[$this->current_position];
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
        return isset($this->products[$this->current_position]);
    }

    public function rewind(): void
    {
        $this->current_position = 0;
    }

    public function is_empty(): bool
    {
        return empty($this->products);
    }

    public function map(\Closure $closure): array
    {
        return array_map(
            $closure,
            iterator_to_array($this)
        );
    }
}
