<?php

namespace bpmj\wpidea\templates_system\admin\modules\guide;

interface Interface_Templates_Guide
{
    public function print_before_layout_settings_info(): void;

    public function print_layout_settings_info(): void;

    public function print_color_settings_info(): void;

}