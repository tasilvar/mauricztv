<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\admin\notices;

use bpmj\wpidea\admin\notices\compatibility\Pixel_Caffeine_Handler;
use bpmj\wpidea\admin\notices\cron\Cron_Jobs_Pending_Handler;


class Notice_Handlers_Initiator
{
    public function __construct(
        Pixel_Caffeine_Handler $pixel_caffeine_handler,
        Cron_Jobs_Pending_Handler $cron_jobs_pending_handler
    ) {
        $this->init_handlers([
            $pixel_caffeine_handler,
            $cron_jobs_pending_handler
        ]);
    }

    private function init_handler(Interface_Notice_Handler $handler): void
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
