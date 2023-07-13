<?php

namespace bpmj\wpidea\templates_system\admin\modules;

use bpmj\wpidea\pages\renderers\Interface_Page_Renderer;
use bpmj\wpidea\templates_system\admin\modules\guide\Interface_Templates_Guide;
use bpmj\wpidea\templates_system\admin\modules\settings_handlers\Interface_Templates_Settings_Handler;

interface Interface_Templates_System_Modules_Factory
{
    public function get_settings_handler(): Interface_Templates_Settings_Handler;

    public function get_page_renderer(): Interface_Page_Renderer;

    public function get_templates_guide(): Interface_Templates_Guide;
}
