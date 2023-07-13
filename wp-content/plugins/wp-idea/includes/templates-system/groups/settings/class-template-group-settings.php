<?php

namespace bpmj\wpidea\templates_system\groups\settings;

use bpmj\wpidea\assets\Assets;
use bpmj\wpidea\templates_system\groups\settings\fields\Template_Group_Settings_Field;
use bpmj\wpidea\templates_system\groups\Template_Group;
use bpmj\wpidea\templates_system\groups\Template_Group_Id;

class Template_Group_Settings
{
    private const OPTION_PREFIX = 'bpmj_wpi_template_group_settings_';

    public const OPTION_MAIN_FONT = 'main_font';
    public const OPTION_SECONDARY_FONT = 'secondary_font';
    public const OPTION_BG_FILE = 'bg_file';
    public const OPTION_LOGIN_BG_FILE = 'login_bg_file';
    public const OPTION_SECTION_BG_FILE = 'section_bg_file';
    public const OPTION_CUSTOM_CSS = 'custom_css';
    public const OPTION_OVERRIDE_ALL = 'override_all';
    public const OPTION_COURSES_PAGE = 'courses_page';
    public const OPTION_CART_PAGE = 'cart_page';
    public const OPTION_USER_ACCOUNT_PAGE = 'user_account_page';

    public const EVENT_GROUP_SETTINGS_CHANGED = 'group_settings_changed';

    private $group_id;

    private $fields;

    private $legacy_base_template;

    private function __construct(Template_Group_Id $group_id, string $legacy_base_template)
    {
        $this->group_id = $group_id;
        $this->legacy_base_template = $legacy_base_template;
    }

    public static function for_group(Template_Group $group): self
    {
        $settings = new self($group->get_id(), $group->get_base_template());

        return $settings->load_fields();
    }

    private function load_fields(): self
    {
        $fields = $this->fetch_fields_from_the_db();

        $fields = $this->fill_default_fields_with_values_saved_in_db($fields, $this->get_default_fields());

        $this->strip_slashes_in_custom_css_field($fields);

        $this->fields = $fields;

        return $this;
    }

    private function strip_slashes_in_custom_css_field(Template_Group_Settings_Fields $fields)
    {
        /** @var Template_Group_Settings_Field $custom_css */
        $custom_css = $fields->offsetGet(self::OPTION_CUSTOM_CSS);
        $custom_css->set_value(stripslashes($custom_css->get_value()));
        $fields->offsetSet(self::OPTION_CUSTOM_CSS, $custom_css);
    }

    private function fetch_fields_from_the_db(): Template_Group_Settings_Fields
    {
        $fetched_fields = get_option($this->get_option_name(), null) ?? $this->get_default_fields();

        if(is_string($fetched_fields)) {
            $unserialized = @unserialize($fetched_fields, ['allowed_classes' => true]);

            if ($unserialized instanceof Template_Group_Settings_Fields) {
                $fetched_fields = $unserialized;
            }
        }

        return $fetched_fields;
    }

    public function save(): bool
    {
        $this->store_fields();

        $this->trigger(self::EVENT_GROUP_SETTINGS_CHANGED);

        return true;
    }

    private function get_group_id(): Template_Group_Id
    {
        return $this->group_id;
    }

    private function get_option_name(): string
    {
        return self::OPTION_PREFIX . $this->get_group_id()->stringify();
    }

    private function get_default_fields(): Template_Group_Settings_Fields
    {
        $fields_collection = new Template_Group_Settings_Fields();

        /** @var Template_Group_Settings_Field $field */
        foreach ($fields_collection::default_fields_array($this->get_legacy_base_template()) as $field) {

            $fields_collection[$field->get_name()] = $field;
        }

        return $fields_collection;
    }

    public function get_all(): Template_Group_Settings_Fields
    {
        return $this->fields;
    }

    public function unset(string $option_name): Template_Group_Settings
    {
        if(!$this->get_all()->find_by_name($option_name)){
            
            return $this;
        }

        $this->fields->offsetUnset($option_name);

        return $this;

    }

    public function set(string $option_name, string $value): Template_Group_Settings
    {
        $field = $this->get($option_name);

        if(is_null($field)) {
            return $this;
        }

        $field->set_value($value);

        $this->fields[$option_name] = $field;

        return $this;
    }

    public function get(string $option_name): ?Template_Group_Settings_Field
    {
        return $this->get_all()->find_by_name($option_name);
    }

    public function update_from_array(array $request_data): self
    {
        foreach ($request_data as $name => $value) {
            $this->set($name, $value);
        }

        return $this;
    }

    private function store_fields(): bool
    {
        return update_option($this->get_option_name(), $this->fields);
    }

    private function regenerate_assets(): void
    {
        $assets = new Assets(BPMJ_EDDCM_TEMPLATES_DIR . $this->get_legacy_base_template());
        $assets->regenerate();
    }

    private function get_legacy_base_template(): string
    {
        return $this->legacy_base_template;
    }

    public function trigger(string $event): self
    {
        switch ($event) {
            case self::EVENT_GROUP_SETTINGS_CHANGED:
                $this->on_group_settings_changed();
                break;
            default:
                break;
        }

        return $this;
    }

    private function on_group_settings_changed(): void
    {
        $this->regenerate_assets();
    }

    private function fill_default_fields_with_values_saved_in_db(
        Template_Group_Settings_Fields $fields_from_db,
        Template_Group_Settings_Fields $default_fields
    ): Template_Group_Settings_Fields
    {
        foreach ($fields_from_db as $field_name => $field) {
            if (!empty($default_fields[$field_name])) {
                $default_fields[$field_name]->set_value($field->get_value());
            }
        }

        return $default_fields;
    }
}