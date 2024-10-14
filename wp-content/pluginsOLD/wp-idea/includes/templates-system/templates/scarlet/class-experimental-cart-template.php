<?php

namespace bpmj\wpidea\templates_system\templates\scarlet;

use bpmj\wpidea\templates_system\admin\blocks\{Breadcrumbs_Block,
	Cart_Additional_Info_Block,
	Cart_Content_Block,
	Page_Title_Block};
use bpmj\wpidea\templates_system\templates\base\Abstract_Cart_Template;

class Experimental_Cart_Template extends Abstract_Cart_Template
{
    public function get_default_content()
    {
        return
            '<!-- wp:group --><div class="wp-block-group"><div class="wp-block-group__inner-container">' .
            Page_Title_Block::get_gutenberg_block_content() .
            Breadcrumbs_Block::get_gutenberg_block_content() .
            '</div></div><!-- /wp:group -->' .
            '<!-- wp:columns -->
        <div class="wp-block-columns"><<!-- wp:column {"width":"66.66%"} -->
        <div id="experimental-cart-column-left" class="wp-block-column" style="flex-basis:66.66%">' . Cart_Content_Block::get_gutenberg_block_content() . '</div>
        <!-- /wp:column -->
        
        <!-- wp:column {"width":"33.33%"} -->
        <div id="experimental-cart-column-right" class="wp-block-column" style="flex-basis:33.33%">' . Cart_Additional_Info_Block::get_gutenberg_block_content() . '</div>
        <!-- /wp:column --></div>
        <!-- /wp:columns -->';
    }

    public function is_full_page_template(): bool
    {
        return true;
    }

    public function get_default_name()
    {
    	return 'template_name.experimental_cart_page';
    }
}
