<?php

namespace bpmj\wpidea\data_types\course;

use Exception;

class Course_Sales_Status
{
    public const ENABLED = 'on';
    public const DISABLED = 'off';

    public const STATUS_ENABLED_NAME = 'enabled';
    public const STATUS_DISABLED_NAME = 'disabled';


    public const VALID_STATUS = [
        self::ENABLED,
        self::DISABLED
    ];

    private string $status;

    public function __construct(string $status)
    {
        if (!in_array($status, self::VALID_STATUS, true)) {
            throw new Exception('Invalid course sales status provided!');
        }

        $this->status = $status;
    }

    public function equals(Course_Sales_Status $other_status): bool
    {
        return $this->get_value() === $other_status->get_value();
    }

    public function get_value(): string
    {
        return $this->status;
    }

    public function get_name(): string
    {
        return ($this->status === self::ENABLED) ? self::STATUS_ENABLED_NAME : self::STATUS_DISABLED_NAME;
    }
}