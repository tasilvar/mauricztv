<?php
declare(strict_types=1);

namespace bpmj\wpidea\courses\core\repositories;

use bpmj\wpidea\courses\core\entities\Course_Structure;
use bpmj\wpidea\learning\course\Course_ID;

interface Interface_Course_Structure_Repository
{
    public function save(Course_Structure $course_structure): bool;

    public function find_by_id(Course_ID $course_id): ?Course_Structure;
}
