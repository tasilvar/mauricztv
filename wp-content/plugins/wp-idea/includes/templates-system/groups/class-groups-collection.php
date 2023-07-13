<?php

namespace bpmj\wpidea\templates_system\groups;

use ArrayObject;
use InvalidArgumentException;

class Groups_Collection extends ArrayObject
{
    public function offsetSet($index, $newval)
    {
        if (!($newval instanceof Template_Group)) {
            throw new InvalidArgumentException("Element must be an instance of the Template_Group class");
        }

        parent::offsetSet($index, $newval);
    }
}