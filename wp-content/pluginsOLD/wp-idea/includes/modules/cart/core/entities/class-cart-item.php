<?php

declare(strict_types=1);

namespace bpmj\wpidea\modules\cart\core\entities;

use bpmj\wpidea\modules\cart\core\value_objects\Product_ID;
use bpmj\wpidea\modules\cart\core\value_objects\Price_ID;

class Cart_Item
{
    private ?Product_ID $product_id;
    private ?Price_ID $price_id;
    private ?int $quantity;

    public function __construct(
        ?Product_ID $product_id = null,
        ?Price_ID $price_id = null,
        ?int $quantity = null
    ) {
        $this->product_id = $product_id;
        $this->price_id = $price_id;
        $this->quantity = $quantity;
    }

    public function get_item_product_id(): ?Product_ID
    {
        return $this->product_id;
    }

    public function get_item_price_id(): ?Price_ID
    {
        return $this->price_id;
    }

    public function get_item_quantity(): ?int
    {
        return $this->quantity;
    }
}
