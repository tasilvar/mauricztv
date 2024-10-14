<?php

namespace bpmj\wpidea\templates_system\templates\classic;

use bpmj\wpidea\templates_system\templates\base\Abstract_User_Account_Template;
use bpmj\wpidea\View;

class User_Account_Template extends Abstract_User_Account_Template
{
    public function get_default_content()
    {
        return View::get_admin('/gutenberg/templates/classic/user-account-template');
    }
}
