<?php namespace bpmj\wpidea\admin\notices\cron;

use bpmj\wpidea\infrastructure\scheduler\Interface_Schedulable;
use DateInterval;
use DateTime;

class Cron_Watchdog_Job implements Interface_Schedulable
{
    public const TRANSIENT_NAME = 'wpi_cron_watchdog_transient';

    public function get_method_to_run(): callable
    {
        return [$this, 'store_current_time_in_transient'];
    }

    public function get_first_run_time(): DateTime
    {
        return new DateTime();
    }

    public function get_interval(): DateInterval
    {
        return new DateInterval(Interface_Schedulable::INTERVAL_1MINUTE);
    }

    public function get_args(): array
    {
        return [];
    }

    public function store_current_time_in_transient(): void
    {
        set_transient(self::TRANSIENT_NAME, time());
    }
}