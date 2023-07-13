<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\digital_products\dto;

class Included_File_DTO_Collection extends \ArrayObject
{
    public function offsetSet($index, $newval)
    {
        if (!($newval instanceof Included_File_DTO)) {
            throw new \InvalidArgumentException('Item must be an instance of the Included_File_DTO class');
        }

        parent::offsetSet($index, $newval);
    }
}