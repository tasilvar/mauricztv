<?php
namespace bpmj\wpidea\pages\renderers;

use bpmj\wpidea\templates_system\templates\Template;

class Old_Templates_Page_Renderer implements Interface_Page_Renderer
{
    public function get_current_page_template(): ?Template
    {
        return null;
    }
}