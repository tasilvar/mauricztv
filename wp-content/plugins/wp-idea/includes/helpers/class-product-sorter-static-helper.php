<?php

namespace bpmj\wpidea\helpers;


class Product_Sorter_Static_Helper
{
    public static function get_products_by_custom_sorting(array $products_ids_by_custom_sorting_order, array $all_products): array
    {
        $products_by_custom_sorting_order = [];

        foreach ($products_ids_by_custom_sorting_order as $product_id) {
            foreach ($all_products as $product) {
                if ((int)$product['id'] === (int)$product_id) {
                    $products_by_custom_sorting_order[] = $product;
                    break;
                }
            }
        }

        foreach ($all_products as $product) {
            if (!in_array($product, $products_by_custom_sorting_order, true)) {
                $products_by_custom_sorting_order[] = $product;
            }
        }

        return $products_by_custom_sorting_order;
    }
}