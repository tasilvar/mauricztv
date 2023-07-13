<?php

namespace bpmj\wpidea\modules\google_analytics\core\events\external\handlers;

use bpmj\wpidea\events\Event_Name;
use bpmj\wpidea\events\Interface_Event_Handler;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\modules\google_analytics\api\Google_Analytics_API;
use bpmj\wpidea\modules\google_analytics\core\providers\Event_Data_Provider;
use bpmj\wpidea\modules\google_analytics\core\services\Interface_Data_Session_Setter;
use bpmj\wpidea\sales\order\api\Interface_Order_API;

class Purchase_Google_Analytics_Handler implements Interface_Event_Handler
{
    private Interface_Events $events;
    private Google_Analytics_API $google_analytics_api;
    private Event_Data_Provider $event_data_provider;
    private Interface_Data_Session_Setter $data_session_setter;
    private Interface_Order_API $order_api;

    public function __construct(
        Interface_Events $events,
        Google_Analytics_API $google_analytics_api,
        Event_Data_Provider $event_data_provider,
        Interface_Data_Session_Setter $data_session_setter,
        Interface_Order_API $order_api
    ) {
        $this->events = $events;
        $this->google_analytics_api = $google_analytics_api;
        $this->event_data_provider = $event_data_provider;
        $this->data_session_setter = $data_session_setter;
        $this->order_api = $order_api;
    }

    public function init(): void
    {
        $this->events->on(Event_Name::ORDER_CREATED, [$this, 'event_complete_purchase']);
    }

    public function event_complete_purchase(int $payment_id): void
    {
        if (!$this->google_analytics_api->is_ga4_enabled()) {
            return;
        }

        $order = $this->order_api->find($payment_id);

        if (!$order) {
            return;
        }

        $event = $this->event_data_provider->get_event_for_purchase($order);

        $this->data_session_setter->add_event_to_session($event);
    }
}
