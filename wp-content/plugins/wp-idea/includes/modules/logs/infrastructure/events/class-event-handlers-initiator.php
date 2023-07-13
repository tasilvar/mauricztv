<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\modules\logs\infrastructure\events;

use bpmj\wpidea\events\Interface_Event_Handler;
use bpmj\wpidea\modules\logs\core\events\external\handlers\{Exception_Caught_Handler,
	Field_Edit_Handler,
	Page_Edit_Handler,
	Plugin_Management_Handler,
	Product_Deleted_Handler};

class Event_Handlers_Initiator
{
    public function __construct(
        Plugin_Management_Handler $plugin_management_handler,
        Field_Edit_Handler $field_edit_handler,
		Page_Edit_Handler $page_edit_handler,
	    Product_Deleted_Handler $product_deleted_handler,
        Exception_Caught_Handler $exception_caught_handler
    ) {
        $this->init_handlers([
            $plugin_management_handler,
            $field_edit_handler,
			$page_edit_handler,
	        $product_deleted_handler,
            $exception_caught_handler
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