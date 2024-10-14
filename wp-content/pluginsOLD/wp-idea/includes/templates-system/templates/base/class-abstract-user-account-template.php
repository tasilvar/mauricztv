<?php

namespace bpmj\wpidea\templates_system\templates\base;

use bpmj\wpidea\templates_system\admin\blocks\{
    User_Account_Form_Block
};
use bpmj\wpidea\templates_system\templates\Template;

abstract class Abstract_User_Account_Template extends Template
{
    protected $registers_blocks = [
        User_Account_Form_Block::class,
    ];

    public function get_default_name()
    {
        return 'template_name.user_account_page';
    }
}
