<?php

namespace bpmj\wpidea\modules\opinions\core\collections;

use bpmj\wpidea\data_types\collection\Abstract_Iterator;
use bpmj\wpidea\modules\opinions\core\entities\Opinion;

class Opinion_Collection extends Abstract_Iterator
{
    public function add(Opinion $item): self
    {
        return $this->add_item($item);
    }

    public function current(): Opinion
    {
        return $this->get_current_item();
    }
}