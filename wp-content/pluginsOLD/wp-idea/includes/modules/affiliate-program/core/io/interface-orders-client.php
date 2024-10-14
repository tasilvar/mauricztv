<?php

namespace bpmj\wpidea\modules\affiliate_program\core\io;

use bpmj\wpidea\modules\affiliate_program\core\value_objects\Commission_Rate;
use bpmj\wpidea\modules\affiliate_program\core\entities\Order;

interface Interface_Orders_Client
{
    public function get_order(int $order_id): ?Order;
}