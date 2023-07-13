<?php

namespace bpmj\wpidea\admin\tables;

use ArrayObject;
use InvalidArgumentException;

class Enhanced_Table_Items_Collection extends ArrayObject
{
    private $items_class;

    private $total_items = 0;

    public const EXCEPTION_INVALID_ARGUMENT = "Item must be an instance of the Abstract_Enhanced_Table_Item class";
    public const EXCEPTION_MIXED_ITEMS = "All of the items in the collection must be of the same class!";

    public function offsetSet($index, $newval): void
    {
        if (!($newval instanceof Abstract_Enhanced_Table_Item)) {
            throw new InvalidArgumentException(self::EXCEPTION_INVALID_ARGUMENT);
        }

        if (empty($this->items_class)) $this->items_class = get_class($newval);

        if(!($newval instanceof $this->items_class)) {
            throw new InvalidArgumentException(self::EXCEPTION_MIXED_ITEMS);
        }

        parent::offsetSet($index, $newval);
    }

    public function set_total(int $total): self
    {
        $this->total_items = $total;

        return $this;
    }

    public function get_total(): int
    {
        return $this->total_items;
    }
}