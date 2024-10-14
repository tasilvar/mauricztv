<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\modules\active_sessions_limiter\infrastructure\events;

use bpmj\wpidea\events\Interface_Event_Handler;
use bpmj\wpidea\modules\active_sessions_limiter\core\events\handlers\Log_Active_Sessions_Limiter_Handler;


class Event_Handlers_Initiator
{
    public function __construct(
        Log_Active_Sessions_Limiter_Handler $log_active_sessions_limit_handler
    ) {
        $this->init_handlers([
            $log_active_sessions_limit_handler
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