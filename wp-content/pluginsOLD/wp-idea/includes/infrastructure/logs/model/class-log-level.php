<?php
/**
 * This file is licenses under proprietary license
 */

namespace bpmj\wpidea\infrastructure\logs\model;

class Log_Level
{
    public const LEVEL_DEBUG = 100;
    public const LEVEL_INFO = 200;
    public const LEVEL_NOTICE = 250;
    public const LEVEL_WARNING = 300;
    public const LEVEL_ERROR = 400;
    public const LEVEL_CRITICAL = 500;
    public const LEVEL_ALERT = 550;
    public const LEVEL_EMERGENCY = 600;

    public const VALID_LEVELS = [
        self::LEVEL_DEBUG,
        self::LEVEL_INFO,
        self::LEVEL_NOTICE,
        self::LEVEL_WARNING,
        self::LEVEL_ERROR,
        self::LEVEL_CRITICAL,
        self::LEVEL_ALERT,
        self::LEVEL_EMERGENCY,
    ];

    private $level;

    public function __construct(
        int $level
    )
    {
        if(!in_array($level, self::VALID_LEVELS, true)) {
            throw new \Exception('Invalid log level provided!');
        }

        $this->level = $level;
    }

    public function equals(Log_Level $other_level): bool
    {
        return $this->get_value() === $other_level->get_value();
    }

    public function get_value(): int
    {
        return $this->level;
    }
}
