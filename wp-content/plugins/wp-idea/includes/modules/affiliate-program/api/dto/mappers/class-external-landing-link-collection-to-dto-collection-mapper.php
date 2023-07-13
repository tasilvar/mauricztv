<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\modules\affiliate_program\api\dto\mappers;

use bpmj\wpidea\modules\affiliate_program\core\entities\External_Landing_Link_Collection;
use bpmj\wpidea\modules\affiliate_program\api\dto\collections\External_Landing_Link_DTO_Collection;

class External_Landing_Link_Collection_To_DTO_Collection_Mapper
{
    private External_Landing_Link_To_DTO_Mapper $link_to_DTO_mapper;

    public function __construct(
        External_Landing_Link_To_DTO_Mapper $link_to_DTO_mapper
    )
    {
        $this->link_to_DTO_mapper = $link_to_DTO_mapper;
    }

    public function map(External_Landing_Link_Collection $collection): External_Landing_Link_DTO_Collection
    {
        $dto_collection = External_Landing_Link_DTO_Collection::create();

        foreach ($collection as $item) {
            $dto_collection->add(
                $this->link_to_DTO_mapper->map($item)
            );
        }

        return $dto_collection;
    }
}