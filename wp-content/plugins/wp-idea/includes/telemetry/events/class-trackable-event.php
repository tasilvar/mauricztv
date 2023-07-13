<?php
namespace bpmj\wpidea\telemetry\events;

class Trackable_Event
{
    protected $event_name;

    protected $telemetry_param_name;

    protected $telemetry_value;

    public function __construct(string $event_name, string $telemetry_param_name, string $telemetry_value) {
        $this->event_name = $event_name;
        $this->telemetry_param_name = $telemetry_param_name;
        $this->telemetry_value = $telemetry_value;
    }

    public function get_event_name(): string
    {
        return $this->event_name;
    }

    public function get_telemetry_param_name(): string
    {
        return $this->telemetry_param_name;
    }

    public function get_telemetry_value(): string
    {
        return $this->telemetry_value;
    }
}