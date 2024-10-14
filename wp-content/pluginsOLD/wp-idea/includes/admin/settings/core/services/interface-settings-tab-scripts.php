<?php

namespace bpmj\wpidea\admin\settings\core\services;


interface Interface_Settings_Tab_Scripts
{
    public function register_script(string $save_single_field_url, string $save_configuration_group_fields_url, ?string $license_key_info_url = null): void;
}