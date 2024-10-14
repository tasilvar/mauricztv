<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\quizzes;

use bpmj\wpidea\learning\quiz\Resolved_Quiz;

class Quiz_Table_Data_Parser_For_Export extends Quiz_Table_Data_Parser
{
    public function parse_models_to_plain_array(array $quizzes): array
    {
        $data = [];

        foreach ($quizzes as $quiz) {
            /** @var Resolved_Quiz $quiz */
            $data[] = [
                'id' => $quiz->get_id(),
                'title' => $quiz->get_title(),
                'date' => $quiz->get_completed_at()->format('Y-m-d H:i:s'),
                'points' => $quiz->get_points() . '/' . $quiz->get_points_total(),
                'result' => $quiz->get_result(),
                'result_label' => $this->get_result_label($quiz->get_result()),
                'course' => $quiz->get_course_title(),
                'user_full_name' => $quiz->get_user_full_name() ?? '',
                'user_email' => $quiz->get_user_email()
            ];
        }

        return $data;
    }
}