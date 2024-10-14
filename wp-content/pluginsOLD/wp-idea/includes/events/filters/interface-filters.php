<?php

namespace bpmj\wpidea\events\filters;

interface Interface_Filters
{
    public function add(string $name, callable $callback, int $priority = 10, int $accepted_args = 1);

    public function apply(string $name, $value, ...$args);

    public function remove(string $name, callable $callback, int $priority = 10);
}