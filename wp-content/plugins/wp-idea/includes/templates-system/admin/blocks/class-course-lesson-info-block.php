<?php

namespace bpmj\wpidea\templates_system\admin\blocks;

use bpmj\wpidea\View;

class Course_Lesson_Info_Block extends Block
{
    const BLOCK_NAME = 'wpi/course-lesson-info-block';

    public function __construct() {
        parent::__construct();
        $this->title = __('Lesson Info', BPMJ_EDDCM_DOMAIN);
    }

    public function get_content_to_render($atts)
    {
        return View::get($this->get_template_path_base() . '/course/lesson-info');
    }
}
