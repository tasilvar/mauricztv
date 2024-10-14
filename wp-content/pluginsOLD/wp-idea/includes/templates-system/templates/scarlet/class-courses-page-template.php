<?php

namespace bpmj\wpidea\templates_system\templates\scarlet;

use bpmj\wpidea\templates_system\templates\base\Abstract_Products_Page_Template;
use bpmj\wpidea\templates_system\admin\blocks\{
    Products_Block,
    Products_Slider_Block
};

class Courses_Page_Template extends Abstract_Products_Page_Template
{
    protected $registers_blocks = [
        Products_Block::class,
        Products_Slider_Block::class
    ];

    public function get_default_content()
    {
        return '<!-- wp:group {"align":"full"} -->
        <div class="wp-block-group alignfull"><div class="wp-block-group__inner-container">
            ' . Products_Slider_Block::get_gutenberg_block_content() . '
        </div></div><!-- /wp:group -->
        '.
        '<!-- wp:group -->
        <div class="wp-block-group"><div class="wp-block-group__inner-container">
            ' . Products_Block::get_gutenberg_block_content() . '
        </div></div><!-- /wp:group -->';
    }
}