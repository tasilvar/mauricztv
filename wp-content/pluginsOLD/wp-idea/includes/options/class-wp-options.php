<?php

namespace bpmj\wpidea\options;

class WP_Options implements Interface_Options
{
    public function get(string $option_name)
    {
        return get_option($option_name, null);
    }

    public function set(string $option_name, $value): bool
    {
        return update_option($option_name, $value);
    }

    public function delete(string $option_name): bool
    {
        return delete_option($option_name);
    }
}
