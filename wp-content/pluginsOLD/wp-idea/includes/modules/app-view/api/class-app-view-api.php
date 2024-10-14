<?php

namespace bpmj\wpidea\modules\app_view\api;

use bpmj\wpidea\modules\app_view\core\services\{App_View_Checker, Course_Header_Renderer, Top_Bar_Navigation_Renderer};

class App_View_API
{
    private App_View_Checker $app_view_checker;
    private Top_Bar_Navigation_Renderer $top_bar_navigation_renderer;
    private Course_Header_Renderer $course_header_renderer;

    public function __construct(
        App_View_Checker $app_view_checker,
        Top_Bar_Navigation_Renderer $top_bar_navigation_renderer,
        Course_Header_Renderer $course_header_renderer
    ) {
        $this->app_view_checker = $app_view_checker;
        $this->top_bar_navigation_renderer = $top_bar_navigation_renderer;
        $this->course_header_renderer = $course_header_renderer;
    }

    public function is_active(): bool
    {
        return $this->app_view_checker->is_active();
    }

    public function render_top_bar_navigation(): string
    {
        if(!$this->is_active()) {
            return '';
        }

        return $this->top_bar_navigation_renderer->get_top_bar_navigation_html();
    }

    public function render_lesson_header(): void
    {
        if(!$this->is_active()) {
            return;
        }

        $this->course_header_renderer->get_lesson_header_html();
    }

    public function render_quiz_header(): void
    {
        if(!$this->is_active()) {
            return;
        }

        $this->course_header_renderer->get_quiz_header_html();
    }
}