<?php

namespace bpmj\wpidea\modules\opinions\core\value_objects;

class Opinion_Content
{
    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function get_value(): string
    {
        return $this->value;
    }
}