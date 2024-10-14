<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\courses\core\value_objects;

use Exception;

class Drip_Value
{
    private int $drip_value;

    public function __construct(int $drip_value)
    {
        if ($drip_value < 0) {
            throw new Exception('Invalid drip value provided!');
        }

        $this->drip_value = $drip_value;
    }

    public function to_int(): int
    {
        return $this->drip_value;
    }

    public function equals(Drip_Value $other_drip_value): bool
    {
        return $this->to_int() === $other_drip_value->to_int();
    }
}