<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\infrastructure\logs\persistence;

use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\infrastructure\logs\model\Log;

interface Interface_Logs_Persistence
{
    public function insert(Log $log): void;

    public function find_all(int $per_page = 0, int $page = 1, ?Sort_By_Clause $sort_by = null): array;

    public function count_all(): int;

    public function count_by_criteria(Log_Query_Criteria $criteria): int;

    public function find_by_criteria(Log_Query_Criteria $criteria, int $per_page = 0, int $page = 1, ?Sort_By_Clause $sort_by = null): array;

    public function delete_by_criteria(Log_Query_Criteria $criteria, int $skip, ?Sort_By_Clause $sort_by = null): void;

    public function delete(int $id): void;

    public function setup(): void;
}