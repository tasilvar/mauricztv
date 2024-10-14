<?php
/**
 * This file is licenses under proprietary license
 */

namespace bpmj\wpidea\infrastructure\system;

class IO
{

    public static function get_param_from_session(string $name): string
    {
        return $_SESSION[$name];
    }

    public static function set_param_to_session(string $name, string $value): void
    {
        $_SESSION[$name] = $value;
    }

    public static function is_param_in_session(string $name): bool
    {
        return isset($_SESSION[$name]);
    }
}
