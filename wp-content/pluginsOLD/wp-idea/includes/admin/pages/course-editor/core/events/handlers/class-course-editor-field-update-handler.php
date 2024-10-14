<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\course_editor\core\events\handlers;

use bpmj\wpidea\admin\pages\course_editor\core\configuration\General_Course_Group;
use bpmj\wpidea\admin\pages\course_editor\core\events\Event_Name;
use bpmj\wpidea\admin\settings\core\entities\fields\Abstract_Setting_Field;
use bpmj\wpidea\app\courses\Courses_App_Service;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\instantiator\Interface_Initiable;
use bpmj\wpidea\learning\course\Course_ID;

class Course_Editor_Field_Update_Handler implements Interface_Initiable
{
    private Courses_App_Service $courses_app_service;
    private Interface_Events $events;

    private const FIELD_UPDATED_FOR_VARIABLE_PRICES = [
        General_Course_Group::VARIABLE_PRICING,
        General_Course_Group::PURCHASE_LIMIT,
        General_Course_Group::PURCHASE_LIMIT_ITEMS_LEFT
    ];

    private const FIELD_UPDATED = [
        General_Course_Group::REDIRECT_PAGE,
        General_Course_Group::REDIRECT_URL,
        General_Course_Group::ACCESS_START
    ];

    public function __construct(
        Courses_App_Service $courses_app_service,
        Interface_Events $events
    ) {
        $this->courses_app_service = $courses_app_service;
        $this->events = $events;
    }

    public function init(): void
    {
        $this->events->on(Event_Name::FIELD_UPDATED, [$this, 'field_update'], 10, 2);
    }

    public function field_update(Abstract_Setting_Field $field, Course_ID $course_id): void
    {
        if (!$course_id) {
            return;
        }

        if ($field->get_name() === General_Course_Group::VARIABLE_PRICING) {
            $this->courses_app_service->disable_sale($course_id);
        }

        if (in_array($field->get_name(), self::FIELD_UPDATED_FOR_VARIABLE_PRICES, true)) {
            $this->courses_app_service->rebuild_course_structure($course_id);
        }

        if (in_array($field->get_name(), self::FIELD_UPDATED, true)) {
            $this->courses_app_service->update_course_pages($course_id);
        }
    }
}