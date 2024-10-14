<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\modules\affiliate_program\api\dto\mappers;

use bpmj\wpidea\modules\affiliate_program\core\entities\External_Landing_Link;
use bpmj\wpidea\modules\affiliate_program\api\dto\External_Landing_Link_DTO;

class External_Landing_Link_To_DTO_Mapper
{
    public function map(External_Landing_Link $landing_link): External_Landing_Link_DTO
    {
        return External_Landing_Link_DTO::create(
            $landing_link->get_id()->to_int(),
            $landing_link->get_product_id()->to_int(),
            $landing_link->get_url()->get_value()
        );
    }
}