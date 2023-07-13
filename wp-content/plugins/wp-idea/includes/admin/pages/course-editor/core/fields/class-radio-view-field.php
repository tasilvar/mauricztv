<?php

namespace bpmj\wpidea\admin\pages\course_editor\core\fields;

use bpmj\wpidea\admin\settings\core\collections\Additional_Fields_Collection;
use bpmj\wpidea\admin\settings\core\entities\fields\Abstract_Setting_Field;

class Radio_View_Field extends Abstract_Setting_Field
{
    private ?string $default_label = null;
    private ?string $label_1 = null;
    private array $options = [];

    public function __construct(
        string $name,
        ?string $label = null,
        ?string $description = null,
        ?string $tooltip = null,
        ?Additional_Fields_Collection $additional_fields = null,
        $value = null,
        ?string $default_label = null,
        ?array $options = []
    ) {
        $this->default_label = $default_label;
        $this->options = $options;

        parent::__construct($name, $label, $description, $tooltip, $additional_fields, $value);
    }

    public function render_to_string(): string
    {
        if (!$this->is_visible()) {
            return '';
        }

        $html_default_label = '';

        if($this->default_label){
            $html_default_label = '<label for="'.$this->get_name().'_deafult">
                <input type="radio" class="radio" id="'.$this->get_name().'_deafult"
                name="'.$this->get_name().'" value="" checked="checked"' . ' ' . $this->get_disabled_html_attr() . '>
                '.$this->default_label.'</label><br>';
        }

        return $this->get_field_wrapper_start() .
            '<fieldset class="navigation-label-field" style="">
                '. $html_default_label .'
                ' . $this->get_radio_fields() . '
            </fieldset>'
            . $this->get_field_wrapper_end();
    }

    private function get_radio_fields(): string
    {
        $html = '';

        foreach($this->options as $value => $label){
            $checked = '';
            if ((string)$value === $this->get_value()) {
                $checked = 'checked="checked"';
            }

            $html .= '<label for="'.$this->get_name().'_'.$value.'">
                <input type="radio" class="radio" id="'.$this->get_name().'_'.$value.'" 
                name="'.$this->get_name().'" value="'.$value.'" '.$checked . ' ' . $this->get_disabled_html_attr() . '>
                '.$label.'</label><br>';
        }

        return $html;
    }
}
