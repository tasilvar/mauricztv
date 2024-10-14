<?php
namespace bpmj\wpidea\templates_system\admin\blocks;

use bpmj\wpidea\View;

class Cart_Content_Block extends Block
{
    const BLOCK_NAME = 'wpi/order-form';

    public function __construct() {
        parent::__construct();
        
        $this->title = __('Order Form', BPMJ_EDDCM_DOMAIN);
    }
    
    public function get_content_to_render($atts)
    {
        return View::get($this->get_template_path_base() . '/cart/cart-content');
    }
}