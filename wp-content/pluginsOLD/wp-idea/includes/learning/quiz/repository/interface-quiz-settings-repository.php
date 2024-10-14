<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\learning\quiz\repository;

use bpmj\wpidea\learning\quiz\model\Quiz;
use bpmj\wpidea\learning\quiz\Quiz_ID;

interface Interface_Quiz_Settings_Repository
{
    public function find_by_id(Quiz_ID $id_quiz): ?Quiz;

    public function save(Quiz $quiz): void;

    public function get_questions_by_quiz_id(Quiz_ID $quiz_id): array;

    public function is_randomize_question_order_enabled(Quiz_ID $id): bool;

    public function is_randomize_answer_order_enabled(Quiz_ID $id): bool;

    public function is_can_see_answers_enabled(Quiz_ID $id): bool;

    public function is_also_show_correct_answers_enabled(Quiz_ID $id): bool;
}