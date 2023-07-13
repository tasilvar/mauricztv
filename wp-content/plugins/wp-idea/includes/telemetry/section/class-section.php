<?php

namespace bpmj\wpidea\telemetry\section;

use bpmj\wpidea\telemetry\events\Register_Event_Occurrence;
use bpmj\wpidea\telemetry\events\Trackable_Event;
use bpmj\wpidea\telemetry\events\Trackable_Events_Collection;
use bpmj\wpidea\wolverine\event\Events;

abstract class Section
{
    const NAME = 'other';

    /**
     * @var Trackable_Events_Collection
     */
    protected $trackable_events;

    public function __construct() {
        $this->trackable_events = new Trackable_Events_Collection();

        $this->add_trackable_events();
    }

    public static function get_param_label(string $param_name): string
    {
        return static::get_labels()[$param_name] ?? $param_name;
    }

    protected static function get_labels(): array
    {
        return [];
    }

    protected function add_trackable_events(): void
    {
        // eg. $this->trackable_events->append(new Trackable_Event(...))
    }

    protected function get_events_to_track(): Trackable_Events_Collection
    {
        return $this->trackable_events;
    }

    public function init_events_tracking(): void
    {
        foreach ($this->get_events_to_track() as $key => $trackable_event) {
            if(!$trackable_event instanceof Trackable_Event) continue;

            $this->track_event_occurrence($trackable_event);
        }
    }

    public function track_event_occurrence(Trackable_Event $trackable_event): void {
        $default_value = Register_Event_Occurrence::VALUE_EVENT_OCCURRED;

        Events::on(
            $trackable_event->get_event_name(),
            new Register_Event_Occurrence(
                get_class($this),
                $trackable_event->get_telemetry_param_name(),
                $trackable_event->get_telemetry_value() ?? $default_value
            )
        );
    }
}