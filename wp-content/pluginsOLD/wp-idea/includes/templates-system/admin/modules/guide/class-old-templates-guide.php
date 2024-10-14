<?php

namespace bpmj\wpidea\templates_system\admin\modules\guide;

use bpmj\wpidea\templates_system\Templates_System;
use bpmj\wpidea\View;

class Old_Templates_Guide implements Interface_Templates_Guide
{
    private $templates_system;

    public function __construct(
        Templates_System $templates_system
    ) {
        $this->templates_system = $templates_system;
    }
    public function print_color_settings_info(): void
    {
        // don't print anything
    }

    public function print_before_layout_settings_info(): void
    {
        if($this->templates_system->is_new_templates_system_disabled_by_user()) {
            echo View::get_admin('/templates/guide/new-templates-disabled');
            return;
        }

        echo View::get_admin('/templates/guide/new-templates-available');
    }

    public function print_layout_settings_info(): void
    {
        // don't print anything
    }
}