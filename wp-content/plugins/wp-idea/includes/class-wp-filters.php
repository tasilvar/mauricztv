<?php

namespace bpmj\wpidea;

use bpmj\wpidea\events\filters\Interface_Filters;

class WP_Filters implements Interface_Filters
{
    public function add(string $name, callable $callback, int $priority = 10, int $accepted_args = 1)
    {
        add_filter($name, $callback, $priority, $accepted_args);
    }

    public function apply(string $name, $value, ...$args)
    {
        return apply_filters($name, $value, ...$args);
    }

    public function remove(string $name, callable $callback, int $priority = 10)
    {
        remove_filter($name, $callback, $priority);
    }

}
