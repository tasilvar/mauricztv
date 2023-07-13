<?php
declare(strict_types=1);

namespace bpmj\wpidea\modules\affiliate_program\core\entities;

use bpmj\wpidea\data_types\collection\Abstract_Iterator;

class External_Landing_Link_Collection extends Abstract_Iterator
{
    public function add(External_Landing_Link $item): self
    {
        return $this->add_item($item);
    }

    public function current(): External_Landing_Link
    {
        return $this->get_current_item();
    }
}
