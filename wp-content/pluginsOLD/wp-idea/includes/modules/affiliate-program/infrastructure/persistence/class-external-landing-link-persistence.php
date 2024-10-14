<?php

declare(strict_types=1);

namespace bpmj\wpidea\modules\affiliate_program\infrastructure\persistence;

use bpmj\wpidea\infrastructure\database\Interface_Database;
use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\modules\affiliate_program\core\entities\External_Landing_Link;
use bpmj\wpidea\modules\affiliate_program\core\value_objects\External_Link_ID;

class External_Landing_Link_Persistence implements Interface_External_Landing_Link_Persistence
{
    public const TABLE_NAME = 'wpi_external_landing_links';

    private Interface_Database $db;

    public function __construct(Interface_Database $db)
    {
        $this->db = $db;
    }

    public function insert(External_Landing_Link $external_landing_link): void
    {
        $this->db->insert(self::TABLE_NAME, [
            'product_id' => $external_landing_link->get_product_id()->to_int(),
            'url' => $external_landing_link->get_url()->get_value(),
        ]);
    }

    public function update(External_Landing_Link $external_landing_link): void
    {
        $set = [
            ['product_id', $external_landing_link->get_product_id()->to_int()],
            ['url', $external_landing_link->get_url()->get_value()]
        ];

        $where = $this->parse_criteria_to_where_clause(
            new External_Landing_Link_Query_Criteria($external_landing_link->get_id()->to_int())
        );

        $this->db->update_rows(self::TABLE_NAME, $set, $where);
    }

    public function find_by_id(External_Link_ID $id): array
    {
        $where = $this->parse_criteria_to_where_clause(
            new External_Landing_Link_Query_Criteria($id->to_int())
        );

        return $this->db->get_results(self::TABLE_NAME, [
            'id',
            'product_id',
            'url'
        ], $where, 1);
    }

    public function find_by_criteria(External_Landing_Link_Query_Criteria $criteria, int $per_page = 0, int $page = 1, ?Sort_By_Clause $sort_by = null): array
    {
        $is_paginated = $per_page > 0;
        $skip = !$is_paginated ? 0 : ($per_page * ($page - 1));
        $where = $this->parse_criteria_to_where_clause($criteria);

        return $this->db->get_results(self::TABLE_NAME, [
            'id',
            'product_id',
            'url'
        ], $where, $per_page, $skip, $sort_by);
    }

    public function delete(External_Link_ID $id): void
    {
        $where = $this->parse_criteria_to_where_clause(
            new External_Landing_Link_Query_Criteria($id->to_int())
        );

        $this->db->delete_rows(self::TABLE_NAME, $where);
    }

    public function setup(): void
    {
        $this->db->create_table_if_not_exists(self::TABLE_NAME, [
            'id bigint(20) NOT NULL AUTO_INCREMENT',
            'product_id bigint(20) NOT NULL',
            'url varchar(255) NOT NULL',
        ], 'id');
    }

    private function parse_criteria_to_where_clause(External_Landing_Link_Query_Criteria $criteria): array
    {
        $where = [];

        if ($criteria->get_id()) {
            $where[] = ['id', '=', $criteria->get_id()];
        }

        if ($criteria->get_product_ids()) {
            $where[] = ['product_id', 'IN', $criteria->get_product_ids()];
        }

        if ($criteria->get_url()) {
            $where[] = ['url', 'LIKE', $criteria->get_url()];
        }

        return $where;
    }
}