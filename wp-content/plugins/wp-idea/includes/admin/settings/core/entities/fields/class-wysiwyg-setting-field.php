<?php

namespace bpmj\wpidea\admin\settings\core\entities\fields;

use bpmj\wpidea\helpers\Translator_Static_Helper;

class Wysiwyg_Setting_Field extends Abstract_Setting_Field
{
    public function render_to_string(): string
    {
        if (!$this->is_visible()) {
            return '';
        }

        return $this->get_field_wrapper_start()
            . $this->get_editor()
            . $this->get_field_wrapper_end();
    }

    private function get_editor_settings(): array
    {
        return [
            'teeny' => true,
            'textarea_name' => $this->get_name(),
            'textarea_rows' => 10,
            'default_editor' => 'tinymce'
        ];
    }

    private function get_editor(): string
    {
        ob_start();

        wp_editor($this->get_value(), $this->get_name(), $this->get_editor_settings());

        echo \_WP_Editors::enqueue_scripts();
        print_footer_scripts();
        echo \_WP_Editors::editor_js();

        return ob_get_clean();
    }
}
