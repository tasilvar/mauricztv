<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\infrastructure\scheduler;

interface Interface_Scheduler
{
    public function schedule(Interface_Schedulable $event_to_schedule): bool;

    public function unschedule(Interface_Schedulable $event_to_unschedule): bool;
}