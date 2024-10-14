<?php

namespace bpmj\wpidea\modules\opinions\core\value_objects;

use Exception;

class Opinion_Status
{
    public const WAITING = 'waiting';
    public const ACCEPTED = 'accepted';
    public const DISCARDED = 'discarded';

    public const ALL_STATUSES = [
        self::WAITING,
        self::ACCEPTED,
        self::DISCARDED,
    ];

    private string $value;

    public function __construct(string $value)
    {
        if (!in_array($value, self::ALL_STATUSES, true)) {
            throw new Exception('Invalid opinion status provided');
        }

        $this->value = $value;
    }

    public function get_value(): string
    {
        return $this->value;
    }
}