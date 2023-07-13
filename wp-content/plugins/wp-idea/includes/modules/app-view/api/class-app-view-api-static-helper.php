<?php

namespace bpmj\wpidea\modules\app_view\api;

class App_View_API_Static_Helper
{
    private static App_View_API $app_view_api;

    public static function init(App_View_API $app_view_api): void
    {
        self::$app_view_api = $app_view_api;
    }

    public static function is_active(): bool
    {
        return self::$app_view_api->is_active();
    }

    public static function render_top_bar_navigation(): string
    {
        return self::$app_view_api->render_top_bar_navigation();
    }

    public static function render_lesson_header(): void
    {
        self::$app_view_api->render_lesson_header();
    }

    public static function render_quiz_header(): void
    {
        self::$app_view_api->render_quiz_header();
    }
}