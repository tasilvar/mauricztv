<?php

namespace bpmj\wpidea\templates_system\templates\scarlet;

use bpmj\wpidea\modules\app_view\api\App_View_API_Static_Helper;
use bpmj\wpidea\templates_system\admin\blocks\{Breadcrumbs_Block,
	Comments_Block,
	Course_Content_List,
	Course_Files_Block,
	Course_Navigation,
	Course_Quiz_Content_Block,
	Course_Top_Bar_Block,
	Page_Title_Block};
use bpmj\wpidea\templates_system\templates\base\Abstract_Course_Quiz_Template;

class Course_Quiz_Template extends Abstract_Course_Quiz_Template
{
    public function get_default_content()
    {
        return '<!-- wp:group {"align":"full"} -->
            <div class="wp-block-group alignfull">
                <div class="wp-block-group__inner-container"> ' . Course_Top_Bar_Block::get_gutenberg_block_content() . '</div>
            </div>
        <!-- /wp:group -->
        <!-- wp:columns -->
            <div class="wp-block-columns">
                <!-- wp:column {"width":58.00} -->
                    <div class="wp-block-column" style="flex-basis:58%"> ' . Page_Title_Block::get_gutenberg_block_content() . '</div>
                <!-- /wp:column -->
                <!-- wp:column {"width":42.00} -->
                    <div class="wp-block-column" style="flex-basis:42%"> ' . Course_Navigation::get_gutenberg_block_content() . '</div>
                <!-- /wp:column -->
            </div>
        <!-- /wp:columns -->
        <!-- wp:group -->
            <div class="wp-block-group">
                <div class="wp-block-group__inner-container"> ' . Breadcrumbs_Block::get_gutenberg_block_content() . '</div>
            </div>
        <!-- /wp:group -->
        <!-- wp:group -->
            <div class="wp-block-group">
                <div class="wp-block-group__inner-container"> ' . Course_Quiz_Content_Block::get_gutenberg_block_content() . '</div>
            </div>
        <!-- /wp:group -->
        <!-- wp:group -->
            <div class="wp-block-group">
                <div class="wp-block-group__inner-container"> ' . Course_Files_Block::get_gutenberg_block_content() . '</div>
            </div>
        <!-- /wp:group -->
        <!-- wp:group -->
            <div class="wp-block-group">
                <div class="wp-block-group__inner-container"> ' . Course_Content_List::get_gutenberg_block_content() . '</div>
            </div>
        <!-- /wp:group -->
        <!-- wp:group -->
            <div class="wp-block-group">
                <div class="wp-block-group__inner-container"> ' . Comments_Block::get_gutenberg_block_content() . '</div>
            </div>
        <!-- /wp:group -->';
    }

    public function before_render(): void
    {
        App_View_API_Static_Helper::render_quiz_header();
    }
}
