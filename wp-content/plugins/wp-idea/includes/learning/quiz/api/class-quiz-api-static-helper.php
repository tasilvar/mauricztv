<?php

namespace bpmj\wpidea\learning\quiz\api;

class Quiz_Api_Static_Helper
{
    private static Interface_Quiz_Api $api;

    public static function init(Interface_Quiz_Api $api): void
    {
        self::$api = $api;
    }

	public static function get_user_solving_quiz_attempts_count(string $user_email, int $quiz_id): int
	{
		return self::$api->get_user_solving_quiz_attempts_count($user_email, $quiz_id);
	}

	public static function get_user_quiz_allowed_attempts_number(int $quiz_id): ?int
	{
		return self::$api->get_user_quiz_allowed_attempts_number($quiz_id);
	}

    public static function get_questions_for_single_test(int $quiz_id): array
    {
        return self::$api->get_questions_for_single_test($quiz_id);
    }

    public static function get_configuration_of_questions(int $quiz_id): array
    {
        return self::$api->get_configuration_of_questions($quiz_id);
    }

    public static function is_can_see_answers_enabled(int $quiz_id): bool
    {
        return self::$api->is_can_see_answers_enabled($quiz_id);
    }

    public static function is_also_show_correct_answers_enabled(int $quiz_id): bool
    {
        return self::$api->is_also_show_correct_answers_enabled($quiz_id);
    }

    public static function get_time_is_up(int $quiz_id): bool
    {
        return self::$api->get_time_is_up($quiz_id);
    }
}