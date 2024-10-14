<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\learning\quiz\dto;

use bpmj\wpidea\learning\quiz\model\Quiz;
use bpmj\wpidea\learning\quiz\model\Quiz_File;
use bpmj\wpidea\learning\quiz\model\Quiz_File_Collection;

class Quiz_To_Dto_Mapper
{
    public function map_quiz_to_dto(Quiz $quiz): Quiz_DTO
    {
        $dto = new Quiz_DTO();
        $dto->id = $quiz->get_id()->to_int();
        $dto->name = $quiz->get_name();
        $dto->description = $quiz->get_description();
        $dto->slug = $quiz->get_slug();
        $dto->subtitle = $quiz->get_subtitle();
        $dto->level = $quiz->get_level();
        $dto->duration = $quiz->get_duration();
        $dto->time_mode = $quiz->get_time_limit_settings()->is_enabled();
        $dto->time = $quiz->get_time_limit_settings()->get_time();
		$dto->attempts_mode = $quiz->get_attempts_limit_settings()->is_enabled();
		$dto->attempts_number = $quiz->get_attempts_limit_settings()->get_limit();
        $dto->evaluated_by_admin_mode = $quiz->get_evaluated_by_admin();
        $dto->randomize_question_order = $quiz->get_randomization_settings()->randomize_questions_order();
        $dto->randomize_answer_order = $quiz->get_randomization_settings()->randomize_answers_order();
        $dto->featured_image = $quiz->get_featured_image();
        $dto->files = $this->get_files_array_from_collection($quiz->get_files());
		$dto->questions = $quiz->get_questions();
		$dto->points_to_pass = $quiz->get_points_to_pass();
		$dto->points_max = $quiz->get_points_max();
		$dto->can_see_answers_mode = $quiz->get_answers_preview_settings()->allow_user_to_see_his_answers_for_completed_quiz();
		$dto->also_show_correct_answers = $quiz->get_answers_preview_settings()->reveal_correct_answers();

        return $dto;
    }

    private function get_files_array_from_collection(
        Quiz_File_Collection $collection
    ): array
    {
        $array = [];

        foreach ($collection as $file) {
            /* @var Quiz_File $file */
            $array[] = [
                'file_name' => $file->get_name(),
                'file_url' => $file->get_url()
            ];
        }

        return $array;
    }
}