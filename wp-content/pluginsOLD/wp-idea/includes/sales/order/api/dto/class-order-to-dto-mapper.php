<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\sales\order\api\dto;

use bpmj\wpidea\sales\order\Order;

class Order_To_DTO_Mapper
{
    public function map(Order $order): Order_DTO
    {
        return Order_DTO::create(
            $order->get_id(),
            $order->get_cart_content(),
            $order->get_client(),
            $order->get_invoice(),
            $order->get_total()
        );
    }
}