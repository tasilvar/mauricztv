<?php

namespace bpmj\wpidea\admin\settings\core\services;

use bpmj\wpidea\admin\settings\core\entities\Abstract_Settings_Group;
use bpmj\wpidea\admin\settings\core\entities\fields\Abstract_Setting_Field;
use bpmj\wpidea\admin\settings\core\entities\fields\Non_Savable_Field;
use bpmj\wpidea\admin\settings\core\entities\Settings_Group_Collection;
use bpmj\wpidea\admin\settings\core\factories\Settings_Group_Factory;
use bpmj\wpidea\instantiator\Interface_Initiable;

class Settings_Api implements Interface_Initiable
{
    private Settings_Group_Collection $setting_groups;
    private Interface_Settings_Fields_Service $settings_fields_service;
    private Settings_Group_Factory $group_factory;
    private array $active_groups = [];

    public function __construct(
        Settings_Group_Factory $group_factory,
        Interface_Active_Settings_Groups_Provider $active_groups,
        Interface_Settings_Fields_Service $settings_fields_service
    ) {
        $this->setting_groups = new Settings_Group_Collection();
        $this->active_groups = $active_groups->get_groups();
        $this->group_factory = $group_factory;
        $this->settings_fields_service = $settings_fields_service;
    }

    public function init(): void
    {
        foreach ($this->active_groups as $group_class) {
            $group = $this->get_group_with_values($group_class);
            $this->setting_groups->add($group);
        }
    }

    public function get_settings_group_by_name(string $name): ?Abstract_Settings_Group
    {
        foreach ($this->setting_groups as $group) {
            if ($group->get_name() === $name) {
                return $group;
            }
        }
        return null;
    }

    public function get_setting_by_name(string $name): ?Abstract_Setting_Field
    {
        foreach ($this->setting_groups as $group) {
            foreach ($group->get_settings_collection() as $field) {

                if ($field->get_name() === $name) {
                    return $field;
                }

                if ($field->has_additional_config()) {
                    foreach ($field->get_additional_fields() as $additional_field) {
                        if ($additional_field->get_name() === $name) {
                            return $additional_field;
                        }
                    }
                }
            }
        }

        return null;
    }

    public function update_field(Abstract_Setting_Field $field): void
    {
        $this->settings_fields_service->update_field($field);
    }

    public function get_field_value(Abstract_Setting_Field $field)
    {
        return $this->settings_fields_service->get_field_value($field);
    }

    private function get_group_with_values(string $class): Abstract_Settings_Group
    {
        $group = $this->group_factory->get_instance($class);

        foreach ($group->get_settings_collection() as $field) {
            if ($field instanceof Non_Savable_Field) {
                continue;
            }

            $get_field_value = $this->settings_fields_service->get_field_value($field);

            if (is_null($get_field_value) && !$field->has_additional_config()) {
                continue;
            }

            $field->change_value($get_field_value);

            if ($field->has_additional_config()) {
                foreach ($field->get_additional_fields() as $additional_field) {
                    $get_additional_field_value = $this->get_field_value($additional_field);
                    if (!$get_additional_field_value) {
                        continue;
                    }
                    $additional_field->change_value($get_additional_field_value);
                }
            }
        }

        return $group;
    }
}