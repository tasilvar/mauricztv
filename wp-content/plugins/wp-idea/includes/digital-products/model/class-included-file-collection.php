<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\digital_products\model;

class Included_File_Collection extends \ArrayObject
{
    public function offsetSet($index, $newval)
    {
        if (!($newval instanceof Included_File)) {
            throw new \InvalidArgumentException('Item must be an instance of the Included_File class');
        }

        parent::offsetSet($index, $newval);
    }
}