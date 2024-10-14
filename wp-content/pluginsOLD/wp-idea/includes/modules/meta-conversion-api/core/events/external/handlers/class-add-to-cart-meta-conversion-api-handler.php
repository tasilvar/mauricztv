<?php

namespace bpmj\wpidea\modules\meta_conversion_api\core\events\external\handlers;

use bpmj\wpidea\Current_Request;
use bpmj\wpidea\events\Event_Name;
use bpmj\wpidea\events\Interface_Event_Handler;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\modules\meta_conversion_api\core\services\Interface_Meta_Conversion_API_Sender;
use bpmj\wpidea\sales\product\api\Interface_Product_API;

class Add_To_Cart_Meta_Conversion_API_Handler implements Interface_Event_Handler
{
    private Interface_Events $events;
    private Interface_Meta_Conversion_API_Sender $meta_conversion_api_sender;
    private Interface_Product_API $product_api;
    private Current_Request $current_request;
    
    public function __construct(
        Interface_Events $events,
        Interface_Meta_Conversion_API_Sender $meta_conversion_api_sender,
        Interface_Product_API $product_api,
        Current_Request $current_request
    ) {
        $this->events = $events;
        $this->meta_conversion_api_sender = $meta_conversion_api_sender;
        $this->product_api = $product_api;
        $this->current_request = $current_request;
    }

    public function init(): void
    {
        if (!$this->is_current_request_from_link() && !$this->is_current_request_from_ajax()) {
            return;
        }

        $this->events->on(Event_Name::PRODUCT_ADDED_TO_CART, [$this, 'send_product_details'], 10, 1);
    }

    public function send_product_details(int $product_id): void
    {
        $product = $this->product_api->find($product_id);

        if (!$product) {
            return;
        }

        $this->meta_conversion_api_sender->send_data_for_add_to_cart_event($product);
    }

    private function is_current_request_from_link(): bool
    {
      return !empty($this->current_request->get_request_arg('add_to_cart'));
    }

    private function is_current_request_from_ajax(): bool
    {
        $wpi_route = $this->current_request->get_request_arg('wpi_route');

        return $wpi_route === 'payment/process_checkout';
    }
}
