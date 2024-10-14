<?php

namespace bpmj\wpidea\students\repository;

use bpmj\wpidea\data_types\mail\Email_Address;
use bpmj\wpidea\data_types\personal_data\Full_Name;
use bpmj\wpidea\data_types\String_VO;
use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\students\model\Student;
use bpmj\wpidea\students\model\Student_Collection;
use bpmj\wpidea\students\persistence\Interface_Student_Persistence;
use bpmj\wpidea\students\persistence\Student_Query_Criteria;
use bpmj\wpidea\students\vo\Student_ID;

class Student_Wp_Repository implements Interface_Student_Repository
{
    private Interface_Student_Persistence $student_persistence;

    public function __construct(
        Interface_Student_Persistence $student_persistence
    )
    {
        $this->student_persistence = $student_persistence;
    }

    public function get_students_with_access_to_course(Product_ID $product_id, Sort_By_Clause $sort_by, int $per_page, int $page): Student_Collection
    {
        return $this->create_student_collection_from_student_data($this->student_persistence->get_students_with_access_to_course($product_id, $sort_by, $per_page, $page));
    }

    public function find_by_criteria(Student_Query_Criteria $criteria, int $page, int $per_page, ?Sort_By_Clause $sort_by = null): Student_Collection
    {
        return $this->create_student_collection_from_student_data($this->student_persistence->find_by_criteria($criteria, $page, $per_page, $sort_by));
    }

    private function create_student_collection_from_student_data(array $student_data): Student_Collection
    {
        $student_collection = Student_Collection::create();

        foreach ($student_data as $student) {
            $student_collection->add(Student::create(
                new Student_ID($student->ID),
                new Full_Name($student->user_firstname, $student->last_name),
                new Email_Address($student->user_email),
                new String_VO($student->user_login)
            ));
        }

        return $student_collection;
    }

    public function count_by_criteria(Student_Query_Criteria $criteria): int
    {
        return $this->student_persistence->count_by_criteria($criteria);
    }
}