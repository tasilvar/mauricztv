<?php

declare(strict_types=1);

namespace bpmj\wpidea\modules\active_sessions_limiter\core\value_objects;

class Limiter_Settings
{
    public const DEFAULT_MAX_ACTIVE_SESSIONS = 3;
    public const MINIMUM_MAX_ACTIVE_SESSIONS = 1;

    private ?int $max_active_sessions;

    private function __construct(
        ?int $max_active_sessions
    )
    {
        $this->max_active_sessions = $max_active_sessions;
    }

    public static function create(
        ?int $max_active_sessions_number
    ): self
    {
        return new self($max_active_sessions_number);
    }

    public function get_max_active_sessions_number(): int
    {
        if(is_null($this->max_active_sessions)) {
            return self::DEFAULT_MAX_ACTIVE_SESSIONS;
        }

        if($this->max_active_sessions < self::MINIMUM_MAX_ACTIVE_SESSIONS) {
            return self::MINIMUM_MAX_ACTIVE_SESSIONS;
        }

        return $this->max_active_sessions;
    }
}