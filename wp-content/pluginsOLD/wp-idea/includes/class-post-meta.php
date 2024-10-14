<?php
namespace bpmj\wpidea;

class Post_Meta
{
    /**
     * Non-static wrapper for ::get_meta method
     */
    public function get_meta($post_id, $name)
    {
        return self::get($post_id, $name);
    }

    public static function set($post_id, $name, $value)
    {
        return update_post_meta($post_id, $name, $value);
    }

    public static function get($post_id, $name)
    {
        return get_post_meta($post_id, $name, true);
    }
}