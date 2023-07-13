<?php

namespace bpmj\wpidea\students\repository;

use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\students\model\Student_Collection;
use bpmj\wpidea\students\persistence\Student_Query_Criteria;

interface Interface_Student_Repository
{
    public function get_students_with_access_to_course(
        Product_ID $product_id,
        Sort_By_Clause $sort_by,
        int $per_page,
        int $page): Student_Collection;

    public function find_by_criteria(
        Student_Query_Criteria $criteria,
        int $page,
        int $per_page,
        ?Sort_By_Clause $sort_by = null): Student_Collection;

    public function count_by_criteria(Student_Query_Criteria $criteria): int;
}