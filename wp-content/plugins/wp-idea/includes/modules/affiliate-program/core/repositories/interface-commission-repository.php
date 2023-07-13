<?php

namespace bpmj\wpidea\modules\affiliate_program\core\repositories;

use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\modules\affiliate_program\core\entities\Commission;
use bpmj\wpidea\modules\affiliate_program\core\entities\Commission_Collection;
use bpmj\wpidea\modules\affiliate_program\infrastructure\persistence\Commission_Query_Criteria;

interface Interface_Commission_Repository
{
    public function create(Commission $commission): void;

    public function count_by_criteria(Commission_Query_Criteria $criteria): int;

    public function find_by_id(int $id): ?Commission;

    public function find_by_criteria(
        Commission_Query_Criteria $criteria,
        int $per_page = 0,
        int $page = 1,
        ?Sort_By_Clause $sort_by = null
    ): Commission_Collection;

    public function update(Commission $commission): void;

    public function delete(int $id): void;
}
