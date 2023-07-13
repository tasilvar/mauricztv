<?php

namespace bpmj\wpidea\wolverine\course\progress\events;

class HelperCheckPreviousLessons
{
    public static function getPreviousLesson($lessonPageId)
    {
        $coursePanelPageId = WPI()->courses->get_course_top_page($lessonPageId);

        $previousLesson = WPI()->courses->get_previous_sibling_of($coursePanelPageId, $lessonPageId);
        if (null === $previousLesson || 'lesson' !== $previousLesson->get_page_type())
            return null;

        return $previousLesson;
    }
}
