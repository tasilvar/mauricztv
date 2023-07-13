<?php

namespace bpmj\wpidea\sales\price_history\core\event;

use bpmj\wpidea\events\Interface_Event_Handler;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\sales\price_history\core\provider\Interface_Price_History_Provider;
use bpmj\wpidea\sales\price_history\core\provider\Interface_Product_Data_Provider;
use bpmj\wpidea\sales\product\core\event\Event_Name;

class Price_Deleted_Events_Handler implements Interface_Event_Handler
{
    private Interface_Events $events;
    private Interface_Price_History_Provider $price_history_provider;
    private Interface_Product_Data_Provider $product_data_provider;

    public function __construct(
        Interface_Events $events,
        Interface_Price_History_Provider $price_history_provider,
        Interface_Product_Data_Provider $product_data_provider
    ) {
        $this->events = $events;
        $this->price_history_provider = $price_history_provider;
        $this->product_data_provider = $product_data_provider;
    }

    public function init(): void
    {
        $this->events->on(Event_Name::PRODUCT_DELETED, [$this, 'handle_price_deleted']);
    }

    public function handle_price_deleted(int $product_id): void
    {
        if (!$product_id) {
            return;
        }

        $this->price_history_provider->delete_by_product($product_id, null);
    }
}