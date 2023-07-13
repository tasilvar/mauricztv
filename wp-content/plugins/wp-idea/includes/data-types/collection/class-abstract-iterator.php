<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\data_types\collection;

use Iterator;

abstract class Abstract_Iterator implements Iterator
{
    private array $items = [];
    private int $current_position = 0;

    private function __construct()
    {
    }

    /**
     * @return static
     */
    public static function create(): self
    {
        return new static();
    }

    /**
     * @return static
     */
    public static function create_from_array(array $items): self
    {
        $collection = new static();

        foreach ($items as $item) {
            $collection->add_item($item);
        }

        return $collection;
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

    public function is_empty(): bool
    {
        return empty($this->items);
    }

    public function map(\Closure $closure): array
    {
        return array_map(
            $closure,
            iterator_to_array($this)
        );
    }

    public function to_array(): array
    {
        return $this->items;
    }

    protected function get_current_item()
    {
        return $this->items[$this->current_position];
    }

    protected function add_item($item): self
    {
        $this->items[] = $item;

        return $this;
    }
}