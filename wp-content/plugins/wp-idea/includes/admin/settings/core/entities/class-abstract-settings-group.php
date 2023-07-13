<?php

namespace bpmj\wpidea\admin\settings\core\entities;

use bpmj\wpidea\admin\helpers\html\Configuration_Settings_Popup;
use bpmj\wpidea\admin\settings\core\collections\Fields_Collection;
use bpmj\wpidea\admin\settings\core\entities\fields\{Abstract_Setting_Field,
	Field_Set_End,
	Field_Set_Start,
	relation\Field_Relation};
use bpmj\wpidea\admin\settings\core\services\Settings_Group_Dependencies_Service;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\settings\Interface_Settings;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\translator\Interface_Translator_Aware;
use bpmj\wpidea\wolverine\user\User;

abstract class Abstract_Settings_Group implements Interface_Translator_Aware
{
    protected Settings_Collection $settings;
    protected Configuration_Settings_Popup $settings_popup;
    protected Interface_Translator $translator;
    protected Interface_Url_Generator $url_generator;
    protected Current_Request $current_request;
    protected Interface_Settings $app_settings;

    public function init(
        Settings_Group_Dependencies_Service $settings_group_dependencies_service
    ): void
    {
        $this->settings_popup = $settings_group_dependencies_service->get_settings_popup();
        $this->url_generator = $settings_group_dependencies_service->get_url_generator();
        $this->current_request = $settings_group_dependencies_service->get_current_request();
        $this->app_settings = $settings_group_dependencies_service->get_app_settings();

        $this->settings = new Settings_Collection();

        $this->register_fields();
    }

    public function set_translator(Interface_Translator $translator): void
    {
        $this->translator = $translator;
    }

    abstract public function get_name(): string;

    abstract public function register_fields(): void;

    public function get_settings_collection(): Settings_Collection
    {
        return $this->settings;
    }

    protected function active_for_current_user(array $roles): bool
    {
        return User::currentUserHasAnyOfTheRoles($roles);
    }

    protected function add_fieldset(
        string $name,
        Fields_Collection $fields,
        bool $visible = true,
        ?Field_Relation $fieldset_relation = null
    ): void
    {
        if(!$visible){
            return;
        }

        $field_set_start = new Field_Set_Start($name);

        $field_set_start->set_relation($fieldset_relation);

        $this->get_settings_collection()->add($field_set_start);

        foreach($fields as $field){
            if(!$field->is_visible()){
                continue;
            }
            $this->get_settings_collection()->add($field);
        }

        $this->get_settings_collection()->add(new Field_Set_End());
    }

    protected function add_field(Abstract_Setting_Field $field): void
    {
        $this->get_settings_collection()->add($field);
    }
}