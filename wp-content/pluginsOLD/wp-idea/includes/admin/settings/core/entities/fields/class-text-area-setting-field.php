<?php
namespace bpmj\wpidea\admin\settings\core\entities\fields;

class Text_Area_Setting_Field extends Abstract_Setting_Field
{
    private ?string $placeholder = null;

    public function render_to_string(): string
    {
        if(!$this->is_visible()){
            return '';
        }

        return $this->get_field_wrapper_start()
            . "<textarea
                    type='text'
                    name='".$this->get_name()."'
                    id='".$this->get_name()."'
                    data-initial-value='" . htmlspecialchars($this->get_value()) . "'
                    class='single-field area-height'
                    placeholder='".$this->get_placeholder()."'
                    " . $this->get_disabled_html_attr() . "
            >".$this->get_value()."</textarea>"
            . $this->get_field_wrapper_end();
    }

    public function get_placeholder(): string
    {
        return $this->placeholder ?? '';
    }

    public function set_placeholder(?string $placeholder): self
    {
        $this->placeholder = $placeholder;

        return $this;
    }

}
