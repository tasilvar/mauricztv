<?php

namespace bpmj\wpidea\learning\quiz\api;

use bpmj\wpidea\learning\quiz\Quiz_ID;
use bpmj\wpidea\learning\quiz\Interface_Resolved_Quiz_Repository;
use bpmj\wpidea\learning\quiz\repository\Interface_Quiz_Settings_Repository;
use bpmj\wpidea\learning\quiz\Resolved_Quiz_Query_Criteria;
use bpmj\wpidea\learning\quiz\service\Quiz_Question_Configuration_Service;

class Quiz_Api implements Interface_Quiz_Api
{
    private Quiz_Question_Configuration_Service $quiz_question_configuration_service;
    private Interface_Resolved_Quiz_Repository $resolved_quiz_repository;
    private Interface_Quiz_Settings_Repository $quiz_settings_repository;

    public function __construct(
        Quiz_Question_Configuration_Service $quiz_question_configuration_service,
        Interface_Resolved_Quiz_Repository $resolved_quiz_repository,
        Interface_Quiz_Settings_Repository $quiz_settings_repository
    ) {
        $this->quiz_question_configuration_service = $quiz_question_configuration_service;
        $this->resolved_quiz_repository            = $resolved_quiz_repository;
        $this->quiz_settings_repository            = $quiz_settings_repository;
    }

	public function get_user_solving_quiz_attempts_count(string $user_email, int $quiz_id): int
	{
		$criteria = new Resolved_Quiz_Query_Criteria(
			null, null, null, $user_email, null, null, null, $quiz_id
		);

		return $this->resolved_quiz_repository->count_by_criteria($criteria);
	}

	public function get_user_quiz_allowed_attempts_number( int $quiz_id ): ?int
	{
		if(get_post_meta( $quiz_id, 'number_test_attempts_mode', true ) !== 'on'){
			return null;
		}

		$number_test_attempts = get_post_meta( $quiz_id, 'number_test_attempts', true );

		return !empty($number_test_attempts) ? (int)$number_test_attempts : null;
	}

	public function get_questions_for_single_test(int $quiz_id): array
    {
        return $this->quiz_question_configuration_service->prepare_new_question_configuration(new Quiz_ID($quiz_id));
    }

	public function save_configuration_of_questions(int $quiz_id, array $configuration_of_questions): void
    {
        $this->quiz_question_configuration_service->save(new Quiz_ID($quiz_id), $configuration_of_questions);
    }

	public function get_configuration_of_questions(int $quiz_id): array
    {
        return $this->quiz_question_configuration_service->get(new Quiz_ID($quiz_id));
    }

    public function is_can_see_answers_enabled(int $quiz_id): bool
    {
       return $this->quiz_settings_repository->is_can_see_answers_enabled(new Quiz_ID($quiz_id));
    }

    public function is_also_show_correct_answers_enabled(int $quiz_id): bool
    {
        return $this->quiz_settings_repository->is_also_show_correct_answers_enabled(new Quiz_ID($quiz_id));
    }

    public function save_time_is_up(int $quiz_id): void
    {
        $this->resolved_quiz_repository->save_time_is_up(new Quiz_ID($quiz_id));
    }

    public function get_time_is_up(int $quiz_id): bool
    {
        return $this->resolved_quiz_repository->get_time_is_up(new Quiz_ID($quiz_id));
    }
}