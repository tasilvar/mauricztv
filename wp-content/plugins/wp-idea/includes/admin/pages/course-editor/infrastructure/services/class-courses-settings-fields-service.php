<?php

namespace bpmj\wpidea\admin\pages\course_editor\infrastructure\services;

use bpmj\wpidea\admin\Edit_Course;
use bpmj\wpidea\admin\pages\course_editor\core\events\Event_Name;
use bpmj\wpidea\admin\pages\course_editor\core\services\Checkboxes_Value_Changer;
use bpmj\wpidea\admin\settings\core\entities\fields\Abstract_Setting_Field;
use bpmj\wpidea\admin\settings\core\services\Interface_Settings_Fields_Service;
use bpmj\wpidea\app\courses\Courses_App_Service;
use bpmj\wpidea\courses\core\dto\Course_DTO;
use bpmj\wpidea\courses\core\dto\Course_To_Dto_Mapper;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\learning\course\Course_ID;
use bpmj\wpidea\sales\product\core\services\Product_Events;
use bpmj\wpidea\sales\product\dto\Product_DTO;
use bpmj\wpidea\sales\product\dto\Product_To_DTO_Mapper;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\sales\product\repository\Interface_Product_Repository;

class Courses_Settings_Fields_Service implements Interface_Settings_Fields_Service
{
    private Course_ID $id;
    private Product_To_DTO_Mapper $product_to_DTO_mapper;
    private Interface_Product_Repository $product_repository;
    private Checkboxes_Value_Changer $checkboxes_value_changer;
    private Courses_App_Service $app_service;
    private Course_To_Dto_Mapper $course_to_dto_mapper;
    private Edit_Course $edit_course;

    private ?Product_DTO $cached_product = null;
    private ?Course_DTO $cached_course = null;
    private Interface_Events $events;
    private Product_Events $product_events;

    public function __construct(
        Course_ID $edited_course_id,
        Product_To_DTO_Mapper $product_to_DTO_mapper,
        Interface_Product_Repository $product_repository,
        Checkboxes_Value_Changer $checkboxes_value_changer,
        Courses_App_Service $app_service,
        Course_To_Dto_Mapper $course_to_dto_mapper,
        Edit_Course $edit_course,
        Interface_Events $events,
        Product_Events $product_events
    ) {
        $this->id = $edited_course_id;
        $this->product_to_DTO_mapper = $product_to_DTO_mapper;
        $this->product_repository = $product_repository;
        $this->checkboxes_value_changer = $checkboxes_value_changer;
        $this->app_service = $app_service;
        $this->course_to_dto_mapper = $course_to_dto_mapper;
        $this->edit_course = $edit_course;
        $this->events = $events;
        $this->product_events = $product_events;
    }

    public function update_field(Abstract_Setting_Field $field): void
    {
        // Do wywalenia po usunięciu starej edycji kursów !!!
        remove_action('save_post', array($this->edit_course, 'save_courses_metabox'), 1, 2);

        $field_name = $field->get_name();
        $course_dto = $this->fetch_and_cache_course_dto();
        $old_value = '';

        if (!$course_dto) {
            return;
        }

        $product_dto = $this->fetch_and_cache_product_dto($course_dto);

        if (!$product_dto) {
            return;
        }

        $field_value = $this->checkboxes_value_changer->change_the_value($field_name, $field->get_value());

        if (property_exists($product_dto, $field_name)) {
            $old_value = $product_dto->$field_name;
            $product_dto->$field_name = $field_value;
        }

        if (property_exists($course_dto, $field_name)) {
            $old_value = $course_dto->$field_name;
            $course_dto->$field_name = $field_value;
        }

        $this->app_service->save_course($product_dto, $course_dto);

        $this->emit_field_updated_event($field);

        $this->product_events->emit_course_field_value_updated_event(
            $field,
            $old_value,
            $field->get_value(),
            $this->id->to_int()
        );

        $this->clear_cache();
    }

    private function fetch_and_cache_product_dto(Course_DTO $course_dto): ?Product_DTO
    {
        $product = $this->product_repository->find(new Product_ID($course_dto->product_id));

        if (!$product) {
            return null;
        }

        $this->cached_product = $this->product_to_DTO_mapper->map_product_to_dto($product);

        return $this->cached_product;
    }

    private function fetch_and_cache_course_dto(): ?Course_DTO
    {
        $course = $this->app_service->find_course($this->id);

        if (!$course) {
            return null;
        }

        $this->cached_course = $this->course_to_dto_mapper->map_course_to_dto($course);

        return $this->cached_course;
    }

    private function clear_cache(): void
    {
        $this->cached_product = null;
        $this->cached_course = null;
    }

    public function get_field_value(Abstract_Setting_Field $field)
    {
        $field_name = $field->get_name();
        $course_dto = $this->get_course_from_cache() ?? $this->fetch_and_cache_course_dto();

        if (!$course_dto) {
            return null;
        }

        $product_dto = $this->get_product_from_cache() ?? $this->fetch_and_cache_product_dto($course_dto);

        if (!$product_dto) {
            return null;
        }

        $field_value = $product_dto->$field_name ?? $course_dto->$field_name ?? null;

        return $this->checkboxes_value_changer->change_the_value($field_name, $field_value);
    }

    private function get_product_from_cache(): ?Product_DTO
    {
        return $this->cached_product;
    }

    private function get_course_from_cache(): ?Course_DTO
    {
        return $this->cached_course;
    }

    private function emit_field_updated_event(Abstract_Setting_Field $field): void
    {
        $this->events->emit(Event_Name::FIELD_UPDATED, $field, $this->id);
    }
}