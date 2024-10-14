<?php
/**
 * This file is licenses under proprietary license
 */

namespace bpmj\wpidea\debug_tools;

use bpmj\wpidea\events\Event_Name;
use bpmj\wpidea\events\Interface_Events;

class Time_Meter
{
    private $events;

    private $start_time;

    private $time_entries = [];

    public function __construct(
        Interface_Events $events
    )
    {
        $this->events = $events;
    }

    public function start_time_measurement(): void
    {
        $this->start_time = microtime(true);
    }

    public function take_measurement(string $label = null): void
    {
        if(!isset($this->start_time)) {
            throw new \Exception('You have to start the measurement first!');
        }

        $this->time_entries[] = [
            'time' => microtime(true),
            'label' => $label
        ];
    }

    public function finish(): void
    {
        $log = 'Time measurements: ';

        foreach ($this->time_entries as $index => $entry) {
            $diff = $index === 0 ? ($entry['time'] - $this->start_time) : ($entry['time'] - $this->time_entries[$index - 1]['time']);
            $log .= ' -------- #' . ($index + 1) . ': ' . round($diff, 4) . 's';

            if($entry['label']) {
                $log .= ' (' . $entry['label'] . ')';
            }
        }

        $this->events->emit(Event_Name::DEBUG, $log);
    }
}