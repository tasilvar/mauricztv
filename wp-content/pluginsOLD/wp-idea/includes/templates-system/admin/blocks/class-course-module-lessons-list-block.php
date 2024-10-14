<?php

namespace bpmj\wpidea\templates_system\admin\blocks;

use bpmj\wpidea\View;

class Course_Module_Lessons_List_Block extends Block
{
    const BLOCK_NAME = 'wpi/course-module-lessons-list';

    public function __construct() {
        parent::__construct();

        $this->title = __('Course Module Lessons', BPMJ_EDDCM_DOMAIN);
    }

    public function get_content_to_render($atts)
    {
        return View::get($this->get_template_path_base() . '/module/lessons-list');
    }
}
