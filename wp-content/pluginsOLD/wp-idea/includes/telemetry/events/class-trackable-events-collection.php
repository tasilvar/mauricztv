<?php

namespace bpmj\wpidea\telemetry\events;

class Trackable_Events_Collection extends \ArrayObject
{
    public function offsetSet($index, $newval)
    {
        if (!($newval instanceof Trackable_Event)) {
            throw new \InvalidArgumentException("New element must be an instance of the Trackable_Event class");
        }

        parent::offsetSet($index, $newval);
    }
}