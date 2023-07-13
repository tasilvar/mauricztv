<?php

namespace bpmj\wpidea\modules\opinions\core\repositories;

use bpmj\wpidea\data_types\ID;
use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\modules\opinions\core\collections\Opinion_Collection;
use bpmj\wpidea\modules\opinions\core\entities\Opinion;
use bpmj\wpidea\modules\opinions\infrastructure\persistence\Opinions_Query_Criteria;

interface Interface_Opinion_Repository
{
    public function find_by_criteria(Opinions_Query_Criteria $criteria, int $per_page = 0, int $page = 1, ?Sort_By_Clause $sort_by = null): Opinion_Collection;

	public function count_by_criteria(Opinions_Query_Criteria $criteria): int;

    public function update(Opinion $opinion): void;

    public function find_by_id(ID $id): ?Opinion;

    public function create(Opinion $opinion): void;
}