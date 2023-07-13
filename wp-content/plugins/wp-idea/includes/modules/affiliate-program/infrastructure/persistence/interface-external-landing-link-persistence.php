<?php
declare(strict_types=1);

namespace bpmj\wpidea\modules\affiliate_program\infrastructure\persistence;

use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\modules\affiliate_program\core\entities\External_Landing_Link;
use bpmj\wpidea\modules\affiliate_program\core\value_objects\External_Link_ID;
use bpmj\wpidea\modules\affiliate_program\infrastructure\persistence\External_Landing_Link_Query_Criteria;


interface Interface_External_Landing_Link_Persistence
{
    public function insert(External_Landing_Link $external_landing_link): void;

    public function update(External_Landing_Link $external_landing_link): void;

    public function find_by_id(External_Link_ID $id): array;

    public function find_by_criteria(External_Landing_Link_Query_Criteria $criteria, int $per_page = 0, int $page = 1, ?Sort_By_Clause $sort_by = null): array;

    public function delete(External_Link_ID $id): void;

    public function setup(): void;
}