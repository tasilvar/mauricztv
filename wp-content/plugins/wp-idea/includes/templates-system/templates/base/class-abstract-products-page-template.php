<?php

namespace bpmj\wpidea\templates_system\templates\base;

use bpmj\wpidea\helpers\Translator_Static_Helper;
use bpmj\wpidea\templates_system\admin\blocks\{
    Products_Block
};
use bpmj\wpidea\templates_system\templates\Template;

abstract class Abstract_Products_Page_Template extends Template
{
    protected $registers_blocks = [
        Products_Block::class
    ];

    public function get_default_name()
    {
        return 'template_name.products_page';
    }
}