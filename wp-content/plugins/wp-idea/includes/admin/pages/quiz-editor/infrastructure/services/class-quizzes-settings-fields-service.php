<?php

namespace bpmj\wpidea\admin\pages\quiz_editor\infrastructure\services;

use bpmj\wpidea\admin\settings\core\entities\fields\Abstract_Setting_Field;
use bpmj\wpidea\admin\settings\core\services\Interface_Settings_Fields_Service;
use bpmj\wpidea\app\quizzes\Quizzes_App_Service;
use bpmj\wpidea\learning\quiz\dto\Quiz_DTO;
use bpmj\wpidea\learning\quiz\dto\Quiz_To_Dto_Mapper;
use bpmj\wpidea\learning\quiz\Quiz_ID;

class Quizzes_Settings_Fields_Service implements Interface_Settings_Fields_Service
{
    private Quiz_ID $id;
    private Quizzes_App_Service $app_service;
    private Quiz_To_Dto_Mapper $quiz_to_dto_mapper;
    private ?Quiz_DTO $cached_quiz = null;
	private Checkboxes_Value_Changer $checkboxes_value_changer;

	public function __construct(
        Quiz_ID $edited_quiz_id,
        Quizzes_App_Service $app_service,
        Quiz_To_Dto_Mapper $quiz_to_dto_mapper,
	    Checkboxes_Value_Changer $checkboxes_value_changer
    ) {
        $this->id = $edited_quiz_id;
        $this->app_service = $app_service;
        $this->quiz_to_dto_mapper = $quiz_to_dto_mapper;
		$this->checkboxes_value_changer = $checkboxes_value_changer;
	}

    public function update_field(Abstract_Setting_Field $field): void
    {
        $field_name = $field->get_name();
        $quiz_dto = $this->fetch_and_cache_quiz_dto();

        if (!$quiz_dto) {
            return;
        }

	    $field_value = $this->checkboxes_value_changer->change_the_value($field_name, $field->get_value());

        if (property_exists($quiz_dto, $field_name)) {
            $quiz_dto->$field_name = $field_value;
        }

        $this->app_service->save_quiz($quiz_dto);

        $this->clear_cache();
    }

    private function clear_cache(): void
    {
        $this->cached_quiz = null;
    }

    public function get_field_value(Abstract_Setting_Field $field)
    {
        $field_name = $field->get_name();
        $quiz_dto = $this->get_quiz_from_cache() ?? $this->fetch_and_cache_quiz_dto();

        if (!$quiz_dto) {
            return null;
        }

	    $field_value = $quiz_dto->$field_name ?? null;

	    return $this->checkboxes_value_changer->change_the_value($field_name, $field_value);
    }

    private function get_quiz_from_cache(): ?Quiz_DTO
    {
        return $this->cached_quiz;
    }

    private function fetch_and_cache_quiz_dto(): ?Quiz_DTO
    {
        $quiz = $this->app_service->find_quiz($this->id);

        if (!$quiz) {
            return null;
        }

        $this->cached_quiz = $this->quiz_to_dto_mapper->map_quiz_to_dto($quiz);

        return $this->cached_quiz;
    }
}