<?php
namespace bpmj\wpidea\admin\settings\core\entities\fields;

class Toggle_Setting_Field extends Abstract_Setting_Field
{

    public function get_popup(): ?string
    {
        if($this->field_requires_higher_package()) {
            return null;
        }

        return parent::get_popup();
    }

    public function render_to_string(): string
    {
        if(!$this->is_visible()){
            return '';
        }

        return $this->get_field_wrapper_start('toggle-field')
            . "<label class='switch' for='".$this->get_name()."'>
                <input type='checkbox'
                   name='".$this->get_name()."'
                   id='".$this->get_name()."'
                   value='on'
                   " . ($this->get_value() ? 'checked' : '') . "
                   class='single-field autosave'
                   " . $this->get_disabled_html_attr() . "
              />
              <span class='slider'></span>
             </label>
             
             "
            . $this->get_popup()
            . $this->get_field_wrapper_end();
    }

}
