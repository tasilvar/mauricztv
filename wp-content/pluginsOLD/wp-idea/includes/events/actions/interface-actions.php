<?php

namespace bpmj\wpidea\events\actions;

interface Interface_Actions
{
    public function add(string $name, callable $callback, int $priority = 10, int $accepted_args = 1);

    public function do(string $name, ...$args);

    public function remove(string $name, callable $callback, int $priority = 10);
}
