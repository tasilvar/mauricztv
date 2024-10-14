<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\infrastructure\logs\repository;

use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\infrastructure\logs\model\Log;
use bpmj\wpidea\infrastructure\logs\persistence\Log_Query_Criteria;

interface Interface_Log_Repository
{
    public function find_all(int $per_page = 0, int $page = 1, ?Sort_By_Clause $sort_by = null): array;

    public function count_all(): int;

    public function count_by_criteria(Log_Query_Criteria $criteria): int;

    public function find_by_criteria(Log_Query_Criteria $criteria, int $per_page = 0, int $page = 1, ?Sort_By_Clause $sort_by = null): array;

    public function save(Log $log): void;

    public function remove_oldest(int $number_of_logs_to_keep): void;

    public function remove_by_criteria(Log_Query_Criteria $criteria): void;

    public function remove(int $id): void;
}