<?php

declare(strict_types=1);

namespace bpmj\wpidea\modules\app_view\core\services;

use bpmj\wpidea\app\courses\Courses_App_Service;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\data_types\Url;
use bpmj\wpidea\helpers\Text_Formatter;
use bpmj\wpidea\learning\course\content\Course_Content_ID;
use bpmj\wpidea\learning\course\content\Interface_Readable_Course_Content_Repository;
use bpmj\wpidea\learning\services\Interface_Content_Parent_Getter;
use bpmj\wpidea\learning\services\Interface_Url_Resolver;
use bpmj\wpidea\modules\app_view\core\providers\Interface_App_Info_Provider;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\view\Interface_View_Provider;

class Top_Bar_Navigation_Renderer
{
    private const NAVIGATION_BAR_TITLE_MAX_LENGTH = 15;

    private Interface_Url_Resolver $url_resolver;
    private Current_Request $current_request;
    private Courses_App_Service $courses_app_service;
    private Interface_View_Provider $view_provider;
    private Interface_Translator $translator;
    private Text_Formatter $text_formatter;
    private Interface_App_Info_Provider $app_info_provider;
    private Interface_Content_Parent_Getter $course_content_parent_getter;
    private Interface_Readable_Course_Content_Repository $course_content_repository;

    public function __construct(
        Interface_Url_Resolver          $course_content_url_resolver,
        Current_Request                 $current_request,
        Courses_App_Service             $courses_app_service,
        Interface_View_Provider         $view_provider,
        Interface_Translator            $translator,
        Text_Formatter                  $text_formatter,
        Interface_App_Info_Provider     $app_info_provider,
        Interface_Content_Parent_Getter $course_content_parent_getter,
        Interface_Readable_Course_Content_Repository $course_content_repository
    ) {
        $this->url_resolver = $course_content_url_resolver;
        $this->current_request = $current_request;
        $this->courses_app_service = $courses_app_service;
        $this->view_provider = $view_provider;
        $this->translator = $translator;
        $this->text_formatter = $text_formatter;
        $this->app_info_provider = $app_info_provider;
        $this->course_content_parent_getter = $course_content_parent_getter;
        $this->course_content_repository = $course_content_repository;
    }

    public function get_top_bar_navigation_html(): string
    {
        return $this->view_provider->get('/app-view/top-bar-navigation', [
            'text' => $this->get_text_to_display(),
            'link' => $this->get_link_to_display(),
        ]);
    }

    private function get_text_to_display(): string
    {
        $parent_id = $this->get_parent_id();

        if (!$parent_id) {
            return $this->app_info_provider->get_app_name();
        }

        $prefix = $this->translator->translate('app_view.go_to') . ': ';

        if ($this->courses_app_service->is_course_panel_page($parent_id->to_int())) {
            return $prefix . $this->translator->translate('app_view.course_panel');
        }

        $parent_course_content = $this->course_content_repository->find_by_id($parent_id);

        if (!$parent_course_content) {
            return $prefix . $this->app_info_provider->get_app_name();
        }

        $title = $parent_course_content->get_title();

        return $prefix . $this->text_formatter->shorten_long_text($title, self::NAVIGATION_BAR_TITLE_MAX_LENGTH);
    }

    private function get_link_to_display(): string
    {
        $url = $this->get_parent_url_link();

        return $url ? $url->get_value() : '';
    }

    private function get_parent_url_link(): ?Url
    {
        $parent_id = $this->get_parent_id();

        if(!$parent_id) {
            return null;
        }

        return $this->url_resolver->get_by_course_content_id(
            $parent_id
        );
    }

    private function get_parent_id(): ?Course_Content_ID
    {
        return $this->course_content_parent_getter->get_parent_content_id_by_course_content_id(
            new Course_Content_ID(
                $this->current_request->get_current_page_id()
            )
        );
    }
}