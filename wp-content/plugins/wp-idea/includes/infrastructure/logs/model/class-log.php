<?php
/**
 * This file is licenses under proprietary license
 */

namespace bpmj\wpidea\infrastructure\logs\model;

use DateTimeImmutable;

class Log
{
    private $id;

    private $created_at;

    private $level;

    private $message;

    private $source;

    public function __construct(
        DateTimeImmutable $created_at,
        Log_Level $level,
        string $message,
        string $source = Log_Source::DEFAULT)
    {
        $this->created_at = $created_at;
        $this->level = $level;
        $this->message = $message;
        $this->source = $source;
    }

    public function get_id(): ?int
    {
        return $this->id;
    }

    public function set_id(int $id): void
    {
        $this->id = $id;
    }

    public function get_created_at(): DateTimeImmutable
    {
        return $this->created_at;
    }

    public function get_level(): Log_Level
    {
        return $this->level;
    }

    public function get_message(): string
    {
        return $this->message;
    }

    public function get_source(): ?string
    {
        return $this->source;
    }
}