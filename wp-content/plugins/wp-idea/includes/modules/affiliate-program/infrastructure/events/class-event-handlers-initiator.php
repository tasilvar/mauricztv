<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\modules\affiliate_program\infrastructure\events;

use bpmj\wpidea\events\Interface_Event_Handler;
use bpmj\wpidea\modules\affiliate_program\core\events\sales\handlers\Commission_Attributed_Event_Handler;
use bpmj\wpidea\modules\affiliate_program\core\events\sales\handlers\Purchase_Completed_Event_Handler;

class Event_Handlers_Initiator
{
    public function __construct(
        Commission_Attributed_Event_Handler $commission_attributed_event_handler,
        Purchase_Completed_Event_Handler $purchase_completed_event_handler
    ) {
        $this->init_handlers([
            $commission_attributed_event_handler,
            $purchase_completed_event_handler
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