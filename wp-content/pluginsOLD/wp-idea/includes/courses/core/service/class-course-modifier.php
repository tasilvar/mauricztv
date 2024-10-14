<?php

declare(strict_types=1);

namespace bpmj\wpidea\courses\core\service;

use bpmj\wpidea\admin\pages\course_editor\core\configuration\General_Course_Group;
use bpmj\wpidea\learning\course\Course_ID;
use bpmj\wpidea\sales\product\model\Product_ID;

class Course_Modifier
{
    private const SALES_DISABLED_VALUE_ON = 'on';

    public function disable_sale_for_variable_pricing(Course_ID $course_id, Product_ID $product_id): void
    {
        $this->update_post_meta($product_id->to_int(), General_Course_Group::SALES_DISABLED, self::SALES_DISABLED_VALUE_ON);
        $this->update_post_meta($course_id->to_int(), General_Course_Group::SALES_DISABLED, self::SALES_DISABLED_VALUE_ON);
    }

    private function update_post_meta(int $id, string $key, $value): void
    {
        update_post_meta($id, $key, $value);
    }
}