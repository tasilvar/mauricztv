<?php
namespace bpmj\wpidea\wolverine\course\progress\events;

use bpmj\wpidea\settings\LMS_Settings;
use bpmj\wpidea\wolverine\event\EventHandler;
use bpmj\wpidea\Course_Progress;

class CheckPreviousLessonAsDone extends EventHandler
{
    public static $finished = null;

    public function run(array $data)
    {
        $lessonPageId = $data['lesson_id'];
        $coursePanelPageId = WPI()->courses->get_course_top_page($lessonPageId);

        $userProgress = new Course_Progress($coursePanelPageId, $data['previous_lesson_id'], false);

        if ( $userProgress->is_tracking_enabled() && LMS_Settings::get_option('auto_progress' ) ) {
            $previousLesson = HelperCheckPreviousLessons::getPreviousLesson($lessonPageId);
            if (is_null($previousLesson))
                return;

            $userProgress->read_progress();
            if (!$userProgress->is_lesson_finished()) {
                self::$finished = $lessonPageId;
                $userProgress->toggle_finished(true);
                $userProgress->read_progress();
            }
        }
    }
}
