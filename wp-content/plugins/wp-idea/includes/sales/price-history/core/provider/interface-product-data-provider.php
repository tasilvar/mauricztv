<?php

namespace bpmj\wpidea\sales\price_history\core\provider;

interface Interface_Product_Data_Provider
{
    public function get_product_regular_price(int $product_id): ?float;

    public function get_product_promo_price(int $product_id): ?float;

    public function get_product_variable_prices(int $product_id): ?array;
}