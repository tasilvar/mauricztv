<?php

namespace bpmj\wpidea\modules\google_analytics\core\events\external\handlers;

use bpmj\wpidea\events\Event_Name;
use bpmj\wpidea\events\Interface_Event_Handler;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\modules\google_analytics\api\Google_Analytics_API;
use bpmj\wpidea\modules\google_analytics\core\providers\Event_Data_Provider;
use bpmj\wpidea\modules\google_analytics\core\services\Interface_Data_Session_Setter;
use bpmj\wpidea\sales\product\api\Interface_Product_API;

class View_Item_Google_Analytics_Handler implements Interface_Event_Handler
{
    private Interface_Events $events;
    private Current_Request $current_requests;
    private Interface_Product_API $product_api;
    private Google_Analytics_API $google_analytics_api;
    private Event_Data_Provider $event_data_provider;
    private Interface_Data_Session_Setter $data_session_setter;

    public function __construct(
        Interface_Events $events,
        Current_Request $current_requests,
        Interface_Product_API $product_api,
        Google_Analytics_API $google_analytics_api,
        Event_Data_Provider $event_data_provider,
        Interface_Data_Session_Setter $data_session_setter
    ) {
        $this->events = $events;
        $this->current_requests = $current_requests;
        $this->product_api = $product_api;
        $this->google_analytics_api = $google_analytics_api;
        $this->event_data_provider = $event_data_provider;
        $this->data_session_setter = $data_session_setter;
    }

    public function init(): void
    {
        $this->events->on(Event_Name::PAGE_VIEWED, [$this, 'event_view_item']);
    }

    public function event_view_item(): void
    {
        if (!$this->google_analytics_api->is_ga4_enabled()) {
            return;
        }

        $page_id = $this->current_requests->get_current_page_id();

        if (!$page_id) {
            return;
        }

        $product = $this->product_api->find($page_id);

        if (!$product) {
            return;
        }

        $event = $this->event_data_provider->get_event_for_view_item($product);

        $this->data_session_setter->add_event_to_session($event);
    }
}
