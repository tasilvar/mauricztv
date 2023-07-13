<?php

declare(strict_types=1);

namespace bpmj\wpidea\modules\affiliate_program\infrastructure\persistence;

use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\modules\affiliate_program\core\entities\Commission;

interface Interface_Commission_Persistence
{
    public function insert(Commission $commission): void;

    public function setup(): void;

    public function count_by_criteria(Commission_Query_Criteria $criteria): int;

    public function find_by_id(int $id): array;

    public function find_by_criteria(
        Commission_Query_Criteria $criteria,
        int $per_page = 0,
        int $page = 1,
        ?Sort_By_Clause $sort_by = null
    ): array;

    public function sum_sale_amount_by_criteria(
        Commission_Query_Criteria $criteria,
        int $per_page = 0,
        int $page = 1,
        ?Sort_By_Clause $sort_by = null
    ): int;

    public function sum_commission_amount_by_criteria(
        Commission_Query_Criteria $criteria,
        int $per_page = 0,
        int $page = 1,
        ?Sort_By_Clause $sort_by = null
    ): int;

    public function delete(int $id): void;

    public function update(Commission $commission): void;

    public function get_summary(
        int $per_page = 0,
        int $page = 1,
        ?Sort_By_Clause $sort_by = null,
        ?array $filters = null
    ): array;
}