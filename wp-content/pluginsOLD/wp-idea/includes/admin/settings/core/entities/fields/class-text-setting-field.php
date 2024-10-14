<?php
namespace bpmj\wpidea\admin\settings\core\entities\fields;

class Text_Setting_Field extends Abstract_Setting_Field
{
    private ?string $placeholder = null;
    private ?int $max_length = null;

    public function render_to_string(): string
    {
        if(!$this->is_visible()){
            return '';
        }

        return $this->get_field_wrapper_start()
            . "<input
                    type='text'
                    name='".$this->get_name()."'
                    id='".$this->get_name()."'
                    value='".$this->get_value()."'
                    placeholder='".$this->get_placeholder()."'
                    data-initial-value='".$this->get_value()."'
                    class='single-field'
                    ".$this->get_disabled_html_attr()."
                    ".$this->get_readonly()."
                    " . $this->get_max_length() . "
            />"
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

    public function set_max_length(?int $max_length): self
    {
        $this->max_length = $max_length;
        return $this;
    }

    public function get_max_length(): string
    {
        if (empty($this->max_length)) {
            return '';
        }

        return "maxLength='" . $this->max_length . "'";
    }
}