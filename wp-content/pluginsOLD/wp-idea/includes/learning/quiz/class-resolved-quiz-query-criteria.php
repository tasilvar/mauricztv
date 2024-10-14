<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\learning\quiz;

use DateTimeImmutable;

class Resolved_Quiz_Query_Criteria
{
    public $course;
    public $title;
    public $user_full_name;
    public $user_email;
    public $result;
    public $datetime_from;
    public $datetime_to;
    public ?int $quiz_id;

    public function __construct(
        ?string $course = null,
        ?string $title = null,
        ?string $user_full_name = null,
        ?string $user_email = null,
        ?string $result = null,
        ?DateTimeImmutable $datetime_from = null,
        ?DateTimeImmutable $datetime_to = null,
        ?int $quiz_id = null
    )
    {
        $this->course = $course;
        $this->title = $title;
        $this->user_full_name = $user_full_name;
        $this->user_email = $user_email;
        $this->result = $result;
        $this->datetime_from = $datetime_from;
        $this->datetime_to = $datetime_to;
        $this->quiz_id = $quiz_id;
    }
}