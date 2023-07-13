<?php

namespace bpmj\wpidea\templates_system\groups\settings\fields;

use bpmj\wpidea\View;

class Template_Group_Settings_Field_Select extends Template_Group_Settings_Field
{
    private $options = [];

    public function get_html(): string
    {
        $params = ['field' => $this];

        return View::get_admin('/templates/settings/settings-field-select', $params);
    }

    public function get_options(): array
    {
        return $this->options;
    }

    public function set_options(array $options): self
    {
        $this->options = $options;

        return $this;
    }
}