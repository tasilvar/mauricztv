<?php
/**
 * This file is licenses under proprietary license
 */

namespace bpmj\wpidea\infrastructure\system;

class Flag
{

    private const ENABLED = true;

    public static function is_defined(string $flag): bool
    {
        return defined($flag);
    }

    public static function is_enabled(string $flag): bool
    {
        return self::is_defined($flag) && self::ENABLED === constant($flag);
    }
}
