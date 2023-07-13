<?php

namespace bpmj\wpidea\admin\settings\core\persistence;

use bpmj\wpidea\admin\settings\core\entities\fields\Abstract_Setting_Field;

interface Interface_Settings_Persistence
{
    public function get_value(Abstract_Setting_Field $field);

    public function save(Abstract_Setting_Field $field): void;
}