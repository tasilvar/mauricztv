<?php

namespace bpmj\wpidea\sales\price_history\infrastructure\provider;

use bpmj\wpidea\sales\price_history\core\provider\Interface_Product_Data_Provider;

class Product_Data_Provider implements Interface_Product_Data_Provider
{
    public function get_product_regular_price(int $product_id): ?float
    {
        if(!metadata_exists('post', $product_id, 'edd_price')) {
            return null;
        }

        $regular_price = get_post_meta($product_id, 'edd_price', true);

        return $regular_price === '' ? 0 : (float)$regular_price;
    }

    public function get_product_promo_price(int $product_id): ?float
    {
        $promo_price = get_post_meta($product_id, 'edd_sale_price', true);

        return $promo_price === '' ? null : (float)$promo_price;
    }

    public function get_product_variable_prices(int $product_id): ?array
    {
        $variable_prices = get_post_meta($product_id, 'edd_variable_prices', true);

        return $variable_prices === '' ? null : $variable_prices;
    }
}