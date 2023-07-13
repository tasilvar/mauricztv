<?php

namespace bpmj\wpidea\templates_system\admin\blocks;

use bpmj\wpidea\View;

class Course_Panel_Lessons_Modules_Block extends Block
{
    const BLOCK_NAME = 'wpi/panel-tiles';

    public function __construct() {
        parent::__construct();

        $this->title = __('Lessons Modules Block', BPMJ_EDDCM_DOMAIN);
    }

    public function get_content_to_render($atts)
    {
        $course_page_id = WPI()->courses->get_course_top_page( get_the_ID() );
        $modules = WPI()->courses->get_course_level1_modules_or_lessons( $course_page_id );

        return View::get($this->get_template_path_base() . '/course-panel/tiles', [
            'modules' => $modules,
        ]);
    }
}
