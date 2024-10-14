<?php
declare(strict_types=1);

namespace bpmj\wpidea\modules\webhooks\infrastructure\persistence;

use bpmj\wpidea\infrastructure\database\Interface_Database;
use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\modules\webhooks\core\entities\Webhook;

class Webhooks_Persistence implements Interface_Webhooks_Persistence
{
    public const TABLE_NAME = 'wpi_webhooks';

    private Interface_Database $db;

    public function __construct(Interface_Database $db)
    {
        $this->db = $db;
    }

    public function insert(Webhook $webhook): void
    {
        $this->db->insert(self::TABLE_NAME, [
            'type_of_event' => $webhook->get_type_of_event()->get_value(),
            'url' => $webhook->get_url()->get_value(),
            'status' => $webhook->get_status()->get_value(),
        ]);
    }

    public function update(Webhook $webhook): void
    {
        $set = [
            ['type_of_event', $webhook->get_type_of_event()->get_value()],
            ['url', $webhook->get_url()->get_value()],
            ['status', $webhook->get_status()->get_value()]
        ];

        $where = $this->parse_criteria_to_where_clause(new Webhook_Query_Criteria(
            null, null, null,  $webhook->get_id()->to_int()
        ));

        $this->db->update_rows(self::TABLE_NAME, $set, $where);
    }

    public function count_by_criteria(Webhook_Query_Criteria $criteria): int
    {
        $where = $this->parse_criteria_to_where_clause($criteria);

        return $this->db->count(self::TABLE_NAME, $where);
    }

    public function find_by_id(int $id): array
    {
        $where = $this->parse_criteria_to_where_clause(new Webhook_Query_Criteria(
            null, null, null,  $id
        ));

        return $this->db->get_results(self::TABLE_NAME, [
            'id',
            'type_of_event',
            'url',
            'status',
        ], $where, 1, 0, null);
    }

    public function find_by_criteria(Webhook_Query_Criteria $criteria, int $per_page = 0, int $page = 1, ?Sort_By_Clause $sort_by = null): array
    {
        $is_paginated = $per_page > 0;
        $skip = !$is_paginated ? 0 : ($per_page * ($page - 1));
        $where = $this->parse_criteria_to_where_clause($criteria);

        return $this->db->get_results(self::TABLE_NAME, [
            'id',
            'type_of_event',
            'url',
            'status',
        ], $where, $per_page, $skip, $sort_by);
    }

    public function delete(int $id): void
    {
        $where = $this->parse_criteria_to_where_clause(new Webhook_Query_Criteria(
            null, null, null,  $id
        ));

        $this->db->delete_rows(self::TABLE_NAME, $where);
    }

    public function setup(): void
    {
        $this->db->create_table_if_not_exists(self::TABLE_NAME, [
            'id mediumint(9) NOT NULL AUTO_INCREMENT',
            'type_of_event VARCHAR(50) NOT NULL',
            'url varchar(255) NOT NULL',
            'status int(1) NOT NULL',
        ], 'id');
    }

    private function parse_criteria_to_where_clause(Webhook_Query_Criteria $criteria): array
    {
        $where = [];

        if(isset($criteria->type_of_event)) {
            $where[] = ['type_of_event', '=', $criteria->type_of_event];
        }

        if(isset($criteria->url_like)) {
            $where[] = ['url', 'LIKE', $criteria->url_like];
        }

        if(isset($criteria->status)) {
            $where[] = ['status', '=', $criteria->status];
        }

        if(isset($criteria->id)) {
            $where[] = ['id', '=', $criteria->id];
        }

        return $where;
    }
}