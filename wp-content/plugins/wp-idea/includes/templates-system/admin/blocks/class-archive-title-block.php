<?php
namespace bpmj\wpidea\templates_system\admin\blocks;


use bpmj\wpidea\View;

class Archive_Title_Block extends Block
{
    const BLOCK_NAME = 'wpi/archive-title';

    public function __construct() {
        parent::__construct();
        
        $this->title = __('Archive Title', BPMJ_EDDCM_DOMAIN);
    }
    
    public function get_content_to_render($atts)
    {
        return View::get($this->get_template_path_base() . '/page/title', [
            'title' => single_tag_title('', false)
        ]);
    }
}