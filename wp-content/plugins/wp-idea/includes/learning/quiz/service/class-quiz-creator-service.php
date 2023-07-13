<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\learning\quiz\service;

use bpmj\wpidea\learning\quiz\dto\Quiz_DTO;
use bpmj\wpidea\learning\quiz\model\Quiz;
use bpmj\wpidea\learning\quiz\model\Quiz_File;
use bpmj\wpidea\learning\quiz\model\Quiz_File_Collection;
use bpmj\wpidea\learning\quiz\Quiz_ID;
use bpmj\wpidea\learning\quiz\repository\Interface_Quiz_Settings_Repository;
use bpmj\wpidea\learning\quiz\value_object\Quiz_Answers_Preview_Settings;
use bpmj\wpidea\learning\quiz\value_object\Quiz_Attempts_Limit_Settings;
use bpmj\wpidea\learning\quiz\value_object\Quiz_Randomization_Settings;
use bpmj\wpidea\learning\quiz\value_object\Quiz_Time_Limit_Settings;

class Quiz_Creator_Service
{
    private Interface_Quiz_Settings_Repository $quiz_settings_repository;

    public function __construct(
        Interface_Quiz_Settings_Repository $quiz_settings_repository
    ) {
        $this->quiz_settings_repository = $quiz_settings_repository;
    }

    public function save_quiz(Quiz_DTO $dto): void
    {
        $model = $this->create_model($dto);

        $this->quiz_settings_repository->save($model);
    }

    private function create_model(Quiz_DTO $dto): Quiz
    {
        return new Quiz(
            new Quiz_ID($dto->id),
            $dto->name,
            $dto->description,
            $dto->slug,
            $dto->subtitle,
            $dto->level,
            $dto->duration,
			new Quiz_Time_Limit_Settings(
				$dto->time_mode,
			$dto->time ?? Quiz_Time_Limit_Settings::DEFAULT_TIME
			),
			new Quiz_Attempts_Limit_Settings(
				$dto->attempts_mode,
				$dto->attempts_number ?? Quiz_Attempts_Limit_Settings::DEFAULT_LIMIT
			),
            $dto->evaluated_by_admin_mode,
            new Quiz_Randomization_Settings(
				$dto->randomize_question_order,
	            $dto->randomize_answer_order
            ),
            $dto->featured_image,
            $this->create_files_collection($dto),
			$dto->questions,
	        $dto->points_to_pass,
	        $dto->points_max,
            new Quiz_Answers_Preview_Settings(
				$dto->can_see_answers_mode,
	            $dto->also_show_correct_answers
            )
        );
    }

    private function create_files_collection(Quiz_DTO $dto): ?Quiz_File_Collection
    {
        $collection = Quiz_File_Collection::create();

        foreach ($dto->files as $file) {
            $collection->add(Quiz_File::create(
                $file['id'] ?? null,
                $file['file_name'],
                $file['file_url']
            ));
        }

        return $collection;
    }
}