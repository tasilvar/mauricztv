<?php

namespace bpmj\wpidea\modules\meta_conversion_api\core\events\external\handlers;

use bpmj\wpidea\events\Event_Name;
use bpmj\wpidea\events\Interface_Event_Handler;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\modules\meta_conversion_api\core\services\Interface_Meta_Conversion_API_Sender;
use bpmj\wpidea\sales\order\api\Interface_Order_API;

class Purchase_Meta_Conversion_API_Handler implements Interface_Event_Handler
{
    private Interface_Events $events;
    private Interface_Meta_Conversion_API_Sender $meta_conversion_api_sender;
    private Interface_Order_API $order_api;

    public function __construct(
        Interface_Events $events,
        Interface_Meta_Conversion_API_Sender $meta_conversion_api_sender,
        Interface_Order_API $order_api
    ) {
        $this->events = $events;
        $this->meta_conversion_api_sender = $meta_conversion_api_sender;
        $this->order_api = $order_api;
    }

    public function init(): void
    {
        $this->events->on(Event_Name::ORDER_CREATED, [$this, 'send_order_details'], 10, 1);
    }

    public function send_order_details(int $payment_id): void
    {
        $order = $this->order_api->find($payment_id);

        if (!$order) {
            return;
        }

        $this->meta_conversion_api_sender->send_data_for_purchase_event($order);
    }
}
