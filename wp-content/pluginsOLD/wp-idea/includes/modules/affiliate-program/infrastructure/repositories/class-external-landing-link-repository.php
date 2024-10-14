<?php

declare(strict_types=1);

namespace bpmj\wpidea\modules\affiliate_program\infrastructure\repositories;

use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\modules\affiliate_program\core\entities\{External_Landing_Link, External_Landing_Link_Collection};
use bpmj\wpidea\modules\affiliate_program\core\repositories\Interface_External_Landing_Link_Repository;
use bpmj\wpidea\modules\affiliate_program\core\value_objects\{External_Link_ID, External_Url, Product_ID};
use bpmj\wpidea\modules\affiliate_program\infrastructure\persistence\External_Landing_Link_Query_Criteria;
use bpmj\wpidea\modules\affiliate_program\infrastructure\persistence\Interface_External_Landing_Link_Persistence;
use bpmj\wpidea\modules\affiliate_program\core\repositories\Interface_External_Landing_Link_Query_Criteria;

class External_Landing_Link_Repository implements Interface_External_Landing_Link_Repository
{
    private Interface_External_Landing_Link_Persistence $external_landing_link_persistence;

    public function __construct(Interface_External_Landing_Link_Persistence $external_landing_link_persistence)
    {
        $this->external_landing_link_persistence = $external_landing_link_persistence;
    }

    public function find_by_id(External_Link_ID $id): ?External_Landing_Link
    {
        $external_landing_link_rows = $this->external_landing_link_persistence->find_by_id($id);

        if (empty($external_landing_link_rows)) {
            return null;
        }

        return $this->table_row_to_external_landing_link_model($external_landing_link_rows[0]);
    }

    public function find_by_criteria(
        Interface_External_Landing_Link_Query_Criteria $criteria,
        int $per_page = 0,
        int $page = 1,
        ?Sort_By_Clause $sort_by = null
    ): External_Landing_Link_Collection {
        $results = $this->external_landing_link_persistence->find_by_criteria($criteria, $per_page, $page, $sort_by);

        return $this->table_rows_to_external_landing_links_model($results);
    }

    public function add(External_Landing_Link $external_landing_link): void
    {
        $this->external_landing_link_persistence->insert($external_landing_link);
    }

    public function update(External_Landing_Link $external_landing_link): void
    {
        $this->external_landing_link_persistence->update($external_landing_link);
    }

    public function delete(External_Link_ID $id): void
    {
        $this->external_landing_link_persistence->delete($id);
    }

    private function table_rows_to_external_landing_links_model(array $rows): External_Landing_Link_Collection
    {
        $external_landing_links = External_Landing_Link_Collection::create();
        foreach ($rows as $row) {
            $external_landing_links->add($this->table_row_to_external_landing_link_model($row));
        }

        return $external_landing_links;
    }

    private function table_row_to_external_landing_link_model(array $row): External_Landing_Link
    {
        $id = $row['id'] ?? null;

        return new External_Landing_Link(
            $id ? new External_Link_ID((int)$id) : null,
            new Product_ID((int)$row['product_id']),
            new External_Url($row['url'])
        );
    }
}