<?php

namespace bpmj\wpidea\admin\settings\core\services;

use bpmj\wpidea\admin\settings\core\entities\fields\Abstract_Setting_Field;
use bpmj\wpidea\admin\settings\core\entities\fields\Toggle_Setting_Field;
use bpmj\wpidea\events\Event_Name;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\admin\settings\core\events\Settings_Field_Value_Changed_Event_Payload;

class Settings_Events
{
    private Interface_Events $events;

    public function __construct(
        Interface_Events $events
    ) {
        $this->events = $events;
    }

    public function emit_field_value_updated_event(
        Abstract_Setting_Field $field,
        $old_value,
        $new_value
    ): void {
        $this->events->emit(
            Event_Name::SETTINGS_FIELD_VALUE_UPDATED,
            Settings_Field_Value_Changed_Event_Payload::create(
                $field->get_label(),
                is_array($old_value) ? $this->json_encode($old_value) : $old_value,
                is_array($new_value) ? $this->json_encode($new_value) : $new_value,
                $this->is_toggle_field($field)
            )
        );
    }

    private function is_toggle_field(Abstract_Setting_Field $field): bool
    {
        return $field instanceof Toggle_Setting_Field;
    }

    private function json_encode(array $fields): ?string
    {
        return json_encode($fields, JSON_FORCE_OBJECT) ?? null;
    }
}
