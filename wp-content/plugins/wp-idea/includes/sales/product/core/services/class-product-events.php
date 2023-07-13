<?php

namespace bpmj\wpidea\sales\product\core\services;

use bpmj\wpidea\admin\pages\bundle_editor\core\fields\Bundle_Content_Field;
use bpmj\wpidea\admin\pages\digital_product_editor\core\fields\Files_Field;
use bpmj\wpidea\admin\settings\core\entities\fields\Abstract_Setting_Field;
use bpmj\wpidea\admin\settings\core\entities\fields\Toggle_Setting_Field;
use bpmj\wpidea\events\Event_Name;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\sales\product\core\event\Product_Field_Value_Changed_Event_Payload;
use bpmj\wpidea\translator\Interface_Translator;

class Product_Events
{
    private Interface_Events $events;
    private Interface_Translator $translator;

    public function __construct(
        Interface_Events $events,
        Interface_Translator $translator
    ) {
        $this->events = $events;
        $this->translator = $translator;
    }

    public function emit_course_field_value_updated_event(
        Abstract_Setting_Field $field,
        $old_value,
        $new_value,
        int $id,
        ?string $source_type = null
    ): void {
        $this->emit_event(
            Event_Name::COURSE_FIELD_VALUE_UPDATED,
            $this->get_field_label($field),
            $old_value,
            $new_value,
            $id,
            $source_type,
            $this->is_toggle_field($field)
        );
    }

    public function emit_course_field_toggle_sales_updated_event(
        string $label,
        $new_value,
        int $id
    ): void {
        $this->emit_event(
            Event_Name::COURSE_FIELD_VALUE_UPDATED,
            $label,
            null,
            $new_value,
            $id,
            Product_Field_Value_Changed_Event_Payload::SOURCE_TYPE_TABLE_EDITED,
            true
        );
    }

    public function emit_course_variable_prices_updated_event(
        $old_value,
        $new_value,
        int $id
    ): void {
        $this->emit_event(
            Event_Name::COURSE_VARIABLE_PRICES_UPDATED,
            $this->translator->translate('product_editor.sections.general.variable_pricing'),
            $old_value,
            $new_value,
            $id,
            null,
            false
        );
    }

    public function emit_course_structure_updated_event(
        $old_value,
        $new_value,
        int $id
    ): void {
        $this->emit_event(
            Event_Name::COURSE_STRUCTURE_UPDATED,
            $this->translator->translate('course_editor.sections.structure'),
            $old_value,
            $new_value,
            $id,
            null,
            false
        );
    }

    public function emit_course_deleted_event(
        string $label,
        int $id
    ): void {
        $this->events->emit(
            Event_Name::COURSE_DELETED,
            Product_Field_Value_Changed_Event_Payload::create(
                $label,
                null,
                null,
                $id,
                Product_Field_Value_Changed_Event_Payload::SOURCE_TYPE_TABLE_EDITED
            )
        );
    }

    public function emit_services_field_value_updated_event(
        Abstract_Setting_Field $field,
        $old_value,
        $new_value,
        int $id,
        ?string $source_type = null
    ): void {
        $this->emit_event(
            Event_Name::SERVICES_FIELD_VALUE_UPDATED,
            $this->get_field_label($field),
            $old_value,
            $new_value,
            $id,
            $source_type,
            $this->is_toggle_field($field)
        );
    }

    public function emit_service_field_toggle_sales_updated_event(
        string $label,
        $new_value,
        int $id
    ): void {
        $this->emit_event(
            Event_Name::SERVICES_FIELD_VALUE_UPDATED,
            $label,
            null,
            $new_value,
            $id,
            Product_Field_Value_Changed_Event_Payload::SOURCE_TYPE_TABLE_EDITED,
            true
        );
    }

    public function emit_services_deleted_event(
        string $label,
        int $id
    ): void {
        $this->events->emit(
            Event_Name::SERVICES_DELETED,
            Product_Field_Value_Changed_Event_Payload::create(
                $label,
                null,
                null,
                $id,
                Product_Field_Value_Changed_Event_Payload::SOURCE_TYPE_TABLE_EDITED
            )
        );
    }

    public function emit_digital_product_field_value_updated_event(
        Abstract_Setting_Field $field,
        $old_value,
        $new_value,
        int $id,
        ?string $source_type = null
    ): void {
        $this->emit_event(
            Event_Name::DIGITAL_PRODUCT_FIELD_VALUE_UPDATED,
            $this->get_field_label($field),
            $old_value,
            $new_value,
            $id,
            $source_type,
            $this->is_toggle_field($field)
        );
    }

    public function emit_digital_product_field_toggle_sales_updated_event(
        string $label,
        $new_value,
        int $id
    ): void {
        $this->emit_event(
            Event_Name::DIGITAL_PRODUCT_FIELD_VALUE_UPDATED,
            $label,
            null,
            $new_value,
            $id,
            Product_Field_Value_Changed_Event_Payload::SOURCE_TYPE_TABLE_EDITED,
            true
        );
    }

    public function emit_digital_product_deleted_event(
        string $label,
        int $id
    ): void {
        $this->events->emit(
            Event_Name::DIGITAL_PRODUCT_DELETED,
            Product_Field_Value_Changed_Event_Payload::create(
                $label,
                null,
                null,
                $id,
                Product_Field_Value_Changed_Event_Payload::SOURCE_TYPE_TABLE_EDITED
            )
        );
    }

    public function emit_physical_product_field_value_updated_event(
        Abstract_Setting_Field $field,
        $old_value,
        $new_value,
        int $id,
        ?string $source_type = null
    ): void {
        $this->emit_event(
            Event_Name::PHYSICAL_PRODUCT_FIELD_VALUE_UPDATED,
            $this->get_field_label($field),
            $old_value,
            $new_value,
            $id,
            $source_type,
            $this->is_toggle_field($field)
        );
    }

    public function emit_physical_product_field_toggle_sales_updated_event(
        string $label,
        $new_value,
        int $id
    ): void {
        $this->emit_event(
            Event_Name::PHYSICAL_PRODUCT_FIELD_VALUE_UPDATED,
            $label,
            null,
            $new_value,
            $id,
            Product_Field_Value_Changed_Event_Payload::SOURCE_TYPE_TABLE_EDITED,
            true
        );
    }

    public function emit_physical_product_deleted_event(
        string $label,
        int $id
    ): void {
        $this->events->emit(
            Event_Name::PHYSICAL_PRODUCT_DELETED,
            Product_Field_Value_Changed_Event_Payload::create(
                $label,
                null,
                null,
                $id,
                Product_Field_Value_Changed_Event_Payload::SOURCE_TYPE_TABLE_EDITED
            )
        );
    }

    public function emit_bundle_field_value_updated_event(
        Abstract_Setting_Field $field,
        $old_value,
        $new_value,
        int $id,
        ?string $source_type = null
    ): void {
        $this->emit_event(
            Event_Name::BUNDLE_FIELD_VALUE_UPDATED,
            $this->get_field_label($field),
            $old_value,
            $new_value,
            $id,
            $source_type,
            $this->is_toggle_field($field)
        );
    }

    public function emit_bundle_field_toggle_sales_updated_event(
        string $label,
        $new_value,
        int $id
    ): void {
        $this->emit_event(
            Event_Name::BUNDLE_FIELD_VALUE_UPDATED,
            $label,
            null,
            $new_value,
            $id,
            Product_Field_Value_Changed_Event_Payload::SOURCE_TYPE_TABLE_EDITED,
            true
        );
    }

    public function emit_bundle_variable_prices_updated_event(
        $old_value,
        $new_value,
        int $id
    ): void {
        $this->emit_event(
            Event_Name::BUNDLE_VARIABLE_PRICES_UPDATED,
            $this->translator->translate('product_editor.sections.general.variable_pricing'),
            $old_value,
            $new_value,
            $id,
            null,
            false
        );
    }

    public function emit_bundle_deleted_event(
        string $label,
        int $id
    ): void {
        $this->events->emit(
            Event_Name::BUNDLE_DELETED,
            Product_Field_Value_Changed_Event_Payload::create(
                $label,
                null,
                null,
                $id,
                Product_Field_Value_Changed_Event_Payload::SOURCE_TYPE_TABLE_EDITED
            )
        );
    }

    private function emit_event(
        string $event_name,
        string $label,
        $old_value,
        $new_value,
        ?int $id,
        ?string $source_type,
        bool $is_toggle
    ): void {
        $this->events->emit(
            $event_name,
            Product_Field_Value_Changed_Event_Payload::create(
                $label,
                is_array($old_value) ? $this->json_encode($old_value) : $old_value,
                is_array($new_value) ? $this->json_encode($new_value) : $new_value,
                $id,
                $source_type ?? Product_Field_Value_Changed_Event_Payload::SOURCE_TYPE_SETTINGS_EDITED,
                $is_toggle
            )
        );
    }

    private function get_field_label(Abstract_Setting_Field $field): string
    {
        if ($this->is_files_field($field)) {
            return $this->translator->translate('digital_product_editor.sections.files');
        }

        if ($this->is_bundle_content_field($field)) {
            return $this->translator->translate('bundle_editor.sections.package_contents');
        }

        return $field->get_label();
    }

    private function is_toggle_field(Abstract_Setting_Field $field): bool
    {
        return $field instanceof Toggle_Setting_Field;
    }

    private function is_files_field(Abstract_Setting_Field $field): bool
    {
        return $field instanceof Files_Field;
    }

    private function is_bundle_content_field(Abstract_Setting_Field $field): bool
    {
        return $field instanceof Bundle_Content_Field;
    }

    private function json_encode(array $fields): ?string
    {
        return json_encode($fields, JSON_FORCE_OBJECT) ?? null;
    }
}
