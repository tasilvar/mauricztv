<?php

namespace bpmj\wpidea\templates_system\templates\scarlet;

use bpmj\wpidea\templates_system\admin\blocks\{Breadcrumbs_Block,
	Course_Banner_Block,
	Course_Offer_Page_Content_Block,
	Course_Offer_Page_Details_Block,
	Opinions_Block};
use bpmj\wpidea\templates_system\templates\base\Abstract_Course_Offer_Page_Template;

class Course_Offer_Page_Template extends Abstract_Course_Offer_Page_Template
{
    public function __construct()
    {
        $this->registers_blocks = array_merge($this->registers_blocks, [
            Course_Banner_Block::class
        ]);

        parent::__construct();
    }

    public function get_default_content()
    {
        return '<!-- wp:group {"align":"full"} -->
        <div class="wp-block-group alignfull"><div class="wp-block-group__inner-container">
        ' . Course_Banner_Block::get_gutenberg_block_content() . '</div></div>
        <!-- /wp:group -->' .

        '<!-- wp:group -->
        <div class="wp-block-group"><div class="wp-block-group__inner-container">' .
            Breadcrumbs_Block::get_gutenberg_block_content() . 
        '</div></div><!-- /wp:group -->    
        
        <!-- wp:columns -->
        <div class="wp-block-columns">
        <!-- wp:column {"width":66.66} -->
        <div class="wp-block-column" style="flex-basis:66.66%">
            ' . Course_Offer_Page_Content_Block::get_gutenberg_block_content() . '
            
            ' . Opinions_Block::get_gutenberg_block_content() . '  
        </div><!-- /wp:column -->
        
        <!-- wp:column {"width":33.33} -->
        <div class="wp-block-column" style="flex-basis:33.33%">
            ' . Course_Offer_Page_Details_Block::get_gutenberg_block_content() . '
        </div><!-- /wp:column --></div>
        <!-- /wp:columns -->
        ';
    }
}