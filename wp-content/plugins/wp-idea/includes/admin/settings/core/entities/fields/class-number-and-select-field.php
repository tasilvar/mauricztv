<?php

namespace bpmj\wpidea\admin\settings\core\entities\fields;

class Number_And_Select_Field extends Abstract_Setting_Field
{
    private array $select_options = [];
    private string $separator = ' ';

    public function __construct(
        string $name,
        ?string $label = null,
        ?string $description = null,
        ?string $tooltip = null,
        ?array $select_options = [],
        $value = null
    ) {
        $this->select_options = $select_options;
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

        $number_value = $value[0] ?? '';

        $select_value = $value[1] ?? '';

        $options = $this->get_options_string($this->select_options, $select_value);

        return $this->get_field_wrapper_start()
            . "<input style='width:150px'
                    type='number'
                    name=''
                    id='" . $this->get_number_field_name() . "'
                    value='" . $number_value . "'
                    class='single-field'
                    " . $this->get_disabled_html_attr() . "
                    " . $this->get_readonly() . "
                    min='0'
            />"
            . "<select style='max-width:150px'
                    name=''
                    id='" . $this->get_select_field_name() . "'
                    class='single-field wpi-select-field'
                    " . $this->get_disabled_html_attr() . "
            >
            " . $options . "</select>
            <input type='hidden' id='" . $this->get_name() . "' name='" . $this->get_name() . "' data-initial-value='" . $this->get_value(
            ) . "' value='" . $this->get_value() . "'>"
            . $this->render_js_script()
            . $this->get_field_wrapper_end();
    }

    private function get_number_field_name(): string
    {
        return $this->get_name() . '_number';
    }

    private function get_select_field_name(): string
    {
        return $this->get_name() . '_select';
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
              $('#{$this->get_number_field_name()}').on( 'keyup change', function(e) { 
                  showFieldButtons();
					$('#{$this->get_name()}').val(getSelectAndFormatValue('{$this->get_select_field_name()}', this.value));
			  });
      
              $('#{$this->get_select_field_name()}').on( 'change', function(e) {  
                    showFieldButtons();
                    $('#{$this->get_name()}').val(getNumberAndFormatValue('{$this->get_number_field_name()}', this.value));
				});
      
              // update field values with real saved value
              $('#{$this->get_name()}').on( 'field-successfully-saved', function(e) {
                    let values = this.value.split('{$this->separator}');
                    $('#{$this->get_number_field_name()}').val(values[0] ?? '');
                    $('#{$this->get_select_field_name()}').val(values[1] ?? '');
				});
              
            const getSelectAndFormatValue = (fieldName, value) =>{
				let type = $('#'+fieldName).val();
				return value+'{$this->separator}'+type;
			};
            
            const getNumberAndFormatValue = (fieldName, value) =>{
				let time = $('#'+fieldName).val();
				return time+'{$this->separator}'+value;
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
                    fields.push($('#{$this->get_number_field_name()}'));
                    fields.push($('#{$this->get_select_field_name()}'));
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
