<?php

namespace bpmj\wpidea\templates_system\templates\classic;

use bpmj\wpidea\templates_system\templates\base\Abstract_Cart_Template;
use bpmj\wpidea\View;

class Cart_Template extends Abstract_Cart_Template
{
    public function get_default_content()
    {
        return View::get_admin('/gutenberg/templates/classic/cart-template');
    }
}