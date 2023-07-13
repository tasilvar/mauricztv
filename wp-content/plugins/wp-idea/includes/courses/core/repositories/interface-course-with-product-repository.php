<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\courses\core\repositories;

use bpmj\wpidea\courses\core\entities\Course_With_Product;
use bpmj\wpidea\learning\course\Course_ID;
use bpmj\wpidea\sales\product\model\Product_ID;

interface Interface_Course_With_Product_Repository
{
    public function save(Course_With_Product $course_with_product): Course_ID;

    public function find(Course_ID $course_id): ?Course_With_Product;

    public function get_course_id_by_product(Product_ID $product_id): ?Course_ID;
}
