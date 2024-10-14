<?php

namespace bpmj\wpidea\templates_system\admin\blocks;

use bpmj\wpidea\View;

class Course_Top_Bar_Block extends Block
{
    const BLOCK_NAME = 'wpi/course-top-bar';

    public function __construct()
    {
        parent::__construct();
        $this->title = __('Course Top Bar', BPMJ_EDDCM_DOMAIN);
    }

    public function get_content_to_render($atts)
    {
        return View::get('/course/top-bar');
    }
}
