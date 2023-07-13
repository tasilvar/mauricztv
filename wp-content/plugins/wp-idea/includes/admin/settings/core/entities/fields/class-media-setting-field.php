<?php
namespace bpmj\wpidea\admin\settings\core\entities\fields;

use bpmj\wpidea\helpers\Translator_Static_Helper;

class Media_Setting_Field extends Abstract_Setting_Field
{
    public function render_to_string(): string
    {
        if(!$this->is_visible()){
            return '';
        }

        return $this->get_field_wrapper_start()
            . "<div class='media-input-wrapper'><input
                    type='text'
                    name='".$this->get_name()."'
                    id='".$this->get_name()."'
                    value='".$this->get_value()."'
                    data-initial-value='".$this->get_value()."'
                    class='single-field media-url'
                    " . $this->get_disabled_html_attr() . "
                    " . $this->get_readonly() . "
            >
            <button class='" . ($this->get_disabled_html_attr() ? 'browse-media-disabled' : 'browse-media') . "'>".Translator_Static_Helper::translate('settings.field.button.media')."</button></div>"
            . $this->get_field_wrapper_end();
    }

}