<?php

namespace bpmj\wpidea\sales\price_history\core\model;

use bpmj\wpidea\data_types\collection\Abstract_Iterator;

class Price_History extends Abstract_Iterator
{
    public function add(Historic_Price $item): self
    {
        return $this->add_item($item);
    }

    public function current(): Historic_Price
    {
        return $this->get_current_item();
    }
}