<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\physical_product\dto;

use bpmj\wpidea\data_types\collection\Abstract_Iterator;

class Physical_Product_DTO_Collection extends Abstract_Iterator
{
    public function current(): Physical_Product_DTO
    {
        return $this->get_current_item();
    }

    public function add(Physical_Product_DTO $item): self
    {
        return $this->add_item($item);
    }
}
