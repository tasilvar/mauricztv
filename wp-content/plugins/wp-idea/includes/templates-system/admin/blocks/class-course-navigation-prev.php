<?php

namespace bpmj\wpidea\templates_system\admin\blocks;

use bpmj\wpidea\View;

class Course_Navigation_Prev extends Block
{
    const BLOCK_NAME = 'wpi/course-navigation-prev';

    public function __construct() {
        parent::__construct();
        $this->title = __('Previous lesson', BPMJ_EDDCM_DOMAIN);
    }

    public function get_content_to_render($atts)
    {
        return View::get($this->get_template_path_base() . '/course/navigation/prev');
    }
}
