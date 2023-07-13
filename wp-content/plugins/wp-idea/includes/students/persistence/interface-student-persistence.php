<?php

namespace bpmj\wpidea\students\persistence;

use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\students\vo\Student_ID;

interface Interface_Student_Persistence
{
    public function get_students_with_access_to_course(Product_ID $product_id,
                                                       Sort_By_Clause $sort_by,
                                                       int $per_page,
                                                       int $page): array;

    public function find_by_criteria(
        Student_Query_Criteria $criteria,
        int $page,
        int $per_page,
        ?Sort_By_Clause $sort_by = null
    ): array;

    public function get_student_access_time_to_course(Student_ID $student_id, Product_ID $product_id): ?int;

    public function count_by_criteria(Student_Query_Criteria $criteria): int;
}