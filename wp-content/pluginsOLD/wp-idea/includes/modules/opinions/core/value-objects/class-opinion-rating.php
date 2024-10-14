<?php

namespace bpmj\wpidea\modules\opinions\core\value_objects;

use Exception;

class Opinion_Rating
{
    public const MIN = 1;
    public const MAX = 5;

    private int $value;

    public function __construct(int $value)
    {
        $min = self::MIN;
        $max = self::MAX;

        if ($value < $min || $value > $max) {
            throw new Exception("Opinion rating must be in range {$min} to {$max}");
        }

        $this->value = $value;
    }

    public function get_value(): int
    {
        return $this->value;
    }
}