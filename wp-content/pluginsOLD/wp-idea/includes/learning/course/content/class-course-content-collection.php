<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\learning\course\content;

class Course_Content_Collection extends \ArrayObject
{
    public function offsetSet($index, $newval)
    {
        if (!($newval instanceof Course_Content)) {
            throw new \InvalidArgumentException('Item must be an instance of the Course_Content class');
        }

        parent::offsetSet($index, $newval);
    }
}