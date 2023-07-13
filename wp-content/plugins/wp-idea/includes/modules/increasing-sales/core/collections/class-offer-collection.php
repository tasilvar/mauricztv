<?php
declare(strict_types=1);

namespace bpmj\wpidea\modules\increasing_sales\core\collections;

use bpmj\wpidea\data_types\collection\Abstract_Iterator;
use bpmj\wpidea\modules\increasing_sales\core\entities\Offer;

class Offer_Collection extends Abstract_Iterator
{
    public function current(): Offer
    {
        return $this->get_current_item();
    }
}