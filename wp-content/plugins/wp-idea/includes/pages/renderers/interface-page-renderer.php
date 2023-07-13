<?php
namespace bpmj\wpidea\pages\renderers;

use bpmj\wpidea\templates_system\templates\Template;

interface Interface_Page_Renderer
{
    public function get_current_page_template(): ?Template;
}