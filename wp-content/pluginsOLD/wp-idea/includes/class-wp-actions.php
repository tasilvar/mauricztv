<?php

namespace bpmj\wpidea;

use bpmj\wpidea\events\actions\Interface_Actions;

class WP_Actions implements Interface_Actions
{
    public function add(string $name, callable $callback, int $priority = 10, int $accepted_args = 1)
    {
        add_action($name, $callback, $priority, $accepted_args);
    }

    public function do(string $name, ...$args)
    {
        do_action($name, ...$args);
    }

    public function remove(string $name, callable $callback, int $priority = 10)
    {
        remove_action($name, $callback, $priority);
    }
}