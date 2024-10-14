<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\modules\google_analytics\infrastructure\events;

use bpmj\wpidea\events\Interface_Event_Handler;
use bpmj\wpidea\modules\google_analytics\core\events\external\handlers\{
    Begin_Checkout_Google_Analytics_Handler,
    Purchase_Google_Analytics_Handler,
    View_Item_Google_Analytics_Handler,
    Remove_From_Cart_Google_Analytics_Handler,
    Add_To_Cart_From_Link_Google_Analytics_Handler
};

class Event_Handlers_Initiator
{
    public function __construct(
        Remove_From_Cart_Google_Analytics_Handler $remove_from_cart_google_analytics_handler,
        Begin_Checkout_Google_Analytics_Handler $begin_checkout_google_analytics_handler,
        Purchase_Google_Analytics_Handler $purchase_google_analytics_handler,
        View_Item_Google_Analytics_Handler $view_item_google_analytics_handler,
        Add_To_Cart_From_Link_Google_Analytics_Handler $add_to_cart_from_link_google_analytics_handler
    ) {
        $this->init_handlers([
            $remove_from_cart_google_analytics_handler,
            $begin_checkout_google_analytics_handler,
            $purchase_google_analytics_handler,
            $view_item_google_analytics_handler,
            $add_to_cart_from_link_google_analytics_handler
        ]);
    }

    private function init_handler(Interface_Event_Handler $handler): void
    {
        $handler->init();
    }

    private function init_handlers(array $handlers): void
    {
        foreach ($handlers as $handler) {
            $this->init_handler($handler);
        }
    }
}