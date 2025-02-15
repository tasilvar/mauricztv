<?php

namespace bpmj\wpidea\modules\google_analytics\core\events\external\handlers;

use bpmj\wpidea\events\Event_Name;
use bpmj\wpidea\events\Interface_Event_Handler;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\modules\google_analytics\api\Google_Analytics_API;
use bpmj\wpidea\modules\google_analytics\core\providers\Event_Data_Provider;
use bpmj\wpidea\modules\google_analytics\core\services\Interface_Data_Session_Setter;
use bpmj\wpidea\sales\product\api\Interface_Product_API;

class Add_To_Cart_From_Link_Google_Analytics_Handler implements Interface_Event_Handler
{
    private Interface_Events $events;
    private Interface_Product_API $product_api;
    private Google_Analytics_API $google_analytics_api;
    private Event_Data_Provider $event_data_provider;
    private Interface_Data_Session_Setter $data_session_setter;


    public function __construct(
        Interface_Events $events,
        Interface_Product_API $product_api,
        Google_Analytics_API $google_analytics_api,
        Event_Data_Provider $event_data_provider,
        Interface_Data_Session_Setter $data_session_setter
    ) {
        $this->events = $events;
        $this->product_api = $product_api;
        $this->google_analytics_api = $google_analytics_api;
        $this->event_data_provider = $event_data_provider;
        $this->data_session_setter = $data_session_setter;
    }

    public function init(): void
    {
        $this->events->on(Event_Name::PRODUCT_ADDED_TO_CART_FROM_LINK, [$this, 'event_add_to_cart'], 10, 2);
    }

    public function event_add_to_cart(int $product_id, array $options): void
    {
        if (!$this->google_analytics_api->is_ga4_enabled()) {
            return;
        }

        if (!$product_id) {
            return;
        }

        $product = $this->product_api->find($product_id);

        if (!$product) {
            return;
        }

        $variant_id = !empty($options['price_id']) ? (int)$options['price_id'] : null;

        $event = $this->event_data_provider->get_event_for_add_to_cart($product, $variant_id);

        $this->data_session_setter->add_event_to_session($event);    
    }
}
