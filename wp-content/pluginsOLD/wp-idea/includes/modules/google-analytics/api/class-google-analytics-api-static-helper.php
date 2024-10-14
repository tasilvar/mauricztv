<?php

namespace bpmj\wpidea\modules\google_analytics\api;

class Google_Analytics_API_Static_Helper
{
    private static Google_Analytics_API $google_analytics_api;

    public static function init(Google_Analytics_API $google_analytics_api): void
    {
        self::$google_analytics_api = $google_analytics_api;
    }

    public static function is_ga4_enabled(): bool
    {
        return self::$google_analytics_api->is_ga4_enabled();
    }
}