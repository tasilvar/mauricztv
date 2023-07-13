<?php

declare(strict_types=1);

namespace bpmj\wpidea\modules\app_view\core\services;

use bpmj\wpidea\events\actions\Action_Name;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\view\Interface_View_Provider;

class Course_Header_Renderer
{
    private const HEADER_TEMPLATE_FILE_PATH = '/templates/scarlet/course/lesson-and-quiz/app-view-lesson-and-quiz-header';

    private Interface_View_Provider $view_provider;
    private Interface_Actions $actions;

    public function __construct(
        Interface_View_Provider $view_provider,
        Interface_Actions $actions
    ) {
        $this->view_provider = $view_provider;
        $this->actions = $actions;
    }

    public function get_lesson_header_html(): void
    {
        $this->actions->add(Action_Name::AFTER_BODY_OPEN_TAG, function () {
            echo $this->view_provider->get(self::HEADER_TEMPLATE_FILE_PATH);
        });
    }

    public function get_quiz_header_html(): void
    {
        $this->actions->add(Action_Name::AFTER_BODY_OPEN_TAG, function () {
            echo $this->view_provider->get(self::HEADER_TEMPLATE_FILE_PATH);
        });
    }
}