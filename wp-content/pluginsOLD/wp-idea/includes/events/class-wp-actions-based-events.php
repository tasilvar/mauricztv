<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\events;

use bpmj\wpidea\events\actions\Interface_Actions;

class WP_Actions_Based_Events implements Interface_Events
{
    private $actions;

    public function __construct(
        Interface_Actions $actions
    )
    {
        $this->actions = $actions;
    }

    public function on(string $event_name, callable $callback, int $priority = 10, int $accepted_args = 1)
    {
        $this->actions->add($event_name, $callback, $priority, $accepted_args);
    }

    public function emit(string $event_name, ...$args): void
    {
        $this->actions->do($event_name, ...$args);
    }
}