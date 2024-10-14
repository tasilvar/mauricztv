<?php

namespace bpmj\wpidea\helpers;

use bpmj\wpidea\shared\infrastructure\cache\In_Memory_Cache;

class In_Memory_Cache_Static_Helper
{
    private static In_Memory_Cache $cache;
    private const DEFAULT_EMPTY_VALUE = 'LXJenRs8LA';

    public static function init(): void
    {
        self::$cache = new In_Memory_Cache();
    }

    public static function set(string $key, $value): void
    {
        self::$cache->set($key, $value);
    }

    public static function get(string $key)
    {
        return self::$cache->get($key);
    }

    public static function get_with_default(string $key)
    {
        return self::$cache->get($key, self::DEFAULT_EMPTY_VALUE);
    }

    public static function delete(string $key): void
    {
        self::$cache->delete($key);
    }

    public static function get_or_set_if_not_exists(string $key, callable $get_value)
    {
        $cached = self::get_with_default($key);
        if($cached !== self::DEFAULT_EMPTY_VALUE) {
            return $cached;
        }

        $value = call_user_func($get_value);
        self::set($key, $value);

        return $value;
    }
}