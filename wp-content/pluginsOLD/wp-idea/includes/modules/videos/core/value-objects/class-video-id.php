<?php

namespace bpmj\wpidea\modules\videos\core\value_objects;

class Video_Id
{
    private string $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function __toString()
    {
        return $this->id;
    }

    public function get_id(): string
    {
        return $this->id;
    }
}