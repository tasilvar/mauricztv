<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\infrastructure\logs\persistence;

use bpmj\wpidea\infrastructure\database\Interface_Database;
use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\infrastructure\logs\model\Log;

class Logs_Persistence implements Interface_Logs_Persistence
{
    public const TABLE_NAME = 'wpi_logs';

    /**
     * @var Interface_Database
     */
    private $db;

    public function __construct(Interface_Database $db)
    {
        $this->db = $db;
    }

    public function insert(Log $log): void
    {
        $this->db->insert(self::TABLE_NAME, [
            'created_at' => $log->get_created_at()->format('Y-m-d H:i:s'),
            'level' => $log->get_level()->get_value(),
            'message' => $log->get_message(),
            'source' => $log->get_source(),
        ]);
    }

    public function find_all(int $per_page = 0, int $page = 1, ?Sort_By_Clause $sort_by = null): array
    {
        $is_paginated = $per_page > 0;
        $skip = !$is_paginated ? 0 : ($per_page * ($page - 1));

        return $this->db->get_results(self::TABLE_NAME, [
            'id',
            'created_at',
            'level',
            'message',
            'source',
        ], [], $per_page, $skip, $sort_by);
    }

    public function count_all(): int
    {
        return $this->db->count(self::TABLE_NAME);
    }

    public function count_by_criteria(Log_Query_Criteria $criteria): int
    {
        $where = $this->parse_criteria_to_where_clause($criteria);

        return $this->db->count(self::TABLE_NAME, $where);
    }

    public function find_by_criteria(Log_Query_Criteria $criteria, int $per_page = 0, int $page = 1, ?Sort_By_Clause $sort_by = null): array
    {
        $is_paginated = $per_page > 0;
        $skip = !$is_paginated ? 0 : ($per_page * ($page - 1));
        $where = $this->parse_criteria_to_where_clause($criteria);

        return $this->db->get_results(self::TABLE_NAME, [
            'id',
            'created_at',
            'level',
            'message',
            'source',
        ], $where, $per_page, $skip, $sort_by);
    }

    public function delete_by_criteria(Log_Query_Criteria $criteria, int $skip, ?Sort_By_Clause $sort_by = null): void
    {
        $where = $this->parse_criteria_to_where_clause($criteria);

        $this->db->delete_rows(self::TABLE_NAME, $where, 0, $skip, $sort_by);
    }

    public function delete(int $id): void
    {
        $where = $this->parse_criteria_to_where_clause(new Log_Query_Criteria(
            null, null, null, null, null, $id
        ));

        $this->db->delete_rows(self::TABLE_NAME, $where);
    }

    public function setup(): void
    {
        $this->db->create_table_if_not_exists(self::TABLE_NAME, [
            'id mediumint(9) NOT NULL AUTO_INCREMENT',
            'created_at datetime NOT NULL',
            'level mediumint(3) NOT NULL',
            'message text NOT NULL',
            'source tinytext',
        ], 'id');
    }

    private function parse_criteria_to_where_clause(Log_Query_Criteria $criteria): array
    {
        $where = [];

        if(isset($criteria->datetime_from)) {
            $where[] = ['created_at', '>=', $criteria->datetime_from->format('Y-m-d H:i:s')];
        }

        if(isset($criteria->datetime_to)) {
            $where[] = ['created_at', '<=', $criteria->datetime_to->format('Y-m-d H:i:s')];
        }

        if(isset($criteria->level)) {
            $where[] = ['level', '=', $criteria->level];
        }

        if(isset($criteria->source_like)) {
            $where[] = ['source', 'LIKE', $criteria->source_like];
        }

        if(isset($criteria->message_like)) {
            $where[] = ['message', 'LIKE', $criteria->message_like];
        }

        if(isset($criteria->id)) {
            $where[] = ['id', '=', $criteria->id];
        }

        return $where;
    }
}