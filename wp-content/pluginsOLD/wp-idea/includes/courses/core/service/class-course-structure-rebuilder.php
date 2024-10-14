<?php

declare(strict_types=1);

namespace bpmj\wpidea\courses\core\service;

use bpmj\wpidea\Courses;
use bpmj\wpidea\courses\core\entities\Course_Structure;
use bpmj\wpidea\sales\product\api\Interface_Product_API;
use bpmj\wpidea\sales\product\model\Product_ID;

class Course_Structure_Rebuilder
{
    private Courses $courses;
    private Interface_Product_API $product_api;

    public function __construct(
        Interface_Product_API $product_api,
        Courses $courses
    ) {
        $this->product_api = $product_api;
        $this->courses = $courses;
    }

    public function get_rebuilded_structure(Course_Structure $course_structure): ?Course_Structure
    {
        $structure_items = $course_structure->to_array();

        if (!$structure_items) {
            return null;
        }

        $product_id = $this->courses->get_product_by_course($course_structure->get_course_id()->to_int());

        if (!$product_id) {
            return null;
        }

        $variable_prices = $this->get_variable_prices(new Product_ID((int)$product_id));

        foreach ($structure_items as $module_order => $module) {
            if ($variable_prices) {
                $structure_items[$module_order]['variable_prices'] = $variable_prices;
            } else {
                unset($structure_items[$module_order]['variable_prices']);
            }

            if (!isset($module['module'])) {
                continue;
            }

            foreach ($module['module'] as $lesson_order => $lesson) {
                if (!$variable_prices) {
                    unset($structure_items[$module_order]['module'][$lesson_order]['variable_prices']);
                    continue;
                }
                $structure_items[$module_order]['module'][$lesson_order]['variable_prices'] = $variable_prices;
            }
        }

        $fields = [
            'course_id' => $course_structure->get_course_id(),
            'access_to_dripping' => $course_structure->get_access_to_dripping(),
            'drip' => $course_structure->get_drip(),
            'module' => $structure_items
        ];

        return Course_Structure::from_array($fields);
    }

    private function get_variable_prices(Product_ID $product_id): ?array
    {
        $price_variants_DTO = $this->product_api->get_price_variants($product_id->to_int());

        if (!$price_variants_DTO->has_pricing_variants) {
            return null;
        }

        $variable_prices = $price_variants_DTO->variable_prices;

        if (empty($variable_prices) || !is_array($variable_prices)) {
            return null;
        }

        $array = [];

        foreach ($variable_prices as $id => $price) {
            $array[] = $id;
        }

        return $array;
    }
}