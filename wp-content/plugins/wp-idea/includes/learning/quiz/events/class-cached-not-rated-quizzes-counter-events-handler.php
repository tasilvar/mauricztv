<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\learning\quiz\events;

use bpmj\wpidea\events\Event_Name;
use bpmj\wpidea\events\Interface_Event_Handler;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\learning\quiz\Cached_Not_Rated_Quizzes_Counter;

class Cached_Not_Rated_Quizzes_Counter_Events_Handler implements Interface_Event_Handler
{
    private Interface_Events $events;
    private Cached_Not_Rated_Quizzes_Counter $not_rated_quizzes_counter;

    public function __construct(
        Interface_Events $events,
        Cached_Not_Rated_Quizzes_Counter $not_rated_quizzes_counter
    )
    {
        $this->events = $events;
        $this->not_rated_quizzes_counter = $not_rated_quizzes_counter;
    }

    public function init(): void
    {
        $this->events->on(Event_Name::QUIZ_FINISHED, [$this, 'clear_counter_cache']);
        $this->events->on(Event_Name::RESOLVED_QUIZ_RESULT_UPDATED, [$this, 'clear_counter_cache']);
    }

    public function clear_counter_cache(): void
    {
        $this->not_rated_quizzes_counter->clear_cache();
    }
}
