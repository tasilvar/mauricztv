<?php

namespace bpmj\wpidea\templates_system\templates\base;

use bpmj\wpidea\templates_system\admin\blocks\{
    Cart_Additional_Info_Block,
    Cart_Content_Block
};
use bpmj\wpidea\templates_system\templates\Template;

abstract class Abstract_Cart_Template extends Template
{
    protected $registers_blocks = [
        Cart_Content_Block::class,
        Cart_Additional_Info_Block::class
    ];

    public function get_default_name()
    {
        return 'template_name.cart_page';
    }
}