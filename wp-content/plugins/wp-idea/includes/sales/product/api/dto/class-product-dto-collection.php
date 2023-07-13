<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\sales\product\api\dto;

use bpmj\wpidea\data_types\collection\Abstract_Iterator;

class Product_DTO_Collection extends Abstract_Iterator
{
    public function current(): Product_DTO
    {
        return $this->get_current_item();
    }

    public function add(Product_DTO $item): self
    {
        return $this->add_item($item);
    }
}