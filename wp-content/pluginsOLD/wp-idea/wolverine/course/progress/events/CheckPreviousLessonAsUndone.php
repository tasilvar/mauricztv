<?php

namespace bpmj\wpidea\wolverine\course\progress\events;

use bpmj\wpidea\settings\LMS_Settings;
use bpmj\wpidea\wolverine\event\EventHandler;
use bpmj\wpidea\Course_Progress;

class CheckPreviousLessonAsUndone extends EventHandler
{
    public function run(array $data)
    {
        $lessonPageId = $data['lesson_id'];
        $coursePanelPageId = WPI()->courses->get_course_top_page($lessonPageId);

        $previousLesson = HelperCheckPreviousLessons::getPreviousLesson($lessonPageId);

        $userProgress = new Course_Progress($coursePanelPageId, $previousLesson->ID, false);
        if ( $userProgress->is_tracking_enabled() && LMS_Settings::get_option('auto_progress' ) ) {
            if (is_null($previousLesson))
                return;


            $userProgress->read_progress();
            if ($userProgress->is_lesson_finished()) {
                $userProgress->toggle_finished(false);
                $userProgress->read_progress();
            }

            wp_send_json_success(['status' => 'ok']);
        }
    }
}
