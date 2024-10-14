<?php

namespace bpmj\wpidea\admin\pages\product_editor\core\fields;

use bpmj\wpidea\admin\settings\core\entities\fields\Abstract_Setting_Field;
use bpmj\wpidea\helpers\Translator_Static_Helper;

class Multiselect_Mailings_Field extends Abstract_Setting_Field
{
    private array $mailer_lists = [];

    public function __construct(
        string $name,
        ?string $label = null,
        ?string $description = null,
        ?string $tooltip = null,
        $value = null
    ) {
        parent::__construct($name, $label, $description, $tooltip, $additional_settings_fields = null, $value);
    }

    public function set_mailer_lists(array $mailer_lists): self
    {
        $this->mailer_lists = $mailer_lists;

        return $this;
    }

    public function render_to_string(): string
    {
        if (!$this->is_visible()) {
            return '';
        }
        
        return $this->get_field_wrapper_start()
            . ' <div class="wpi-mailing-lists" style="width:100%;" data-mailer="' . $this->get_name() . '">
                    ' . $this->get_value_mailing_lists_string() . '
                 </div>

                 <button class="wpi-add-mailing-list btn-eddcm btn-eddcm-primary" data-mailer="' . $this->get_name(
            ) . '">' . Translator_Static_Helper::translate('service_editor.sections.mailings.add_next') . '</button>

                <div class="wpi-mailing-list-template wpi-mailing-list" data-mailer="' . $this->get_name() . '">
                    <select style="max-width:50%;" data-name="' . $this->get_name() . '[]" autocomplete="off" ' . $this->get_disabled_html_attr() . '>
                        <option hidden selected value="">' . Translator_Static_Helper::translate('service_editor.sections.mailings.select_list') . '</option>
                        ' . $this->get_options_mailer_lists_string() . '
                    </select>
                   
                    <a href="#" class="wpi-remove-mailing-list"><span class="dashicons dashicons-no"></span></a>
                    
                </div> '
            . $this->render_js_script()
            . $this->get_field_wrapper_end();
    }

    private function get_value_mailing_lists_string(): string
    {
        $html = '';

        if(empty($this->get_value())) {
            return $html;
        }

        foreach ($this->get_value() as $value) {
            $html .= '<div class="wpi-mailing-list" data-mailer="' . $this->get_name() . '">
                        <select name="' . $this->get_name() . '[]" autocomplete="off" ' . $this->get_disabled_html_attr() . ' style="max-width: 50%;">
                            <option hidden selected value="">' . Translator_Static_Helper::translate('service_editor.sections.mailings.select_list') . '</option>
                           ' . $this->get_options_mailer_lists_string($value) . '
                        </select>
                        <a href="#" class="wpi-remove-mailing-list"><span class="dashicons dashicons-no"></span></a>
                      </div>';
        }

        return $html;
    }

    private function get_options_mailer_lists_string(string $value_selected = ''): string
    {
        $html = '';
        foreach ($this->mailer_lists as $value => $label) {
            $selected = '';
            if ((string)$value === $value_selected) {
                $selected = "selected='selected'";
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
         
        const first_list_element_event = () =>{
			$('.wpi-add-mailing-list').trigger( 'click' );
			$('.wpi-mailing-lists').children().addClass('first-list');
			$('.wpi-add-mailing-list[data-mailer=\"' + $('.wpi-mailing-lists').data('mailer') + '\"]').addClass('none');
		};
        
        const remove_mailing_list = ( e ) =>{
            e.preventDefault();
   
			$(e.currentTarget).parent('div.wpi-mailing-list').remove();
          
			if ($( '.wpi-mailing-lists' ).children().length === 0) {
				first_list_element_event();
			}
		};

		$('.wpi-add-mailing-list').on( 'click', function ( e ) {
			e.preventDefault();

			let mailer_name = $(this).data('mailer'),
				template = $('.wpi-mailing-list-template[data-mailer=\"' + mailer_name + '\"]').clone();
         
			template.removeClass('wpi-mailing-list-template');
			let select = template.find('select');
			select.attr('name', select.data('name'));
			select.on('change', function () {
				$('.wpi-mailing-lists').children().removeClass('first-list');
				$('.wpi-add-mailing-list[data-mailer=\"' + $('.wpi-mailing-lists').data('mailer') + '\"]').removeClass('none');
			});
         
			template.find( 'a.wpi-remove-mailing-list' ).on( 'click', remove_mailing_list );
   
			$('.wpi-mailing-lists[data-mailer=\"' + mailer_name + '\"]').append(template);
		} );

		$('a.wpi-remove-mailing-list').on( 'click', remove_mailing_list);

		if ($( '.wpi-mailing-lists' ).children().length === 0) {
			first_list_element_event();
		}
               
           });
        </script>";
    }
}
