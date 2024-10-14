<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\data_types;

class String_VO
{
    private string $value;

    public function __construct(
        string $value
    ) {
        $this->value = $value;
    }

    public function get_value(): string
    {
        return $this->value;
    }

    public function equals(String_VO $other_vo): bool
    {
        return $this->get_value() === $other_vo->get_value();
    }
}