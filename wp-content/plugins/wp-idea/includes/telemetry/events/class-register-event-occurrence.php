<?php
namespace bpmj\wpidea\telemetry\events;

use bpmj\wpidea\telemetry\section\Section;
use bpmj\wpidea\wolverine\event\EventHandler;

class Register_Event_Occurrence extends EventHandler
{
    const VALUE_EVENT_OCCURRED = 'yes';

    /**
     * @var Section
     */
    protected $section;

    protected $param_name;

    protected $event_value;

    public function __construct(string $section_class_name, string $param_name, string $event_value = self::VALUE_EVENT_OCCURRED) {
        $this->param_name = $param_name;
        $this->event_value = $event_value;

        if(class_exists($section_class_name)) $this->section = new $section_class_name;
    }

    public function run(array $data)
    {
        if(empty($this->section)) return;
        
        if($this->event_occurence_already_tracked()) return;

        WPI()->telemetry->update_data_in_section(
            $this->section,
            $this->param_name,
            $this->event_value
        );
    }

    protected function event_occurence_already_tracked(): bool
    {
        $telemetry_param_value = WPI()->telemetry->get_param_value(
            $this->section::NAME, 
            $this->param_name
        );

        return $telemetry_param_value === $this->event_value;
    }
}