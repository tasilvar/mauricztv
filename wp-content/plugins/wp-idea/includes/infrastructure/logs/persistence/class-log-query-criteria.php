<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\infrastructure\logs\persistence;

use DateTimeImmutable;

class Log_Query_Criteria
{
    public $datetime_from;

    public $datetime_to;

    public $level;

    public $source_like;

    public $message_like;

    public $id;

    public function __construct(
        ?DateTimeImmutable $datetime_from = null,
        ?DateTimeImmutable $datetime_to = null,
        ?int $level = null,
        ?string $source_like = null,
        ?string $message_like = null,
        ?int $id = null
    ) {
        $this->datetime_from = $datetime_from;
        $this->datetime_to = $datetime_to;
        $this->level = $level;
        $this->source_like = $source_like;
        $this->message_like = $message_like;
        $this->id = $id;
    }
}