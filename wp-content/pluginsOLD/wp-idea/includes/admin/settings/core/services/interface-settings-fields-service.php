<?php

namespace bpmj\wpidea\admin\settings\core\services;

use bpmj\wpidea\admin\settings\core\entities\fields\Abstract_Setting_Field;

interface Interface_Settings_Fields_Service
{
    public function update_field(Abstract_Setting_Field $field): void;

    public function get_field_value(Abstract_Setting_Field $field);
}