<?php

namespace bpmj\wpidea\modules\cart;

use bpmj\wpidea\modules\cart\api\Cart_API;
use bpmj\wpidea\modules\cart\api\Cart_API_Static_Helper;
use bpmj\wpidea\shared\abstractions\modules\Interface_Module;
use bpmj\wpidea\modules\cart\infrastructure\filters\Filter_Handlers_Initiator;
use bpmj\wpidea\modules\cart\infrastructure\events\Event_Handlers_Initiator;

 class Cart_Module implements Interface_Module
 {
     private Cart_API $cart_api;
     private Filter_Handlers_Initiator $filter_handlers_initiator;
     private Event_Handlers_Initiator $event_handlers_initiator;

     public function __construct(
        Cart_API $cart_api,
        Filter_Handlers_Initiator $filter_handlers_initiator,
        Event_Handlers_Initiator $event_handlers_initiator
     ) {
         $this->cart_api = $cart_api;
        $this->filter_handlers_initiator = $filter_handlers_initiator;
        $this->event_handlers_initiator = $event_handlers_initiator;
     }

    public function init(): void
    {
        Cart_API_Static_Helper::init($this->cart_api);
    }

    public function get_routes(): array
    {
        return [];
    }

    public function get_translations(): array
    {
        return [];
    }
}
