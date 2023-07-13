<?php

namespace bpmj\wpidea\modules\opinions\infrastructure\persistence;

use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\modules\opinions\core\entities\Opinion;

interface Interface_Opinions_Persistence
{
    public function setup(): void;

    public function find_by_criteria(
        Opinions_Query_Criteria $criteria,
        int $per_page = 0,
        int $page = 1,
        ?Sort_By_Clause $sort_by = null
    ): array;


	public function count_by_criteria(Opinions_Query_Criteria $criteria): int;

    public function update(Opinion $opinion): void;

    public function insert(Opinion $opinion): void;
}