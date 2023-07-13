<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\modules\search_engine\core\clients\sales\dto;

class Product_DTO_Collection extends \ArrayObject
{
    public function offsetSet($index, $newval)
    {
        if (!($newval instanceof Product_DTO)) {
            throw new \InvalidArgumentException('Item must be an instance of the Product_DTO class');
        }

        parent::offsetSet($index, $newval);
    }
}