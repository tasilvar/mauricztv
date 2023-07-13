<?php
/**
 * This file is licenses under proprietary license
 */

namespace bpmj\wpidea\scheduled_events;

use bpmj\wpidea\admin\notices\cron\Cron_Watchdog_Job;
use bpmj\wpidea\infrastructure\logs\tasks\Delete_Old_Logs;
use bpmj\wpidea\infrastructure\scheduler\Interface_Scheduler;
use Psr\Container\ContainerInterface;

class On_Load_Schedule_Handler
{
    private Interface_Scheduler $scheduler;
    private ContainerInterface $container;

    public function __construct(Interface_Scheduler $scheduler, ContainerInterface $container) {
        $this->scheduler = $scheduler;
        $this->container = $container;

        $this->schedule();
    }

    private function schedule(): void
    {
        $this->scheduler->schedule($this->container->get(Check_License_Data::class));
        $this->scheduler->schedule($this->container->get(Delete_Old_Logs::class));
        $this->scheduler->schedule($this->container->get(Cron_Watchdog_Job::class));
    }
}