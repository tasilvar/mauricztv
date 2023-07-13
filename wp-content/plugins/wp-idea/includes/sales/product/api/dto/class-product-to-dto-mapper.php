<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\sales\product\api\dto;

use bpmj\wpidea\sales\product\model\Product;

class Product_To_DTO_Mapper
{
    public function map(Product $product): Product_DTO
    {
        return Product_DTO::create(
            $product->get_id()->to_int(),
            $product->get_name()->get_value(),
            $product->get_price()->get_value(),
            $product->get_variable_pricing_enabled(),
            $product->get_product_variants()
        );
    }
}