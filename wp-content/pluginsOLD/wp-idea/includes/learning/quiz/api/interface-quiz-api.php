<?php

namespace bpmj\wpidea\learning\quiz\api;

interface Interface_Quiz_Api
{
    public function get_user_solving_quiz_attempts_count(string $user_email, int $quiz_id): int;

    public function get_user_quiz_allowed_attempts_number(int $quiz_id): ?int;

    public function get_questions_for_single_test(int $quiz_id): array;

    public function save_configuration_of_questions(int $quiz_id, array $configuration_of_questions): void;

    public function get_configuration_of_questions(int $quiz_id): array;

    public function is_can_see_answers_enabled(int $quiz_id): bool;

    public function is_also_show_correct_answers_enabled(int $quiz_id): bool;

    public function save_time_is_up(int $quiz_id): void;

    public function get_time_is_up(int $quiz_id): bool;
}