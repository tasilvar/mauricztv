<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\course_editor\core\filters\handlers;

use bpmj\wpidea\admin\pages\course_editor\core\services\Course_Editor_Page_Checker;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\events\filters\Interface_Filters;
use bpmj\wpidea\instantiator\Interface_Initiable;

class Course_Editor_Add_Body_Classes_Handler implements Interface_Initiable
{
    private const ADMIN_BODY_CLASS = 'admin_body_class';
    private const POST_ARG_NAME = 'post';
    private const ACTION_ARG_NAME = 'action';
    private const EDIT_DESCRIPTION_ARG_NAME = 'edit_description';
    private const ADMIN_BODY_CLASSES = 'course-editor';

    private Course_Editor_Page_Checker $page_checker;
    private Interface_Filters $filters;
    private Current_Request $current_request;


    public function __construct(
        Course_Editor_Page_Checker $page_checker,
        Interface_Filters $filters,
        Current_Request $current_request
    ) {
        $this->page_checker = $page_checker;
        $this->filters = $filters;
        $this->current_request = $current_request;
    }

    public function init(): void
    {
        if (!$this->is_a_course_edit_page()) {
            return;
        }

        $this->filters->add(self::ADMIN_BODY_CLASS, [$this, 'add_body_classes']);
    }

    public function add_body_classes(string $classes): string
    {
        $classes .= ' ' . self::ADMIN_BODY_CLASSES;

        return $classes;
    }

    private function is_a_course_edit_page(): bool
    {
        $post_id = $this->current_request->get_query_arg(self::POST_ARG_NAME);
        $post_id = is_numeric($post_id) ? (int)$post_id : null;
        $action = $this->current_request->get_query_arg(self::ACTION_ARG_NAME);
        $edit_description = $this->current_request->get_query_arg(self::EDIT_DESCRIPTION_ARG_NAME);
        $edit_description = $edit_description === '1' ?? false;
        $post_type = get_post_type($post_id);

        return $this->page_checker->is_course_edit_page(
            $post_id,
            $post_type ?: null,
            $action,
            $edit_description
        );
    }
}