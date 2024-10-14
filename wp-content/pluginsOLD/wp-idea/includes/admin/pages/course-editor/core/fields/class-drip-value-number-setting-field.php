<?php
namespace bpmj\wpidea\admin\pages\course_editor\core\fields;

use bpmj\wpidea\admin\settings\core\entities\fields\Abstract_Setting_Field;
use bpmj\wpidea\helpers\Translator_Static_Helper;

class Drip_Value_Number_Setting_Field extends Abstract_Setting_Field
{
    public function render_to_string(): string
    {
        if(!$this->is_visible()){
            return '';
        }

        $hidden_save_fields = true;

        $custom_save_field = $this->field_requires_higher_package()
            ? ''
            : "<button style='margin-left:20px;' class='custom-field-save-button' data-action='set-drip-value' style='display: none;'>
                                  " . Translator_Static_Helper::translate('settings.field.button.set') . "
                               </button>";

        return $this->get_field_wrapper_start()
            . "<input
                    type='number'
                    name='".$this->get_name()."'
                    id='".$this->get_name()."'
                    value='".$this->get_value()."'
                    data-initial-value='".$this->get_value()."'
                    class='single-field drip-value-field'
                    ".$this->get_disabled_html_attr(). '
                    ' .$this->get_readonly(). '
                    step="1" min="0" max="999"
                    oninput="validity.valid||(value=\'\');"
                    style="max-width:120px;"
            />'
            . $custom_save_field
            . $this->get_field_wrapper_end($hidden_save_fields);



    }
}