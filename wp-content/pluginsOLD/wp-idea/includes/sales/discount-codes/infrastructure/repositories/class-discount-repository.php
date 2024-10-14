<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\sales\discount_codes\infrastructure\repositories;

use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\sales\discount_codes\core\collections\Discount_Collection;
use bpmj\wpidea\sales\discount_codes\core\entities\Discount;
use bpmj\wpidea\sales\discount_codes\core\repositories\Discount_Query_Criteria;
use bpmj\wpidea\sales\discount_codes\core\repositories\Interface_Discount_Repository;
use bpmj\wpidea\sales\discount_codes\core\value_objects\Amount;
use bpmj\wpidea\sales\discount_codes\core\value_objects\Code;
use bpmj\wpidea\sales\discount_codes\core\value_objects\Discount_ID;
use bpmj\wpidea\sales\discount_codes\core\value_objects\Max_Uses;
use bpmj\wpidea\sales\discount_codes\core\value_objects\Name;
use bpmj\wpidea\sales\discount_codes\core\value_objects\Status;
use bpmj\wpidea\sales\discount_codes\core\value_objects\Time_Limit;
use bpmj\wpidea\sales\discount_codes\core\value_objects\Uses;
use bpmj\wpidea\sales\discount_codes\infrastructure\persistence\Discount_Persistence;
use DateTimeImmutable;

class Discount_Repository implements Interface_Discount_Repository
{
    private Discount_Persistence $persistence;

    public function __construct(
        Discount_Persistence $persistence
    ) {
        $this->persistence = $persistence;
    }

    public function find_all(): Discount_Collection
    {
        $criteria = new Discount_Query_Criteria();
        $per_page = -1;
        $page = 1;
        $sort_by = null;
        
        $data = $this->persistence->find_by_criteria($criteria, $per_page, $page, $sort_by);
        
        return $this->create_entities_collection_from_data($data);
    }

    public function find_by_criteria(
        Discount_Query_Criteria $criteria,
        int $per_page = 0,
        int $page = 1,
        ?Sort_By_Clause $sort_by = null,
        $exclude_slow_data = false
    ): Discount_Collection {
        $data = $this->persistence->find_by_criteria($criteria, $per_page, $page, $sort_by, $exclude_slow_data);

        return $this->create_entities_collection_from_data($data);
    }

    public function count_by_criteria(Discount_Query_Criteria $criteria): int
    {
        return $this->persistence->count_by_criteria($criteria);
    }

    public function delete(Discount_ID $id): void
    {
        $this->persistence->delete($id->to_int());
    }

    private function create_entities_collection_from_data(array $data): Discount_Collection
    {
        $collection = Discount_Collection::create();

        foreach ($data as $row) {
            $collection->add(
                Discount::create(
                    new Discount_ID($row['ID']),
                    new Code($row['code']),
                    new Name($row['name']),
                    new Amount($row['amount'], $row['amount_type']),
                    new Uses($row['uses']),
                    ($row['uses_max'] > 0) ? new Max_Uses($row['uses_max']) : null,
                    new Time_Limit(
                        $row['start_date'] ? new DateTimeImmutable($row['start_date']) : null,
                        $row['expiration'] ? new DateTimeImmutable($row['expiration']) : null
                    ),
                    new Status($row['status'])
                )
            );
        }

        return $collection;
    }
}