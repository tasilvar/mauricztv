<?php

namespace bpmj\wpidea\templates_system\groups\settings\fields;

use bpmj\wpidea\View;

class Template_Group_Settings_Field_Font_Select extends Template_Group_Settings_Field
{
    private $ajax_get_options_action;

    public function get_html(): string
    {
        $params = ['field' => $this];

        return View::get_admin('/templates/settings/settings-field-font-select', $params);
    }

    public function set_ajax_get_options_action(string $ajax_get_options_action): self
    {
        $this->ajax_get_options_action = $ajax_get_options_action;

        return $this;
    }

    public function get_ajax_get_options_action(): ?string
    {
        return $this->ajax_get_options_action;
    }
}