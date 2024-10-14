<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\modules\meta_conversion_api\infrastructure\events;

use bpmj\wpidea\events\Interface_Event_Handler;
use bpmj\wpidea\modules\meta_conversion_api\core\events\external\handlers\{Add_To_Cart_Meta_Conversion_API_Handler,
    Initiate_Checkout_Meta_Conversion_API_Handler,
    Purchase_Meta_Conversion_API_Handler,
    Page_View_Meta_Conversion_API_Handler
};


class Event_Handlers_Initiator
{
    public function __construct(
        Initiate_Checkout_Meta_Conversion_API_Handler $initiate_checkout_meta_conversion_api_handler,
        Add_To_Cart_Meta_Conversion_API_Handler $add_to_cart_meta_conversion_api_handler,
        Purchase_Meta_Conversion_API_Handler $purchase_meta_conversion_api_handler,
        Page_View_Meta_Conversion_API_Handler $page_view_meta_conversion_api_handler
    ) {
        $this->init_handlers([
            $initiate_checkout_meta_conversion_api_handler,
            $add_to_cart_meta_conversion_api_handler,
            $purchase_meta_conversion_api_handler,
            $page_view_meta_conversion_api_handler
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