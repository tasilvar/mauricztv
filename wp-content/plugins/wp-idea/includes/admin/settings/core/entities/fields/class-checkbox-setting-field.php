<?php

namespace bpmj\wpidea\admin\settings\core\entities\fields;

class Checkbox_Setting_Field extends Abstract_Setting_Field
{
    public function render_to_string(): string
    {
        if (!$this->is_visible()) {
            return '';
        }

        return $this->get_field_wrapper_start()
            . "<input
                    type='checkbox'
                    name='" . $this->get_name() . "'
                    id='" . $this->get_name() . "'
                    value='on'
                    " . ($this->get_value() ? 'checked' : '') . "
                    data-initial-value='" . $this->get_value() . "'
                    class='single-field'
                    " . $this->get_disabled_html_attr() . '
            />'
            . $this->get_field_wrapper_end();
    }
}
