<?php

namespace bpmj\wpidea\modules\google_analytics\core\events\external\handlers;

use bpmj\wpidea\events\Event_Name;
use bpmj\wpidea\events\Interface_Event_Handler;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\modules\cart\api\Cart_API;
use bpmj\wpidea\modules\google_analytics\api\Google_Analytics_API;
use bpmj\wpidea\modules\google_analytics\core\providers\Event_Data_Provider;
use bpmj\wpidea\modules\google_analytics\core\services\Interface_Data_Session_Setter;
use bpmj\wpidea\sales\product\api\Interface_Product_API;

class Remove_From_Cart_Google_Analytics_Handler implements Interface_Event_Handler
{
    private Interface_Events $events;
    private Google_Analytics_API $google_analytics_api;
    private Event_Data_Provider $event_data_provider;
    private Interface_Data_Session_Setter $data_session_setter;
    private Cart_API $cart_api;
    private Interface_Product_API $product_api;

    public function __construct(
        Interface_Events $events,
        Google_Analytics_API $google_analytics_api,
        Event_Data_Provider $event_data_provider,
        Interface_Data_Session_Setter $data_session_setter,
        Cart_API $cart_api,
        Interface_Product_API $product_api
    ) {
        $this->events = $events;
        $this->google_analytics_api = $google_analytics_api;
        $this->event_data_provider = $event_data_provider;
        $this->data_session_setter = $data_session_setter;
        $this->cart_api = $cart_api;
        $this->product_api = $product_api;
    }

    public function init(): void
    {
        $this->events->on(Event_Name::PRODUCT_REMOVAL_FROM_CART_REQUESTED, [$this, 'event_remove_from_cart']);
    }

    public function event_remove_from_cart($cart_key): void
    {
        if (!$this->google_analytics_api->is_ga4_enabled()) {
            return;
        }

        $cart_content = $this->cart_api->get_cart_content()->to_array();

        if (!isset($cart_content[$cart_key])) {
            return;
        }
        $cart_item = $cart_content[$cart_key];

        $product_id = $cart_item->get_item_product_id();
        $price_id = $cart_item->get_item_price_id();

        $product = $this->product_api->find($product_id->to_int());

        if (!$product) {
            return;
        }

        $variant_id = $price_id ? $price_id->to_int() : null;

        $event = $this->event_data_provider->get_event_for_remove_from_cart($product, $variant_id);

        $this->data_session_setter->add_event_to_session($event);
    }
}
