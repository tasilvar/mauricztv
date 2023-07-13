<?php

declare(strict_types=1);

namespace bpmj\wpidea\modules\conflicting_plugins_detector\api;

class Conflicting_Plugins_API_Static_Helper
{
    private static Conflicting_Plugins_API $conflicting_plugins_API;

    public static function init(Conflicting_Plugins_API $conflicting_plugins_API): void
    {
        self::$conflicting_plugins_API = $conflicting_plugins_API;
    }

    public static function get_active_conflicting_plugins_names(): array
    {
        return self::$conflicting_plugins_API->get_active_conflicting_plugins_names();
    }
}