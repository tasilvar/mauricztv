<?php

declare(strict_types=1);

namespace bpmj\wpidea\infrastructure\scheduler;

use DateInterval;
use DateTimeImmutable;
use Closure;

class WP_Scheduler implements Interface_Scheduler
{
    public function schedule(Interface_Schedulable $event): bool
    {
        $what_to_run_slug = $this->get_callable_slug($event->get_method_to_run());
        $interval_in_seconds = $this->convert_date_interval_to_seconds($event->get_interval());

        $this->register_interval_if_needed($interval_in_seconds);

        $scheduled = false;

        if (!wp_next_scheduled($what_to_run_slug)) {
            $scheduled = wp_schedule_event($event->get_first_run_time()->getTimestamp(),
                'wpi_' . $interval_in_seconds . 's', $what_to_run_slug, $event->get_args());
        }

        add_action($what_to_run_slug, $event->get_method_to_run());

        return $scheduled;
    }

    public function unschedule(Interface_Schedulable $event_to_unschedule): bool
    {
        $what_to_run_slug = $this->get_callable_slug($event_to_unschedule->get_method_to_run());
        return (bool)wp_clear_scheduled_hook($what_to_run_slug);
    }

    private function register_interval_if_needed(int $interval_in_seconds): void
    {
        add_filter('cron_schedules', function ($schedules) use ($interval_in_seconds) {
            $schedules['wpi_' . $interval_in_seconds . 's'] = [
                'interval' => $interval_in_seconds,
                'display' => __($interval_in_seconds . ' seconds', BPMJ_EDDCM_DOMAIN)
            ];
            return $schedules;
        });
    }

    private function convert_date_interval_to_seconds(DateInterval $interval): int
    {
        $reference = new DateTimeImmutable();
        $end_time = $reference->add($interval);

        return $end_time->getTimestamp() - $reference->getTimestamp();
    }

    private function get_callable_slug(callable $callable): string
    {
        if (is_string($callable)) {
            return trim($callable);
        }

        if (is_array($callable)) {
            if (is_object($callable[0])) {
                return sprintf("%s::%s", get_class($callable[0]), trim($callable[1]));
            }

            return sprintf("%s::%s", trim($callable[0]), trim($callable[1]));
        }

        if ($callable instanceof Closure) {
            return 'closure';
        }

        return 'unknown';
    }
}