<?php

namespace bpmj\wpidea\admin\pages\course_editor\core\fields;

use bpmj\wpidea\admin\settings\core\entities\fields\Abstract_Setting_Field;
use bpmj\wpidea\helpers\Translator_Static_Helper;

class Course_Start_Date_Field extends Abstract_Setting_Field
{
    private array $hour = [];
    private array $minutes = [];
    private string $separator = ' ';

    public function __construct(
        string $name,
        ?string $label = null,
        ?string $description = null,
        ?string $tooltip = null,
        ?array $hour = [],
        ?array $minutes = [],
        $value = null
    ) {
        $this->hour = $hour;
        $this->minutes = $minutes;
        parent::__construct($name, $label, $description, $tooltip, null, $value);
    }

    public function set_separator(string $separator): self
    {
        $this->separator = $separator;

        return $this;
    }

    public function render_to_string(): string
    {
        if (!$this->is_visible()) {
            return '';
        }


        $value = explode($this->separator, $this->get_value());

        $date = $value[0] ?? '';

        $hour = $this->parse_hour_to_string($value[1] ?? null);
        $minutes = $this->parse_minutes_to_string($value[1] ?? null);

        $hour_option = $this->get_options_html($this->hour, $hour);
        $minutes_option = $this->get_options_html($this->minutes, $minutes);



        return $this->get_field_wrapper_start()
            . "<input style='max-width:49%'
                    type='date'
                    name=''
                    id='" . $this->get_date_field_name() . "'
                    value='" . $date . "'
                    class='single-field'
                    " . $this->get_disabled_html_attr() . "
                    " . $this->get_readonly() . "
            />"
            . " &nbsp; ".Translator_Static_Helper::translate('course_editor.sections.general.access_start.at')." &nbsp; <select style='max-width:10%'
                    name=''
                    id='" . $this->get_hour_field_name() . "'
                    class='single-field wpi-select-field'
                    " . $this->get_disabled_html_attr() . "
            >
            " . $hour_option . "</select> &nbsp; : &nbsp; 
               <select style='max-width:10%'
                    name=''
                    id='" . $this->get_minutes_field_name() . "'
                    class='single-field wpi-select-field'
                    " . $this->get_disabled_html_attr() . "
                    >
                    " . $minutes_option . "</select>
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

    private function get_minutes_field_name(): string
    {
        return $this->get_name() . '_minutes';
    }

    private function parse_hour_to_string(?string $time): string
    {
        if (!$time) {
            return '';
        }

        $hour = explode(':', $time);
        return $hour[0] ?? '';
    }

    private function parse_minutes_to_string(?string $time): string
    {
        if (!$time) {
            return '';
        }

        $minutes = explode(':', $time);
        return $minutes[1] ?? '';
    }

    private function get_options_html(array $options, string $field_value): string
    {
        $html = '';
        foreach ($options as $value => $label) {
            $selected = '';
            if ((int)$value === (int)$field_value) {
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
                  showFieldButtons();
					$('#{$this->get_name()}').val(getHourMinutesAndFormatValue('{$this->get_hour_field_name()}', '{$this->get_minutes_field_name()}', this.value));
			  });
      
              $('#{$this->get_hour_field_name()}').on( 'change', function(e) {    
                  showFieldButtons();
					$('#{$this->get_name()}').val(getDateMinutesAndFormatValue('{$this->get_date_field_name()}', '{$this->get_minutes_field_name()}', this.value));
				});
              
              $('#{$this->get_minutes_field_name()}').on( 'change', function(e) {
                  showFieldButtons();
					$('#{$this->get_name()}').val(getDateHourAndFormatValue('{$this->get_date_field_name()}', '{$this->get_hour_field_name()}', this.value));
				});
              
              // update field values with real saved value
              $('#{$this->get_name()}').on( 'field-successfully-saved', function(e) {
                    let values = this.value.split('{$this->separator}');
                    let time = this.values.split(':');
                    $('#{$this->get_date_field_name()}').val(values[0] ?? '');
                    $('#{$this->get_hour_field_name()}').val(time[0] ?? '');
                    $('#{$this->get_minutes_field_name()}').val(time[1] ?? '');
				});
              
            const getDateMinutesAndFormatValue = (fieldDateName, fieldMinutesName, value) =>{
				let date = $('#'+fieldDateName).val();
                let minutes = $('#'+fieldMinutesName).val();
                
                if(!date){
                    return '';
                }
                
				return date+'{$this->separator}'+value+':'+minutes;
			};
            
            const getDateHourAndFormatValue = (fieldDateName, fieldHourName, value) =>{
                let date = $('#'+fieldDateName).val();
				let hour = $('#'+fieldHourName).val();
                
                if(!date){
                    return '';
                }
                
				return date+'{$this->separator}'+hour+':'+value;
			};
           
            const getHourMinutesAndFormatValue = (fieldHourName, fieldMinutesName, value) =>{
				let hour = $('#'+fieldHourName).val();
                let minutes = $('#'+fieldMinutesName).val();
                
                if(!value){
                    return '';
                }
                
				return value+'{$this->separator}'+hour+':'+minutes;
			};
            
            const showFieldButtons = () =>{
                let fieldWrapper = $('.single-field-wrapper[data-related-field=\"{$this->get_name()}\"]');
                
                const fieldButtonsBox = fieldWrapper.find('.field-buttons');
                
                let fieldSave = fieldButtonsBox.find('.single-field-save-button')
                let fieldCancel = fieldButtonsBox.find('.single-field-cancel-button')
    
                fieldSave.show();
                fieldCancel.show();
           }
           
           const hideFieldButtons = (fieldName) =>{
                let fieldWrapper = $('.single-field-wrapper[data-related-field=\"' + fieldName + '\"]');
                const fieldButtonsBox = fieldWrapper.find('.field-buttons');
                
                let fieldSave = fieldButtonsBox.find('.single-field-save-button')
                let fieldCancel = fieldButtonsBox.find('.single-field-cancel-button')
    
                fieldSave.hide();
                fieldCancel.hide();
           }
           
           const hideFieldErrors= (fieldName) =>{
                let fieldWrapper = $('.single-field-wrapper[data-related-field=\"' + fieldName + '\"]');
    
                fieldWrapper.removeClass('has-errors');
                let errorsNode = fieldWrapper.find('.validation-errors')
    
                errorsNode.text('');
                errorsNode.hide();
            }
           
            $('.single-field-cancel-button').on('click', function (e) {
                let fieldName = $(this).data('related-field')
                const fields = [
                    $('#' + fieldName)
                ];
                
                if(fieldName === '{$this->get_name()}') {
                    fields.push($('#{$this->get_date_field_name()}'));
                    fields.push($('#{$this->get_hour_field_name()}'));
                    fields.push($('#{$this->get_minutes_field_name()}'));
                }
                
                fields.forEach(field => {
                    let fieldInitVal = field.data('initial-value');
    
                    field.val(fieldInitVal);
                })
                
                hideFieldErrors(fieldName);
                hideFieldButtons(fieldName);
            });
           
    
           });
        </script>";
    }

}
