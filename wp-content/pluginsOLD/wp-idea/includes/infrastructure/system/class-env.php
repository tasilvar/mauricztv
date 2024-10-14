<?php
/**
 * This file is licenses under proprietary license
 */

namespace bpmj\wpidea\infrastructure\system;

class Env
{

    public static function is_defined(string $env): bool
    {
        return defined($env);
    }
    
    public static function get_value(string $env)
    {
        return self::is_defined($env) ? constant($env) : null;
    }
}
