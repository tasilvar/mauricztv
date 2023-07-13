<?php

declare(strict_types=1);

namespace bpmj\wpidea\courses\core\value_objects;

use Exception;

class Drip_Unit
{
    public const MINUTES = 'minutes';
    public const HOURS = 'hours';
    public const DAYS = 'days';
    public const MONTHS = 'months';
    public const YEARS = 'years';

    public const VALID_UNIT = [
        self::MINUTES,
        self::HOURS,
        self::DAYS,
        self::MONTHS,
        self::YEARS
    ];

    private string $drip_unit;

    public function __construct(string $drip_unit)
    {
        if (!in_array($drip_unit, self::VALID_UNIT, true)) {
            throw new Exception('Invalid unit provided!');
        }

        $this->drip_unit = $drip_unit;
    }

    public function equals(Drip_Unit $other_drip_unit): bool
    {
        return $this->get_value() === $other_drip_unit->get_value();
    }

    public function get_value(): string
    {
        return $this->drip_unit;
    }

}