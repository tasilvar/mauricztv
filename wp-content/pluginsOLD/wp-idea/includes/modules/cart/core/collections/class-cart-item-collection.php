<?php
declare(strict_types=1);

namespace bpmj\wpidea\modules\cart\core\collections;

use bpmj\wpidea\data_types\collection\Abstract_Iterator;
use bpmj\wpidea\modules\cart\core\entities\Cart_Item;

class Cart_Item_Collection extends Abstract_Iterator
{
    public function current(): Cart_Item
    {
        return $this->get_current_item();
    }
}