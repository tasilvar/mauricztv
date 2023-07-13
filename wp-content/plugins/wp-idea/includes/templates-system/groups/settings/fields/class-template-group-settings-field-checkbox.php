<?php

namespace bpmj\wpidea\templates_system\groups\settings\fields;

use bpmj\wpidea\View;

class Template_Group_Settings_Field_Checkbox extends Template_Group_Settings_Field
{
    public const VALUE_OFF = 'off';
    public const VALUE_ON = 'on';

    public function get_html(): string
    {
        $params = ['field' => $this];

        return View::get_admin('/templates/settings/settings-field-checkbox', $params);
    }

    public function is_checked(): bool
    {
        return !empty($this->get_value()) && $this->get_value() !== self::VALUE_OFF;
    }
}