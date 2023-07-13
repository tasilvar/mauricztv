<?php

namespace bpmj\wpidea\sales\product\core\event;

class Product_Field_Value_Changed_Event_Payload
{
    public const SETTINGS_TYPE_DIGITAL_PRODUCT = 'digital_product';
    public const SETTINGS_TYPE_COURSE = 'course';
    public const SETTINGS_TYPE_SERVICE = 'service';
    public const SETTINGS_TYPE_PHYSICAL_PRODUCT = 'physical_product';
    public const SETTINGS_TYPE_BUNDLE = 'bundle';
    public const SOURCE_TYPE_TABLE_EDITED = 'table_edited';
    public const SOURCE_TYPE_SETTINGS_EDITED = 'settings_edited';

    private string $field_label;
    private ?string $old_field_value;
    private ?string $new_field_value;

    private ?int $item_id;
    private string $source_type;

    private bool $is_toggle;


    private function __construct(
        string $field_label,
        ?string $old_field_value,
        ?string $new_field_value,
        ?int $item_id,
        string $source_type,
        bool $is_toggle = false
    ) {
        $this->field_label = $field_label;
        $this->old_field_value = $old_field_value;
        $this->new_field_value = $new_field_value;
        $this->item_id = $item_id;
        $this->source_type = $source_type;
        $this->is_toggle = $is_toggle;
    }

    public static function create(
        string $field_label,
        ?string $old_field_value,
        ?string $new_field_value,
        ?int $item_id,
        string $source_type,
        bool $is_toggle = false
    ): self {
        return new self(
            $field_label,
            $old_field_value,
            $new_field_value,
            $item_id,
            $source_type,
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

    public function get_item_id(): ?int
    {
        return $this->item_id;
    }

    public function is_toggle(): bool
    {
        return $this->is_toggle;
    }

    public function get_source_type(): string
    {
        return $this->source_type;
    }
}