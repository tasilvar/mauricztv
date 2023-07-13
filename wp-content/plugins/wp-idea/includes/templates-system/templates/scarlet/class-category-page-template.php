<?php
namespace bpmj\wpidea\templates_system\templates\scarlet;

use bpmj\wpidea\templates_system\templates\base\Abstract_Category_Page_Template;
use bpmj\wpidea\templates_system\admin\blocks\{
    Products_Block,
    Archive_Title_Block
};
use bpmj\wpidea\View;

class Category_Page_Template extends Abstract_Category_Page_Template
{
    protected $registers_blocks = [
        Archive_Title_Block::class,
        Products_Block::class
    ];
    
    public function get_default_content()
    {
        return View::get_admin('/gutenberg/templates/scarlet/archive-page-template');
    }

}