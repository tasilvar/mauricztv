<?php

namespace bpmj\wpidea\admin\settings\infrastructure\services;

use bpmj\wpidea\admin\settings\core\entities\fields\Abstract_Setting_Field;
use bpmj\wpidea\admin\settings\core\persistence\Interface_Settings_Persistence;
use bpmj\wpidea\admin\settings\core\services\Interface_Settings_Fields_Service;
use bpmj\wpidea\admin\settings\core\services\Settings_Events;
use bpmj\wpidea\events\Interface_Events;

class App_Settings_Fields_Service implements Interface_Settings_Fields_Service
{
    public const UPDATED_EVENT_NAME = 'wpi-field-updated';
    private Interface_Settings_Persistence $settings_persistence;
    private Interface_Events $events;
    private Settings_Events $settings_events;

    public function __construct(
        Interface_Settings_Persistence $settings_persistence,
        Interface_Events $events,
        Settings_Events $settings_events
    ) {
        $this->settings_persistence = $settings_persistence;
        $this->events = $events;
        $this->settings_events = $settings_events;
    }

    public function update_field(Abstract_Setting_Field $field): void
    {
        $old_value = $this->get_field_value($field);

        $this->settings_persistence->save($field);

        $this->events->emit(self::UPDATED_EVENT_NAME, $field->get_name());

        $new_value = $field->get_value();

        $this->settings_events->emit_field_value_updated_event(
            $field,
            $old_value,
            $new_value
        );
    }

    public function get_field_value(Abstract_Setting_Field $field)
    {
        return $this->settings_persistence->get_value($field);
    }
}