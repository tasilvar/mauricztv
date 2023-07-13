<?php

namespace bpmj\wpidea\modules\opinions\infrastructure\repositories;

use bpmj\wpidea\data_types\ID;
use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\modules\opinions\core\collections\Opinion_Collection;
use bpmj\wpidea\modules\opinions\core\entities\Opinion;
use bpmj\wpidea\modules\opinions\core\repositories\Interface_Opinion_Repository;
use bpmj\wpidea\modules\opinions\core\value_objects\Opinion_Content;
use bpmj\wpidea\modules\opinions\core\value_objects\Opinion_Rating;
use bpmj\wpidea\modules\opinions\core\value_objects\Opinion_Status;
use bpmj\wpidea\modules\opinions\infrastructure\persistence\Interface_Opinions_Persistence;
use bpmj\wpidea\modules\opinions\infrastructure\persistence\Opinions_Query_Criteria;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\user\User_ID;
use DateTime;

class Opinion_Repository implements Interface_Opinion_Repository
{
    private Interface_Opinions_Persistence $persistence;

    public function __construct(
        Interface_Opinions_Persistence $persistence
    ) {
        $this->persistence = $persistence;
    }

    public function find_by_criteria(Opinions_Query_Criteria $criteria, int $per_page = 0, int $page = 1, ?Sort_By_Clause $sort_by = null): Opinion_Collection
    {
        $items = [];
        foreach ($this->persistence->find_by_criteria($criteria, $per_page, $page, $sort_by) as $row) {
            $items[] = Opinion::load(
                new ID($row['id']),
                new Product_ID($row['product_id']),
                new User_ID($row['user_id']),
                new Opinion_Content($row['opinion_content']),
                new DateTime($row['date_of_issue']),
                new Opinion_Status($row['status']),
                new Opinion_Rating($row['rating']),
	            $row['user_full_name'],
	            $row['product_name'],
	            $row['user_email'],
            );
        }

        return Opinion_Collection::create_from_array($items);
    }

    public function count_by_criteria(Opinions_Query_Criteria $criteria): int
    {
        return $this->persistence->count_by_criteria($criteria);
    }

    public function update(Opinion $opinion): void
    {
        $this->persistence->update($opinion);
    }

    public function find_by_id(ID $id): ?Opinion
    {
        $criteria = new Opinions_Query_Criteria();
        $criteria->set_opinion_id($id->to_int());

        $result = $this->find_by_criteria($criteria);

        if ($result->is_empty()) {
            return null;
        }

        return $result->current();
    }

    public function create(Opinion $opinion): void
    {
        $this->persistence->insert($opinion);
    }
}