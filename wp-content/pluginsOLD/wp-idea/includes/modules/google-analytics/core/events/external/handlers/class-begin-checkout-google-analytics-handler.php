<?php

namespace bpmj\wpidea\modules\google_analytics\core\events\external\handlers;

use bpmj\wpidea\events\Event_Name;
use bpmj\wpidea\events\Interface_Event_Handler;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\modules\cart\api\Cart_API;
use bpmj\wpidea\modules\google_analytics\api\Google_Analytics_API;
use bpmj\wpidea\modules\google_analytics\core\providers\Event_Data_Provider;
use bpmj\wpidea\modules\google_analytics\core\services\Interface_Data_Session_Setter;

class Begin_Checkout_Google_Analytics_Handler implements Interface_Event_Handler
{
    private Interface_Events $events;
    private Cart_API $cart_api;
    private Google_Analytics_API $google_analytics_api;
    private Event_Data_Provider $event_data_provider;
    private Interface_Data_Session_Setter $data_session_setter;

    public function __construct(
        Interface_Events $events,
        Cart_API $cart_api,
        Google_Analytics_API $google_analytics_api,
        Event_Data_Provider $event_data_provider,
        Interface_Data_Session_Setter $data_session_setter
    ) {
        $this->events = $events;
        $this->cart_api = $cart_api;
        $this->google_analytics_api = $google_analytics_api;
        $this->event_data_provider = $event_data_provider;
        $this->data_session_setter = $data_session_setter;
    }

    public function init(): void
    {
        $this->events->on(Event_Name::CHECKOUT_INITIATED, [$this, 'event_begin_checkout']);
    }

    public function event_begin_checkout(): void
    {
        if (!$this->google_analytics_api->is_ga4_enabled()) {
            return;
        }

        if ($this->cart_api->get_cart_content()->is_empty()) {
            return;
        }

        $event = $this->event_data_provider->get_event_for_begin_checkout($this->cart_api);

        $this->data_session_setter->add_event_to_session($event);
    }
}
