<?php

namespace bpmj\wpidea\templates_system\templates\scarlet;

use bpmj\wpidea\templates_system\templates\base\Abstract_Course_Module_Template;
use bpmj\wpidea\templates_system\admin\blocks\{
    Breadcrumbs_Block,
    Comments_Block,
    Course_Module_Lessons_List_Block,
    Course_Top_Bar_Block,
    Page_Content_Block,
    Page_Title_Block
};

class Course_Module_Template extends Abstract_Course_Module_Template
{
    public function get_default_content()
    {
        return '<!-- wp:group {"align":"full"} -->
            <div class="wp-block-group alignfull">
                <div class="wp-block-group__inner-container"> ' . Course_Top_Bar_Block::get_gutenberg_block_content() . '</div>
            </div>
        <!-- /wp:group -->
        <!-- wp:group -->
            <div class="wp-block-group">
                <div class="wp-block-group__inner-container"> ' . Page_Title_Block::get_gutenberg_block_content() . '</div>
            </div>
        <!-- /wp:group -->
        <!-- wp:group -->
            <div class="wp-block-group">
                <div class="wp-block-group__inner-container"> ' . Breadcrumbs_Block::get_gutenberg_block_content() . '</div>
            </div>
        <!-- /wp:group -->
        <!-- wp:group -->
            <div class="wp-block-group">
                <div class="wp-block-group__inner-container"> ' . Page_Content_Block::get_gutenberg_block_content() . '</div>
            </div>
        <!-- /wp:group -->
        <!-- wp:group -->
            <div class="wp-block-group">
                <div class="wp-block-group__inner-container"> ' . Course_Module_Lessons_List_Block::get_gutenberg_block_content() . '</div>
            </div>
        <!-- /wp:group -->
        <!-- wp:group -->
            <div class="wp-block-group">
                <div class="wp-block-group__inner-container"> ' . Comments_Block::get_gutenberg_block_content() . '</div>
            </div>
        <!-- /wp:group -->';
    }
}
