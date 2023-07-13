<?php
/**
 * This file is licenses under proprietary license
 */

namespace bpmj\wpidea\debug_tools;

use bpmj\wpidea\events\Event_Name;
use bpmj\wpidea\events\Interface_Events;

class Memory_Meter
{
    public const LOG_DIFF = 'log_diff';
    public const LOG_ABSOLUTE_VALUES = 'log_absolute_values';

    private $events;

    private $start_value;

    private $entries = [];

    public function __construct(
        Interface_Events $events
    )
    {
        $this->events = $events;
    }

    public function start_memory_measurement(): void
    {
        $this->start_value = memory_get_usage();
    }

    public function take_measurement(string $label = null): void
    {
        if(!isset($this->start_value)) {
            throw new \Exception('You have to start the measurement first!');
        }

        $this->entries[] = [
            'memory' => memory_get_usage(),
            'label' => $label
        ];
    }

    public function finish(string $mode = self::LOG_DIFF): void
    {
        $log = 'Memory measurements: ';

        foreach ($this->entries as $index => $entry) {
            $value = $entry['memory'];
            if($mode === self::LOG_DIFF) {
                $value = $index === 0 ? ($entry['memory'] - $this->start_value) : ($entry['memory'] - $this->entries[$index - 1]['memory']);
            }
            $log .= ' -------- #' . ($index + 1) . ': ' . $this->format_size($value);

            if($entry['label']) {
                $log .= ' (' . $entry['label'] . ')';
            }
        }

        $this->events->emit(Event_Name::DEBUG, $log);
    }

    private function format_size(int $size): string
    {
        $unit = ['B','KB','MB','GB','TB'];

        return @round($size/ (1024 ** ($i = floor(log($size, 1024)))),4).' '.$unit[$i];
    }
}