<?php

namespace bpmj\wpidea\modules\meta_conversion_api\core\events\external\handlers;

use bpmj\wpidea\events\Event_Name;
use bpmj\wpidea\events\Interface_Event_Handler;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\modules\cart\api\Cart_API;
use bpmj\wpidea\modules\meta_conversion_api\core\services\Interface_Meta_Conversion_API_Sender;

class Initiate_Checkout_Meta_Conversion_API_Handler implements Interface_Event_Handler
{
    private Interface_Events $events;
    private Interface_Meta_Conversion_API_Sender $meta_conversion_api_sender;
    private Cart_API $cart_api;

    public function __construct(
        Interface_Events $events,
        Interface_Meta_Conversion_API_Sender $meta_conversion_api_sender,
        Cart_API $cart_api
    ) {
        $this->events = $events;
        $this->meta_conversion_api_sender = $meta_conversion_api_sender;
        $this->cart_api = $cart_api;
    }

    public function init(): void
    {
        $this->events->on(Event_Name::CHECKOUT_INITIATED, [$this, 'send_order_details']);
    }

    public function send_order_details(): void
    {
        $this->meta_conversion_api_sender->send_data_for_initiate_checkout_event($this->cart_api);
    }
}
