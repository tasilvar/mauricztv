<?php

namespace bpmj\wpidea\admin\pages\course_editor\core\fields;

use bpmj\wpidea\admin\settings\core\collections\Additional_Fields_Collection;
use bpmj\wpidea\admin\settings\core\entities\fields\Abstract_Setting_Field;
use bpmj\wpidea\helpers\Translator_Static_Helper;

class Drip_Unit_Select_Setting_Field extends Abstract_Setting_Field
{
    private array $options = [];
    
    public function __construct(
        string $name,
        ?string $label = null,
        ?string $description = null,
        ?string $tooltip = null,
        ?Additional_Fields_Collection $additional_settings_fields = null,
        ?array $options = [],
        $value = null
    ) {
        $this->options = $options;
        parent::__construct($name, $label, $description, $tooltip, $additional_settings_fields, $value);
    }

    public function render_to_string(): string
    {
        if (!$this->is_visible()) {
            return '';
        }

        $options = $this->get_options_string();

        $custom_save_field =  $this->field_requires_higher_package()
            ? ''
            : "<button style='margin-left:20px;' class='custom-field-save-button' data-action='change-drip-unit-do'>
                                  " . Translator_Static_Helper::translate('settings.field.button.set') . "
                               </button>";

        $hidden_save_fields = true;

        return $this->get_field_wrapper_start()
            . "<select
                    name='" . $this->get_name() . "'
                    id='" . $this->get_name() . "'
                    data-initial-value='" . $this->get_value() . "'
                    class='single-field wpi-select-field drip-unit-field'
                    " . $this->get_disabled_html_attr() . "
                    style='max-width:120px;'
            >
            " . $options . "</select>"
            . $custom_save_field
            . $this->get_field_wrapper_end($hidden_save_fields);
    }

    private function get_options_string(): string
    {
        $html = '';
        foreach ($this->options as $value => $label) {
            $selected = '';
            if ((string)$value === $this->get_value()) {
                $selected = 'selected="selected"';
            }
            $html .= "<option {$selected} value='{$value}'>{$label}</option>";
        }
        return $html;
    }

}
