<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\events;

interface Interface_Events extends Interface_Event_Emitter
{
    public function on(string $event_name, callable $callback, int $priority = 10, int $accepted_args = 1);
}