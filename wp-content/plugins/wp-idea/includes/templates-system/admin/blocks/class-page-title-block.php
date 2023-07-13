<?php
namespace bpmj\wpidea\templates_system\admin\blocks;


use bpmj\wpidea\View;

class Page_Title_Block extends Block
{
    const BLOCK_NAME = 'wpi/page-title';

    public function __construct() {
        parent::__construct();
        
        $this->title = __('Page Title', BPMJ_EDDCM_DOMAIN);
    }
    
    public function get_content_to_render($atts)
    {
        $title = apply_filters('bpmj_eddcm_page_title_block_title', get_the_title());

        return View::get($this->get_template_path_base() . '/page/title', [
            'title' => $title
        ]);
    }
}