<?php

namespace bpmj\wpidea\admin\pages\product_editor\core\fields;

use bpmj\wpidea\admin\settings\core\entities\fields\Abstract_Setting_Field;
use bpmj\wpidea\helpers\Translator_Static_Helper;

class Tags_Field extends Abstract_Setting_Field
{
    public function __construct(
        string $name,
        ?string $label = null,
        ?string $description = null,
        ?string $tooltip = null,
        $value = null
    ) {
        parent::__construct($name, $label, $description, $tooltip, $additional_settings_fields = null, $value);
    }

    public function render_to_string(): string
    {
        if (!$this->is_visible()) {
            return '';
        }

        return $this->get_field_wrapper_start()
            . '<input type="text" name="' . $this->get_name() . '" class="tags_mailings" value="' . $this->get_value() . '">'
            . $this->render_js_script()
            . $this->get_field_wrapper_end();
    }

    private function render_js_script(): string
    {
        return "
        <script>
           jQuery( document ).ready( function ( $ ) {
         
                $( '.tags_mailings' ).tagsInput({
                    'height': '65px',
                    'width': '100%',
                    'interactive': true,
                    'defaultText': bpmj_eddcm.add_tag,
                    'removeWithBackspace': true,
                    'placeholderColor': '#666666'
               });
               
           });
        </script>";
    }
}
