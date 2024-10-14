<?php

namespace bpmj\wpidea\admin\settings\core\events;

class Settings_Field_Value_Changed_Event_Payload
{
    private string $field_label;
    private ?string $old_field_value;
    private ?string $new_field_value;
    private bool $is_toggle;


    private function __construct(
        string $field_label,
        ?string $old_field_value,
        ?string $new_field_value,
        bool $is_toggle = false
    )
    {
        $this->field_label = $field_label;
        $this->old_field_value = $old_field_value;
        $this->new_field_value = $new_field_value;
        $this->is_toggle = $is_toggle;
    }

    public static function create(
        string $field_label,
        ?string $old_field_value,
        ?string $new_field_value,
        bool $is_toggle = false
    ): self
    {
        return new self(
            $field_label,
            $old_field_value,
            $new_field_value,
            $is_toggle
        );
    }

    public function get_field_label(): string
    {
        return $this->field_label;
    }

    public function get_old_field_value(): ?string
    {
        return $this->old_field_value;
    }

    public function get_new_field_value(): ?string
    {
        return $this->new_field_value;
    }

    public function is_toggle(): bool
    {
        return $this->is_toggle;
    }
}