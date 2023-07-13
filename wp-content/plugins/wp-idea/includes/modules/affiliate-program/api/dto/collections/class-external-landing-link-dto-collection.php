<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\modules\affiliate_program\api\dto\collections;

use bpmj\wpidea\data_types\collection\Abstract_Iterator;
use bpmj\wpidea\modules\affiliate_program\api\dto\External_Landing_Link_DTO;

class External_Landing_Link_DTO_Collection extends Abstract_Iterator
{
    public function add(External_Landing_Link_DTO $item): self
    {
        return $this->add_item($item);
    }

    public function current(): External_Landing_Link_DTO
    {
        return $this->get_current_item();
    }
}