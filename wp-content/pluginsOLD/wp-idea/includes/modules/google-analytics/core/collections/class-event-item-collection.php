<?php
declare(strict_types=1);

namespace bpmj\wpidea\modules\google_analytics\core\collections;

use bpmj\wpidea\data_types\collection\Abstract_Iterator;
use bpmj\wpidea\modules\google_analitycs\core\entities\Event_Item;

class Event_Item_Collection extends Abstract_Iterator
{
    public function current(): Event_Item
    {
        return $this->get_current_item();
    }
}