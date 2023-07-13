<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\app\quizzes;

use bpmj\wpidea\learning\quiz\dto\Quiz_DTO;
use bpmj\wpidea\learning\quiz\model\Quiz;
use bpmj\wpidea\learning\quiz\Quiz_ID;
use bpmj\wpidea\learning\quiz\repository\Interface_Quiz_Settings_Repository;
use bpmj\wpidea\learning\quiz\service\Quiz_Creator_Service;

class Quizzes_App_Service
{
    private Interface_Quiz_Settings_Repository $quiz_settings_repository;
    private Quiz_Creator_Service $quiz_creator_service;

    public function __construct(
        Interface_Quiz_Settings_Repository $quiz_settings_repository,
        Quiz_Creator_Service $quiz_creator_service
    ) {
        $this->quiz_settings_repository = $quiz_settings_repository;
        $this->quiz_creator_service = $quiz_creator_service;
    }

    public function save_quiz(Quiz_DTO $quiz_dto): void
    {
        $this->quiz_creator_service->save_quiz($quiz_dto);
    }

    public function find_quiz(Quiz_ID $id_quiz): ?Quiz
    {
        return $this->quiz_settings_repository->find_by_id($id_quiz);
    }
}