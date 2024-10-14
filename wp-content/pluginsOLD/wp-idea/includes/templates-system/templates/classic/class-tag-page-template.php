<?php

namespace bpmj\wpidea\templates_system\templates\classic;

use bpmj\wpidea\templates_system\templates\base\Abstract_Tag_Page_Template;
use bpmj\wpidea\View;

class Tag_Page_Template extends Abstract_Tag_Page_Template
{
    public function get_default_content()
    {
        return View::get_admin('/gutenberg/templates/classic/products-page-template');
    }
}