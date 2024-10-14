<?php

namespace bpmj\wpidea\admin\menu;

class Admin_Menu_Items_Collection extends \ArrayObject
{
    public function offsetSet($index, $newval)
    {
        if (!($newval instanceof Admin_Menu_Item)) {
            throw new \InvalidArgumentException("Item must be an instance of the Admin_Menu_Item class");
        }

        parent::offsetSet($index, $newval);
    }

    public function find_item_by_slug(string $slug): ?Admin_Menu_Item
    {
        $matches = array_filter($this->getArrayCopy(), function($item) use ($slug){
            return $item->get_menu_slug() == $slug;
        });

        if(empty($matches)) return null;

        return reset($matches);
    }
}
