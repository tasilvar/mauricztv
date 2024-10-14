<?php

namespace bpmj\wpidea\sales\price_history\core\model;

use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\sales\product\model\Product_Price;
use bpmj\wpidea\sales\product\model\Variant_ID;

class Lowest_Price
{
    private Product_ID $product_id;
    private ?Variant_ID $product_variant_id;
    private Product_Price $amount;

    public function __construct(Product_ID $product_id, ?Variant_ID $product_variant_id, Product_Price $amount)
    {
        $this->product_id = $product_id;
        $this->product_variant_id = $product_variant_id;
        $this->amount = $amount;
    }

    public function get_product_id(): Product_ID
    {
        return $this->product_id;
    }

    public function get_product_variant_id(): ?Variant_ID
    {
        return $this->product_variant_id;
    }

    public function get_amount(): Product_Price
    {
        return $this->amount;
    }
}