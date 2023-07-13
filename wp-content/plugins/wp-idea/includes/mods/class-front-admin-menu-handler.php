<?php

namespace bpmj\wpidea\mods;

use bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug;
use bpmj\wpidea\admin\pages\course_editor\Course_Editor_Page_Renderer;
use bpmj\wpidea\admin\pages\quiz_editor\Quiz_Editor_Page_Renderer;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\data_types\ID;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\instantiator\Interface_Initiable;
use bpmj\wpidea\learning\course\Interface_Readable_Course_Repository;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\view\Interface_Admin_Bar;

class Front_Admin_Menu_Handler implements Interface_Initiable
{
    private Interface_Actions $actions;
    private Interface_Readable_Course_Repository $course_repository;
    private Current_Request $current_request;
    private Interface_Url_Generator $url_generator;
    private Interface_Translator $translator;
    private Interface_Admin_Bar $admin_bar;

    public function __construct(
        Interface_Actions $actions,
        Interface_Readable_Course_Repository $course_repository,
        Current_Request $current_request,
        Interface_Url_Generator $url_generator,
        Interface_Translator $translator,
        Interface_Admin_Bar $admin_bar
    ) {
        $this->actions = $actions;
        $this->course_repository = $course_repository;
        $this->current_request = $current_request;
        $this->url_generator = $url_generator;
        $this->translator = $translator;
        $this->admin_bar = $admin_bar;
    }

    public function init(): void
    {
        $this->actions->add('wp_before_admin_bar_render', [$this, 'handle_menu']);
    }

    public function handle_menu(): void
    {
        $page_id = $this->current_request->get_current_page_id();
        if (!$page_id) {
            return;
        }

        $edit_link = $this->admin_bar->get_menu_position_by_id('edit');
        if (!$edit_link) {
            return;
        }

        if ($this->course_repository->is_course_panel_page($page_id)) {
            $edit_link->title = $this->translator->translate('courses.edit_course');
            $edit_link->href = $this->get_course_editor_page_url($page_id);
            $this->admin_bar->set_menu_position($edit_link);
            return;
        }

        if ($this->course_repository->is_course_lesson_page($page_id)) {
            $edit_link->title = $this->translator->translate('courses.edit_lesson');
            $this->admin_bar->set_menu_position($edit_link);
            return;
        }

        if ($this->course_repository->is_course_module_page($page_id)) {
            $edit_link->title = $this->translator->translate('courses.edit_module');
            $this->admin_bar->set_menu_position($edit_link);
        }

        if ($this->course_repository->is_course_test_page($page_id)) {
            $edit_link->title = $this->translator->translate('courses.edit_test');
	        $edit_link->href = $this->get_quiz_editor_page_url($page_id);
            $this->admin_bar->set_menu_position($edit_link);
        }
    }

    private function get_course_editor_page_url(int $page_id): string
    {
        $course = $this->course_repository->find_by_page_id(new ID($page_id));
        return $this->url_generator->generate_admin_page_url('admin.php', [
            'page' => Admin_Menu_Item_Slug::EDITOR_COURSE,
            Course_Editor_Page_Renderer::COURSE_ID_QUERY_ARG_NAME => $course->get_id()->to_int()
        ]);
    }

    private function get_quiz_editor_page_url(int $page_id): string
    {
        return $this->url_generator->generate_admin_page_url('admin.php', [
            'page' => Admin_Menu_Item_Slug::EDITOR_QUIZ,
            Quiz_Editor_Page_Renderer::QUIZ_ID_QUERY_ARG_NAME => $page_id
        ]);
    }
}