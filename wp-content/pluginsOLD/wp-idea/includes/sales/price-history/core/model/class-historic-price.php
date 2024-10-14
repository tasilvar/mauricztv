<?php

namespace bpmj\wpidea\sales\price_history\core\model;

use bpmj\wpidea\data_types\ID;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\sales\product\model\Product_Price;
use bpmj\wpidea\sales\product\model\Variant_ID;
use DateTime;

class Historic_Price
{
    private DateTime $date;
    private ?Product_Price $regular_price;
    private ?Product_Price $promo_price;
    private ?ID $id;
    private ?Product_ID $product_id;
    private ?Variant_ID $product_variant_id;
    private ?Product_Price $previous_regular_price;
    private ?Product_Price $previous_promo_price;

    private function __construct(
        DateTime $date,
        ?Product_Price $regular_price = null,
        ?Product_Price $promo_price = null,
        ?ID $id = null,
        ?Product_ID $product_id = null,
        ?Variant_ID $product_variant_id = null,
        ?Product_Price $previous_regular_price = null,
        ?Product_Price $previous_promo_price = null
    ) {
        $this->date = $date;
        $this->regular_price = $regular_price;
        $this->promo_price = $promo_price;
        $this->product_id = $product_id;
        $this->id = $id;
        $this->product_variant_id = $product_variant_id;
        $this->previous_regular_price = $previous_regular_price;
        $this->previous_promo_price = $previous_promo_price;
    }

    public static function create(
        DateTime $date,
        ?Product_Price $regular_price = null,
        ?Product_Price $promo_price = null,
        ?ID $id = null,
        ?Product_ID $product_id = null,
        ?Variant_ID $product_variant_id = null,
        ?Product_Price $previous_regular_price = null,
        ?Product_Price $previous_promo_price = null
    ): self {
        return new self($date, $regular_price, $promo_price, $id, $product_id, $product_variant_id, $previous_regular_price, $previous_promo_price);
    }

    public function get_date(): DateTime
    {
        return $this->date;
    }

    public function get_regular_price(): ?Product_Price
    {
        return $this->regular_price;
    }

    public function get_promo_price(): ?Product_Price
    {
        return $this->promo_price;
    }

    public function get_id(): ?ID
    {
        return $this->id;
    }

    public function get_product_id(): ?Product_ID
    {
        return $this->product_id;
    }

    public function get_product_variant_id(): ?Variant_ID
    {
        return $this->product_variant_id;
    }

    public function get_previous_regular_price(): ?Product_Price
    {
        return $this->previous_regular_price;
    }

    public function get_previous_promo_price(): ?Product_Price
    {
        return $this->previous_promo_price;
    }

    public function change_occurred_in_regular_price(): bool
    {
        return $this->regular_price->get_value() !== $this->previous_regular_price->get_value();
    }
}