<?php

namespace bpmj\wpidea\templates_system\templates\classic;

use bpmj\wpidea\templates_system\templates\base\Abstract_Course_Module_Template;
use bpmj\wpidea\View;

class Course_Module_Template extends Abstract_Course_Module_Template
{
    public function get_default_content()
    {
        return View::get_admin('/gutenberg/templates/classic/course-module-template');
    }
}
