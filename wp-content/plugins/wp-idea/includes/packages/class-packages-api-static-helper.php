<?php
/**
 * This file is licenses under proprietary license
 */

namespace bpmj\wpidea\packages;

class Packages_API_Static_Helper
{
    private static Interface_Packages_API $api;

    public static function init(Interface_Packages_API $api): void
    {
        self::$api = $api;
    }

    public static function has_access_to_feature(string $feature): bool
    {
        return self::$api->has_access_to_feature($feature);
    }

    public static function render_no_access_to_feature_info(string $feature, ?string $custom_message = null, bool $short = false): string
    {
        return self::$api->render_no_access_to_feature_info($feature, $custom_message, $short);
    }
}