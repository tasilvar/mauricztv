<?php

namespace bpmj\wpidea\admin\pages\product_editor\core\fields;

use bpmj\wpidea\admin\settings\core\entities\fields\Abstract_Setting_Field;

class Special_Offer_Dates_Field extends Abstract_Setting_Field
{
    private array $options = [];

    public function __construct(
        string $name,
        ?string $label = null,
        ?string $description = null,
        ?string $tooltip = null,
        ?array $options = [],
        $value = null
    ) {
        $this->options = $options;
        parent::__construct($name, $label, $description, $tooltip, null, $value);
    }

    public function render_to_string(): string
    {
        if (!$this->is_visible()) {
            return '';
        }

        $value = explode(' ', $this->get_value());

        $value1 = $value[0] ?? '';

        $value2 = $this->get_hour_of_time_to_string($value[1] ?? null);

        $options = $this->get_options_string($this->options, $value2);

        return $this->get_field_wrapper_start()
            . "<input style='max-width:150px;'
                    type='date'
                    name=''
                    id='" . $this->get_date_field_name() . "'
                    value='" . $value1 . "'
                    class='single-field'
                    " . $this->get_disabled_html_attr() . "
                    " . $this->get_readonly() . "
            />"
            . "<select style='max-width:150px;'
                    name=''
                    id='" . $this->get_hour_field_name() . "'
                    class='single-field wpi-select-field'
                    " . $this->get_disabled_html_attr() . "
            >
            " . $options . "</select>
            <input type='hidden' id='" . $this->get_name() . "' name='" . $this->get_name() . "' value='" . $this->get_value() . "'>"
            . $this->render_js_script()
            . $this->get_field_wrapper_end();
    }

    private function get_date_field_name(): string
    {
        return $this->get_name() . '_date';
    }

    private function get_hour_field_name(): string
    {
        return $this->get_name() . '_hour';
    }

    private function get_hour_of_time_to_string(?string $time): string
    {
        if (!$time) {
            return '';
        }

        $hour = explode(':', $time);
        return $hour[0] ?? '';
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
              $('#{$this->get_date_field_name()}').on( 'change', function(e) { 
					$('#{$this->get_name()}').val(getTimeAndFormatValue('{$this->get_hour_field_name()}', this.value));
			  });
      
              $('#{$this->get_hour_field_name()}').on( 'change', function(e) {    
					$('#{$this->get_name()}').val(getDateAndFormatValue('{$this->get_date_field_name()}', this.value));
				});
              
            const getTimeAndFormatValue = (fieldName, value) =>{
				let hour = $('#'+fieldName).val();
                
                if(!value){
                    return '';
                }
                
				return value+' '+hour+':00:00';
			};
            
            const getDateAndFormatValue = (fieldName, value) =>{
				let date = $('#'+fieldName).val();
                
                if(!date){
                    return '';
                }
                
				return date+' '+value+':00:00';
			};
           });
        </script>";
    }

}
