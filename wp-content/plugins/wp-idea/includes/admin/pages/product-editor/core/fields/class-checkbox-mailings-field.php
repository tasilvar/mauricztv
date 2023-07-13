<?php

namespace bpmj\wpidea\admin\pages\product_editor\core\fields;

use bpmj\wpidea\admin\settings\core\entities\fields\Abstract_Setting_Field;
use bpmj\wpidea\helpers\Translator_Static_Helper;

class Checkbox_Mailings_Field extends Abstract_Setting_Field
{
    private array $mailer_lists = [];
    private ?string $field_name_pair = null;

    public function __construct(
        string $name,
        ?string $label = null,
        ?string $description = null,
        ?string $tooltip = null,
        $value = null
    ) {
        parent::__construct($name, $label, $description, $tooltip, $additional_settings_fields = null, $value);
    }

    public function set_mailer_lists (array $mailer_lists): self
    {
        $this->mailer_lists = $mailer_lists;

        return $this;
    }

    public function set_pair(string $field_name_pair): self
    {
        $this->field_name_pair = $field_name_pair;

        return $this;
    }

    public function render_to_string(): string
    {
        if (!$this->is_visible()) {
            return '';
        }

        return $this->get_field_wrapper_start() .
            $this->get_checkboxes_from_lists_string()
            . $this->get_field_wrapper_end();
    }

    private function get_checkboxes_from_lists_string(): string
    {
        $html = '';

        if (!is_array($this->mailer_lists) || empty($this->mailer_lists)) {
            return Translator_Static_Helper::translate('service_editor.sections.mailings.empty_lists');
        }
        foreach ($this->mailer_lists as $id => $list) {
            $checked = is_array($this->get_value()) && in_array($id, $this->get_value()) ? 'checked="checked"' : '';
            $html .= '<div style="width:100%;"><label for="' . $this->get_name() . '_' . $id . '" class="checkbox">
                            <input 
                                type="checkbox" value="' . $id . '" 
                                name="' . $this->get_name() . '[]" 
                                class="single-field checkbox-double"
                                style="margin-bottom:9px;"
                                id="' . $this->get_name() . '_' . $id . '" ' . $checked . '
                                ' . $this->get_data_pair($id) . '
                             > '
                . $list
                . '</label></div>';
        }

        return $html;
    }

    private function get_data_pair(string $id): string
    {
        if(!$this->field_name_pair){
            return '';
        }

        return 'data-pair="' . $this->field_name_pair . '_' . $id . '"';
    }
}
