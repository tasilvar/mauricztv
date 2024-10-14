<?php

namespace bpmj\wpidea\templates_system\admin\blocks;

use bpmj\wpidea\View;

class Course_Progress_Block extends Block
{
    const BLOCK_NAME = 'wpi/course-progress-block';

    public function __construct() {
        parent::__construct();
        $this->title = __('Course Progress', BPMJ_EDDCM_DOMAIN);
    }

    public function get_content_to_render($atts)
    {
        $lesson_page_id = get_the_ID();
        $course_page_id = WPI()->courses->get_course_top_page( $lesson_page_id );
        $nonce = wp_create_nonce( 'wpidea_lesson_' . $course_page_id . '_' . $lesson_page_id );

        return View::get($this->get_template_path_base() . '/course/progress', [
            'lesson_page_id' => $lesson_page_id,
            'course_page_id' => $course_page_id,
            'nonce' => $nonce
        ]);
    }
}
