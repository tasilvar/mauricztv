<?php

namespace bpmj\wpidea\modules\affiliate_program\core\value_objects;

class Affiliate_ID
{
    private string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function as_string(): string
    {
        return $this->id;
    }
}