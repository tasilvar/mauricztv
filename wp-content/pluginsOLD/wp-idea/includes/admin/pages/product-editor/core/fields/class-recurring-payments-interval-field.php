<?php

namespace bpmj\wpidea\admin\pages\product_editor\core\fields;

use bpmj\wpidea\admin\settings\core\entities\fields\Abstract_Setting_Field;

class Recurring_Payments_Interval_Field extends Abstract_Setting_Field
{
    private array $payments_intervals = [];
    private array $payments_units = [];

    public function __construct(
        string $name,
        ?string $label = null,
        ?string $description = null,
        ?string $tooltip = null,
        ?array $payments_intervals = [],
        ?array $payments_units = [],
        $value = null
    ) {
        $this->payments_intervals = $payments_intervals;
        $this->payments_units = $payments_units;
        parent::__construct($name, $label, $description, $tooltip, null, $value);
    }

    public function render_to_string(): string
    {
        if (!$this->is_visible()) {
            return '';
        }

        $value = explode(' ', $this->get_value());

        $payments_interval_value = $value[0] ?? '';
        $payments_unit_value = $value[1] ?? '';

        $payments_intervals = $this->get_options_string($this->payments_intervals, $payments_interval_value);
        $payments_units = $this->get_options_string($this->payments_units, $payments_unit_value);

        return $this->get_field_wrapper_start()
            . "<select style='max-width:49%'
                    name=''
                    id='" . $this->get_number_field_name() . "'
                    class='single-field wpi-select-field'
                    " . $this->get_disabled_html_attr() . "
            >
            " . $payments_intervals . "</select>"
            . "<select style='max-width:49%'
                    name=''
                    id='" . $this->get_unit_field_name() . "'
                    class='single-field wpi-select-field'
                    " . $this->get_disabled_html_attr() . "
            >
            " . $payments_units . "</select>
            <input type='hidden' id='" . $this->get_name() . "' name='" . $this->get_name() . "' value='" . $this->get_value() . "'>"
            . $this->render_js_script()
            . $this->get_field_wrapper_end();
    }

    private function get_number_field_name(): string
    {
        return $this->get_name() . '_number';
    }

    private function get_unit_field_name(): string
    {
        return $this->get_name() . '_unit';
    }

    private function get_options_string(array $options, string $field_value): string
    {
        $html = '';
        foreach ($options as $value => $label) {
            $selected = '';
            if ((string)$value === $field_value) {
                $selected = 'selected="selected"';
            }
            $html .= "<option {$selected} value='{$value}'>{$label}</option>";
        }
        return $html;
    }

    private function render_js_script(): string
    {
        return "
        <script>
           jQuery( document ).ready( function ( $ ) {
              $('#{$this->get_number_field_name()}').on( 'change', function(e) { 
					$('#{$this->get_name()}').val(getUnitAndFormatValue('{$this->get_unit_field_name()}', this.value));
			  });
      
              $('#{$this->get_unit_field_name()}').on( 'change', function(e) {    
					$('#{$this->get_name()}').val(getNumberAndFormatValue('{$this->get_number_field_name()}', this.value));
				});
              
            const getUnitAndFormatValue = (fieldName, value) =>{
				let unit = $('#'+fieldName).val();
				return value+' '+unit;
			};
            
            const getNumberAndFormatValue = (fieldName, value) =>{
				let number = $('#'+fieldName).val();
				return number+' '+value;
			};
           });
        </script>";
    }
}
