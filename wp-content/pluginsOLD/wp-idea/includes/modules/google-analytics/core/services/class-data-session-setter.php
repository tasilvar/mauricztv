<?php

namespace bpmj\wpidea\modules\google_analytics\core\services;

use bpmj\wpidea\modules\google_analytics\core\collections\Event_Item_Collection;
use bpmj\wpidea\modules\google_analytics\core\entities\Event;
use bpmj\wpidea\modules\google_analytics\Google_Analytics_Module;

class Data_Session_Setter implements Interface_Data_Session_Setter
{
    public function add_event_to_session(Event $event): void
    {
        $content = $this->event_model_to_array($event);


        $_SESSION[Google_Analytics_Module::PARAM_SESSION_NAME][] = [
            'type' => $event->get_event_name(),
            'content' => $content,
        ];
    }

    private function event_model_to_array(Event $event): array
    {
        $items = $this->event_items_to_array($event->get_items());

        return [
            'currency' => $event->get_currency(),
            'value' => $event->get_value(),
            'items' => $items
        ];
    }

    private function event_items_to_array(Event_Item_Collection $event_items): array
    {
        $items = [];

        foreach ($event_items->to_array() as $event_item) {
            $items[] = [
                'item_id' => $event_item->get_item_id(),
                'item_name' => $event_item->get_item_name(),
                'item_variant' => $event_item->get_item_variant(),
                'quantity' => $event_item->get_quantity(),
                'discount' => $event_item->get_discount() ?? '',
                'price' => $event_item->get_price()
            ];
        }

        return $items;
    }
}
