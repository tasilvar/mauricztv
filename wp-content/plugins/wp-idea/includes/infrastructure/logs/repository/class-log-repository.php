<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\infrastructure\logs\repository;

use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\infrastructure\logs\model\Log;
use bpmj\wpidea\infrastructure\logs\model\Log_Level;
use bpmj\wpidea\infrastructure\logs\model\Log_Source;
use bpmj\wpidea\infrastructure\logs\persistence\Interface_Logs_Persistence;
use bpmj\wpidea\infrastructure\logs\persistence\Log_Query_Criteria;

class Log_Repository implements Interface_Log_Repository
{
    /**
     * @var Interface_Logs_Persistence
     */
    private $logs_persistence;

    public function __construct(
        Interface_Logs_Persistence $logs_persistence
    ) {
        $this->logs_persistence = $logs_persistence;
    }

    public function find_all(int $per_page = 0, int $page = 1, ?Sort_By_Clause $sort_by = null): array
    {
        $results = $this->logs_persistence->find_all($per_page, $page, $sort_by);

        return $this->table_rows_to_models($results);
    }

    public function count_all(): int
    {
        return $this->logs_persistence->count_all();
    }

    public function count_by_criteria(Log_Query_Criteria $criteria): int
    {
        return $this->logs_persistence->count_by_criteria($criteria);
    }

    public function find_by_criteria(Log_Query_Criteria $criteria, int $per_page = 0, int $page = 1, ?Sort_By_Clause $sort_by = null): array
    {
        $results = $this->logs_persistence->find_by_criteria($criteria, $per_page, $page, $sort_by);

        return $this->table_rows_to_models($results);
    }

    public function save(Log $log): void
    {
        $this->logs_persistence->insert($log);
    }

    public function remove_oldest(int $number_of_logs_to_keep): void
    {
        $this->logs_persistence->delete_by_criteria(
            new Log_Query_Criteria(),
            $number_of_logs_to_keep,
            (new Sort_By_Clause())->sort_by('created_at', true)
        );
    }

    public function remove_by_criteria(Log_Query_Criteria $criteria): void
    {
        $this->logs_persistence->delete_by_criteria($criteria, 0);
    }

    public function remove(int $id): void
    {
        $this->logs_persistence->delete($id);
    }

    private function table_rows_to_models(array $rows): array
    {
        $final_array = [];

        foreach ($rows as $row) {
            $final_array[] = $this->table_row_to_model($row);
        }

        return $final_array;
    }

    private function table_row_to_model(array $row): Log
    {
        $log = new Log(
            new \DateTimeImmutable($row['created_at']),
            new Log_Level((int)$row['level']),
            $row['message'],
            $row['source'] ?? Log_Source::DEFAULT
        );

        $log->set_id((int)$row['id']);

        return $log;
    }
}