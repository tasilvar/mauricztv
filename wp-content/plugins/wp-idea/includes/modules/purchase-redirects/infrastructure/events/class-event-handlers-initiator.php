<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\modules\purchase_redirects\infrastructure\events;

use bpmj\wpidea\events\Interface_Event_Handler;
use bpmj\wpidea\modules\purchase_redirects\core\events\external\handlers\Redirect_After_Purchase_Handler;

class Event_Handlers_Initiator
{
    public function __construct(
        Redirect_After_Purchase_Handler $redirect_after_purchase_handler
    ) {
        $this->init_handlers([
            $redirect_after_purchase_handler
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