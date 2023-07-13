<?php

namespace bpmj\wpidea\students\service;

use bpmj\wpidea\students\persistence\Interface_Student_Persistence;
use bpmj\wpidea\students\vo\Student_ID;
use bpmj\wpidea\sales\product\model\Product_ID;

class Student_Access_Time_To_Course_Provider
{
    private Interface_Student_Persistence $persistence;

    public function __construct(
        Interface_Student_Persistence $persistence
    )
    {
        $this->persistence = $persistence;
    }

    public function get_student_access_time_to_course(Student_ID $student_id, Product_ID $product_id): ?int
    {
        return $this->persistence->get_student_access_time_to_course($student_id, $product_id);
    }
}
