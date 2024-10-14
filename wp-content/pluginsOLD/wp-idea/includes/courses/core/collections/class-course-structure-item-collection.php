<?php
declare(strict_types=1);

namespace bpmj\wpidea\courses\core\collections;

use bpmj\wpidea\courses\core\entities\Course_Structure_Item;
use bpmj\wpidea\data_types\collection\Abstract_Iterator;

class Course_Structure_Item_Collection extends Abstract_Iterator
{
    public function current(): Course_Structure_Item
    {
        return $this->get_current_item();
    }

    public function add(Course_Structure_Item $item): self
    {
        return $this->add_item($item);
    }
}