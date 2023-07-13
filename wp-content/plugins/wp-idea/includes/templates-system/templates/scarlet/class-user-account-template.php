<?php

namespace bpmj\wpidea\templates_system\templates\scarlet;

use bpmj\wpidea\templates_system\templates\base\Abstract_User_Account_Template;
use bpmj\wpidea\templates_system\admin\blocks\{
    Breadcrumbs_Block,
    Page_Title_Block,
    User_Account_Form_Block
};

class User_Account_Template extends Abstract_User_Account_Template
{
    public function get_default_content()
    {
        return '<!-- wp:group -->
            <div class="wp-block-group">
                <div class="wp-block-group__inner-container">' . Page_Title_Block::get_gutenberg_block_content() . '</div>
            </div>
        <!-- /wp:group -->
        <!-- wp:group -->
            <div class="wp-block-group">
                <div class="wp-block-group__inner-container">' . Breadcrumbs_Block::get_gutenberg_block_content() . '</div>
            </div>
        <!-- /wp:group -->
        <!-- wp:group -->
            <div class="wp-block-group">
                <div class="wp-block-group__inner-container">' . User_Account_Form_Block::get_gutenberg_block_content() . '</div>
            </div>
        <!-- /wp:group -->';
    }
}
