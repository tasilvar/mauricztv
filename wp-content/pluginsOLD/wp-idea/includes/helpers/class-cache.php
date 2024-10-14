<?php

namespace bpmj\wpidea\helpers;

class Cache
{
    public const EXPIRATION_TIME_NEVER = 0;
    public const EXPIRATION_TIME_1_MINUTE = 60;
    public const EXPIRATION_TIME_1_HOUR = self::EXPIRATION_TIME_1_MINUTE * 60;
	public const EXPIRATION_TIME_24_HOURS = self::EXPIRATION_TIME_1_HOUR * 24;

	public static function set(string $name, $value, $expires = self::EXPIRATION_TIME_NEVER): void
    {
        set_transient($name, $value, $expires);
    }

    public static function get(string $name)
    {
        return get_transient($name);
    }

    public static function unset(string $name)
    {
        delete_transient($name);
    }
}
