<?php

namespace bpmj\wpidea\templates_system\admin\blocks;

use bpmj\wpidea\View;

class Course_Panel_Description_Block extends Block
{
    const BLOCK_NAME = 'wpi/panel-course-description';

    public function __construct() {
        parent::__construct();

        $this->title = __('Course Description Block', BPMJ_EDDCM_DOMAIN);
    }

    public function get_content_to_render($atts)
    {
        $lesson_page_id = get_the_ID();
        $course_page_id = WPI()->courses->get_course_top_page( $lesson_page_id );
        $show_video = WPI()->templates->get_meta( 'video_mode' ) === 'on';
        $video_url = WPI()->templates->get_meta( 'video' );
        $first_lesson = WPI()->courses->get_first_lesson( $course_page_id );

        return View::get($this->get_template_path_base() . '/course-panel/course-description', [
            'show_video' => $show_video,
            'video_url' => $video_url,
            'first_lesson' => $first_lesson,
        ]);
    }
}
