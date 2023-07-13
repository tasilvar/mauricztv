<?php
namespace bpmj\wpidea\admin\settings\core\entities\fields;

use bpmj\wpidea\helpers\Translator_Static_Helper;

class Configure_Popup_Setting_Field extends Abstract_Setting_Field
{

    public function render_to_string(): string
    {
        if(!$this->is_visible()){
            return '';
        }
        
        return $this->get_field_wrapper_start()
            . $this->get_popup()
            . $this->get_field_wrapper_end();
    }
}
