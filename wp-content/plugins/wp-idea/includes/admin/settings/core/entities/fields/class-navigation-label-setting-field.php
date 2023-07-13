<?php

namespace bpmj\wpidea\admin\settings\core\entities\fields;

use bpmj\wpidea\admin\settings\core\collections\Additional_Fields_Collection;

class Navigation_Label_Setting_Field extends Abstract_Setting_Field
{
    private ?string $default_label = null;
    private ?string $label_1 = null;
    private ?string $label_2 = null;

    public function __construct(
        string $name,
        ?string $label = null,
        ?string $description = null,
        ?string $tooltip = null,
        ?Additional_Fields_Collection $additional_fields = null,
        $value = null,
        $default_label = null,
        $label_1 = null,
        $label_2 = null
    ) {
        $this->default_label = $default_label;
        $this->label_1 = $label_1;
        $this->label_2 = $label_2;
        parent::__construct($name, $label, $description, $tooltip, $additional_fields, $value);
    }

    public function render_to_string(): string
    {
        if (!$this->is_visible()) {
            return '';
        }

        $html_default_label = '';

        if($this->default_label){
            $html_default_label = '<label for="'.$this->get_name().'_0">
                <input type="radio" class="radio" id="'.$this->get_name().'_0" 
                name="'.$this->get_name().'" value="" checked="checked">
                '.$this->default_label.'</label><br>';
        }

        $checked_other_option = (!in_array($this->get_value(),['lesson','lesson_title']) && !empty($this->get_value())) ? 'checked="checked"' : '';

        return $this->get_field_wrapper_start() .
            '<fieldset class="navigation-label-field" style="">
                '.$html_default_label.'
                <label for="'.$this->get_name().'_1">
                <input ' . ($this->get_value() === 'lesson' ? 'checked="checked"' : '') .
            ' type="radio" class="radio" id="'.$this->get_name().'_1" 
                name="'.$this->get_name().'" value="lesson">
                '.$this->label_1.'</label><br>
                <label for="'.$this->get_name().'_2">
                <input ' . ($this->get_value() === 'lesson_title' ? 'checked="checked"' : '') . '
                type="radio" class="radio" id="'.$this->get_name().'_2" 
                name="'.$this->get_name().'" value="lesson_title">
                '.$this->label_2.'</label><br><label for="'.$this->get_name().'_3">
                <input ' . $checked_other_option . '
                type="radio" class="radio" id="'.$this->get_name().'_3" 
                name="'.$this->get_name().'" value="other">
                Inna</label> 
                <input type="text" name="'.$this->get_name().'" value="' . ($checked_other_option ? $this->get_value() : '') . '" 
                '. (!$checked_other_option ? 'disabled="disabled"' : '') .' class="_other_option_input">
                <br><pl class="wp_idea - description - field"></p>
            </fieldset>'
            . $this->get_field_wrapper_end();
    }
}
