<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\sales\product\service;

use bpmj\wpidea\sales\product\model\Product_And_Variant_ID;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\sales\product\model\Variant_ID;

class Variant_IDs_Parser
{
    public function parse_product_id_to_string_id(Product_ID $product_id, ?Variant_ID $variant_id): string
    {
        $product_variant_id = '';

        if ($variant_id) {
            $product_variant_id = '_' . $variant_id->to_int();
        }

        return $product_id->to_int() . $product_variant_id;
    }

    public function parse_string_id_to_product_and_variant_id(string $id): Product_And_Variant_ID
    {
        $exploded_id = explode('_', $id);

        $product_id = new Product_ID((int)$exploded_id[0]);
        $variant_id = !empty($exploded_id[1]) ? new Variant_ID((int)($exploded_id[1])) : null;

        return new Product_And_Variant_ID($product_id, $variant_id);
    }

}