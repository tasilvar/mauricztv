<?php

namespace bpmj\wpidea\modules\affiliate_program\infrastructure\repositories;

use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\modules\affiliate_program\core\entities\Commission;
use bpmj\wpidea\modules\affiliate_program\core\entities\Commission_Collection;
use bpmj\wpidea\modules\affiliate_program\core\value_objects\Partner_ID;
use bpmj\wpidea\modules\affiliate_program\core\repositories\Interface_Commission_Repository;
use bpmj\wpidea\modules\affiliate_program\core\value_objects\Commission_ID;
use bpmj\wpidea\modules\affiliate_program\core\value_objects\Status;
use bpmj\wpidea\modules\affiliate_program\infrastructure\persistence\Commission_Query_Criteria;
use bpmj\wpidea\modules\affiliate_program\infrastructure\persistence\Interface_Commission_Persistence;
use DateTime;

class Commission_Wp_Repository implements Interface_Commission_Repository
{
    private Interface_Commission_Persistence $commissions_persistence;

    public function __construct(
        Interface_Commission_Persistence $commissions_persistence
    ) {
        $this->commissions_persistence = $commissions_persistence;
    }

    public function create(Commission $commission): void
    {
        $this->commissions_persistence->insert($commission);
    }

    public function count_by_criteria(Commission_Query_Criteria $criteria): int
    {
        return $this->commissions_persistence->count_by_criteria($criteria);
    }

    public function find_by_id(int $id): ?Commission
    {
        $commission_row = $this->commissions_persistence->find_by_id($id);

        if (!$commission_row) {
            return null;
        }

        return $this->table_row_to_commission_model($commission_row);
    }

    public function find_by_criteria(
        Commission_Query_Criteria $criteria,
        int $per_page = 0,
        int $page = 1,
        ?Sort_By_Clause $sort_by = null
    ): Commission_Collection {
        $data = $this->commissions_persistence->find_by_criteria($criteria, $per_page, $page, $sort_by);

        return $this->create_entities_collection_from_data($data);
    }

    public function update(Commission $commission): void
    {
        $this->commissions_persistence->update($commission);
    }

    public function delete(int $id): void
    {
        $this->commissions_persistence->delete($id);
    }

    private function create_entities_collection_from_data(array $data): Commission_Collection
    {
        $collection = new Commission_Collection();

        foreach ($data as $row) {
            $collection->add($this->table_row_to_commission_model($row));
        }

        return $collection;
    }

    private function table_row_to_commission_model(array $row): Commission
    {
        return new Commission(
            new Commission_ID($row['id']),
            new Partner_ID($row['partner_id']),
            $row['partner_affiliate_id'],
            $row['partner_email'],
            $row['name'],
            $row['email'],
            is_array($row['products']) ? $row['products'] : json_decode($row['products'], true),
            $row['sale_amount'],
            $row['percentage'],
            $row['amount'],
            new DateTime($row['created_at']),
            new Status($row['status']),
            $row['campaign'] ?? null
        );
    }

}
