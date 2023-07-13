<?php

namespace bpmj\wpidea\templates_system\groups\settings\fields;

use bpmj\wpidea\View;

class Template_Group_Settings_Field_Textarea extends Template_Group_Settings_Field
{
    public function get_html(): string
    {
        $params = ['field' => $this];

        return View::get_admin('/templates/settings/settings-field-textarea', $params);
    }
}