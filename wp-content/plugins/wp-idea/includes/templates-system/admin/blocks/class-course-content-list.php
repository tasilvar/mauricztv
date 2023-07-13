<?php

namespace bpmj\wpidea\templates_system\admin\blocks;

use bpmj\wpidea\View;

class Course_Content_List extends Block
{
    const BLOCK_NAME = 'wpi/course-content-list';

    public function __construct() {
        parent::__construct();
        $this->title = __('Course Content Navigation Block', BPMJ_EDDCM_DOMAIN);
    }

    public function get_content_to_render($atts)
    {
        $lesson_page_id = get_the_ID();
        $course_page_id = WPI()->courses->get_course_top_page( $lesson_page_id );
        return View::get('/course/content-list', [
            'course_page_id' => $course_page_id,
            'lesson_page_id' => $lesson_page_id,
        ]);
    }
}
