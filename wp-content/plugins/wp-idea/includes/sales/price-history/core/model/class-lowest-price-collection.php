<?php

namespace bpmj\wpidea\sales\price_history\core\model;

use bpmj\wpidea\data_types\collection\Abstract_Iterator;

class Lowest_Price_Collection extends Abstract_Iterator
{
    public function add(Lowest_Price $item): self
    {
        return $this->add_item($item);
    }

    public function current(): Lowest_Price
    {
        return $this->get_current_item();
    }
}