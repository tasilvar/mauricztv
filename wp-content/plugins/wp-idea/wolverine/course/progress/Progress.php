<?php
namespace bpmj\wpidea\wolverine\course\progress;

use bpmj\wpidea\settings\LMS_Settings;
use bpmj\wpidea\wolverine\course\progress\events\CheckPreviousLessonAsDone;
use bpmj\wpidea\wolverine\course\progress\events\CheckPreviousLessonAsUndone;
use bpmj\wpidea\wolverine\course\progress\events\HelperCheckPreviousLessons;
use bpmj\wpidea\wolverine\event\Events;
use bpmj\wpidea\infrastructure\system\IO;

class Progress
{
    const EVENT_USER_WENT_TO_THE_NEXT_LESSON = 'user_went_to_the_next_lesson';

    const EVENT_USER_CHECK_PREVIOUS_LESSON_AS_UNDONE = 'user_check_previous_lesson_as_undone';

    const AUTOCHECK_NAME = 'autocheck';

    public function __construct()
    {
        $this->registerEvents();
        $this->registerEventsHandlers();
    }

    protected function registerEvents()
    {
        add_action('wp', [$this, 'maybeTriggerEventUserWentToTheNexLesson']);
        add_action('wp_ajax_bpmj_eddcm_check_previous_lesson_as_undone', [$this, 'checkPreviousLessonAsUndone']);
    }

    public function maybeTriggerEventUserWentToTheNexLesson()
    {
        $lessonPageId = get_the_ID();

        if ($this->isCourseContent($lessonPageId)) {
            if (IO::is_param_in_session('bpmj_eddcm_previous_lesson_id')) {
                $previousLessonPageId = (int) IO::get_param_from_session('bpmj_eddcm_previous_lesson_id');

                $previousLesson = HelperCheckPreviousLessons::getPreviousLesson($lessonPageId);

                if (! is_null($previousLesson) && $previousLessonPageId === $previousLesson->ID) {
                    Events::trigger(self::EVENT_USER_WENT_TO_THE_NEXT_LESSON, [
                        'previous_lesson_id' => $previousLesson->ID,
                        'lesson_id' => $lessonPageId,
                    ]);
                }
            }

            IO::set_param_to_session('bpmj_eddcm_previous_lesson_id', $lessonPageId);
        } else {
            if ($_SERVER['REQUEST_URI'] === get_the_permalink($lessonPageId))
                IO::set_param_to_session('bpmj_eddcm_previous_lesson_id', null);
        }
    }

    public function checkPreviousLessonAsUndone()
    {
        if (! isset($_POST['lesson']))
            return;

        Events::trigger(self::EVENT_USER_CHECK_PREVIOUS_LESSON_AS_UNDONE, [
            'lesson_id' => sanitize_text_field($_POST['lesson']),
        ]);
    }

    protected function registerEventsHandlers()
    {
        Events::on(self::EVENT_USER_WENT_TO_THE_NEXT_LESSON, new CheckPreviousLessonAsDone());
        Events::on(self::EVENT_USER_CHECK_PREVIOUS_LESSON_AS_UNDONE, new CheckPreviousLessonAsUndone());
    }

    private function isCourseContent($lessonPageId)
    {
        $mode = get_post_meta($lessonPageId, 'mode', true);

        if ('lesson' === $mode || 'test' === $mode || 'full' === $mode)
            return true;

        return  false;
    }
}
