<?php

namespace bpmj\wpidea\learning\quiz\service;

use bpmj\wpidea\learning\quiz\Interface_Resolved_Quiz_Repository;
use bpmj\wpidea\learning\quiz\repository\Interface_Quiz_Settings_Repository;
use bpmj\wpidea\learning\quiz\Quiz_ID;

class Quiz_Question_Configuration_Service
{
    private Interface_Quiz_Settings_Repository $quiz_settings_repository;
    private Quiz_Randomizer $quiz_randomizer;
    private Interface_Resolved_Quiz_Repository $resolved_quiz_repository;

    public function __construct(
        Interface_Resolved_Quiz_Repository $resolved_quiz_repository,
        Quiz_Randomizer $quiz_randomizer,
        Interface_Quiz_Settings_Repository $quiz_repository
    )
    {
        $this->resolved_quiz_repository = $resolved_quiz_repository;
        $this->quiz_settings_repository = $quiz_repository;
        $this->quiz_randomizer = $quiz_randomizer;
    }

    public function prepare_new_question_configuration(Quiz_ID $quiz_id): array
    {
        $quiz_questions = $this->quiz_settings_repository->get_questions_by_quiz_id($quiz_id);

        if ($this->quiz_settings_repository->is_randomize_question_order_enabled($quiz_id)) {
            $quiz_questions = $this->quiz_randomizer->randomize_questions($quiz_questions);
        }

        if ($this->quiz_settings_repository->is_randomize_answer_order_enabled($quiz_id)) {
            $quiz_questions = $this->quiz_randomizer->randomize_question_answers($quiz_questions);
        }

        return $quiz_questions;
    }

    public function save(Quiz_ID $quiz_id, array $configuration_of_questions): void
    {
        $this->resolved_quiz_repository->save_configuration_of_questions($quiz_id, $configuration_of_questions);
    }

    public function get(Quiz_ID $quiz_id): array
    {
        return $this->resolved_quiz_repository->get_configuration_of_questions($quiz_id);
    }
}