<?php

declare(strict_types=1);

namespace bpmj\wpidea\courses\core\service;

use bpmj\wpidea\Courses;
use bpmj\wpidea\courses\core\dto\Purchase_Limit_DTO;
use bpmj\wpidea\learning\course\{Course_ID};
use bpmj\wpidea\sales\product\api\Interface_Product_API;

class Product_Modifier
{
    private const PURCHASE_LIMIT_VARIABLE_PRICES_META_KEY = 'bpmj_eddcm_purchase_limit';
    private const PURCHASE_LIMIT_ITEMS_LEFT_VARIABLE_PRICES_META_KEY = 'bpmj_eddcm_purchase_limit_items_left';

    private Courses $courses;
    private Interface_Product_API $product_api;

    public function __construct(
        Interface_Product_API $product_api,
        Courses $courses
    ) {
        $this->product_api = $product_api;
        $this->courses = $courses;
    }

    public function update_purchase_limit_for_variable_pricing(Course_ID $course_id): void
    {
        $product_id = $this->courses->get_product_by_course($course_id->to_int());

        if (!$product_id) {
            return;
        }

        $purchase_limit_DTO = $this->product_api->get_purchase_limit((int)$product_id);
        $purchase_limit = $purchase_limit_DTO->limit;
        $purchase_limit_items_left = $purchase_limit_DTO->items_left;

        $purchase_limit_unlimited = false;

        if ($purchase_limit_items_left > $purchase_limit) {
            $purchase_limit_items_left = $purchase_limit;
        }

        $price_variants_DTO = $this->product_api->get_price_variants((int)$product_id);

        if ($price_variants_DTO->has_pricing_variants) {
            $purchase_limit = 0;
            $purchase_limit_items_left = 0;
            $any_purchase_limit_empty = false;

            foreach ($price_variants_DTO->variable_prices as $price) {
                if (!empty($price[self::PURCHASE_LIMIT_VARIABLE_PRICES_META_KEY])) {
                    $purchase_limit += (int)$price[self::PURCHASE_LIMIT_VARIABLE_PRICES_META_KEY];
                } else {
                    $any_purchase_limit_empty = true;
                }

                if (!empty($price[self::PURCHASE_LIMIT_ITEMS_LEFT_VARIABLE_PRICES_META_KEY])) {
                    $purchase_limit_items_left += (int)$price[self::PURCHASE_LIMIT_ITEMS_LEFT_VARIABLE_PRICES_META_KEY];
                }
            }
            if ($any_purchase_limit_empty && !empty($purchase_limit)) {
                $purchase_limit_unlimited = true;
            }
        }

        $purchase_limit_DTO = new Purchase_Limit_DTO();
        $purchase_limit_DTO->limit = $purchase_limit;
        $purchase_limit_DTO->items_left = $purchase_limit_items_left;
        $purchase_limit_DTO->unlimited = $purchase_limit_unlimited;

        $this->product_api->update_purchase_limit((int)$product_id, $purchase_limit_DTO);
    }
}