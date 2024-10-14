<?php

namespace bpmj\wpidea\templates_system\admin\modules;

use bpmj\wpidea\pages\renderers\Interface_Page_Renderer;
use bpmj\wpidea\pages\renderers\New_Templates_Page_Renderer;
use bpmj\wpidea\pages\renderers\Old_Templates_Page_Renderer;
use bpmj\wpidea\templates_system\admin\modules\guide\Interface_Templates_Guide;
use bpmj\wpidea\templates_system\admin\modules\guide\New_Templates_Guide;
use bpmj\wpidea\templates_system\admin\modules\guide\Old_Templates_Guide;
use bpmj\wpidea\templates_system\admin\modules\settings_handlers\Interface_Templates_Settings_Handler;
use bpmj\wpidea\templates_system\admin\modules\settings_handlers\New_Templates_System_Settings_Handler;
use bpmj\wpidea\templates_system\admin\modules\settings_handlers\Old_Templates_System_Settings_Handler;
use bpmj\wpidea\templates_system\Templates_System;

class Old_Templates_System_Modules_Factory implements Interface_Templates_System_Modules_Factory
{
    private $templates_system_settings_renderer;

    private $templates_page_renderer;

    private $templates_guide;

    public function __construct(
        Old_Templates_System_Settings_Handler $old_templates_system_settings_handler,
        Old_Templates_Page_Renderer $old_templates_page_renderer,
        Old_Templates_Guide $old_templates_guide
    ) {
        $this->templates_system_settings_renderer = $old_templates_system_settings_handler;
        $this->templates_page_renderer = $old_templates_page_renderer;
        $this->templates_guide = $old_templates_guide;
    }

    public function get_settings_handler(): Interface_Templates_Settings_Handler
    {
        return $this->templates_system_settings_renderer;
    }

    public function get_page_renderer(): Interface_Page_Renderer
    {
        return $this->templates_page_renderer;
    }

    public function get_templates_guide(): Interface_Templates_Guide
    {
        return $this->templates_guide;
    }
}
