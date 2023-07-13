<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\sales\product\model;

class Product_And_Variant_ID
{
    private Product_ID $product_id;
    private ?Variant_ID $variant_id;

    public function __construct(
        Product_ID $product_id,
        ?Variant_ID $variant_id
    ) {
        $this->product_id = $product_id;
        $this->variant_id = $variant_id;
    }

    public function get_product_id(): Product_ID
    {
        return $this->product_id;
    }

    public function get_variant_id(): ?Variant_ID
    {
        return $this->variant_id;
    }
}