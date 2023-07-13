<?php

declare(strict_types=1);

namespace bpmj\wpidea\modules\affiliate_program\core\repositories;

use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\modules\affiliate_program\core\entities\{External_Landing_Link, External_Landing_Link_Collection};
use bpmj\wpidea\modules\affiliate_program\core\value_objects\External_Link_ID;
use bpmj\wpidea\modules\affiliate_program\infrastructure\persistence\External_Landing_Link_Query_Criteria;

interface Interface_External_Landing_Link_Repository
{
    public function find_by_criteria(
        Interface_External_Landing_Link_Query_Criteria $criteria,
        int $per_page = 0,
        int $page = 1,
        ?Sort_By_Clause $sort_by = null
    ): External_Landing_Link_Collection;

    public function find_by_id(External_Link_ID $id): ?External_Landing_Link;

    public function add(External_Landing_Link $external_landing_link): void;

    public function update(External_Landing_Link $external_landing_link): void;

    public function delete(External_Link_ID $id): void;
}