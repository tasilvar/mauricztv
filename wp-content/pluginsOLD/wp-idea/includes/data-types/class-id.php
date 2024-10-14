<?php


namespace bpmj\wpidea\data_types;

use OutOfBoundsException;

class ID
{
    protected int $id;

    public function __construct(int $id)
    {
        if ($id <= 0) {
            throw new OutOfBoundsException();
        }

        $this->id = $id;
    }

    public function to_int(): int
    {
        return $this->id;
    }

    public function equals(ID $other_id): bool
    {
        return $this->to_int() === $other_id->to_int();
    }
}