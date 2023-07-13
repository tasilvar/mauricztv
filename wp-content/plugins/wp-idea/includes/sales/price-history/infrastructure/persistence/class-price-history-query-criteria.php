<?php

namespace bpmj\wpidea\sales\price_history\infrastructure\persistence;

class Price_History_Query_Criteria
{
    private ?array $product_ids;
    private ?array $price;
    private ?array $type_of_price;
    private ?array $date_of_change;

    public function __construct(
        ?array $product_ids = null,
        ?array $price = null,
        ?array $type_of_price = null,
        ?array $date_of_change = null
    )
    {
        $this->product_ids = $product_ids;
        $this->price = $price;
        $this->type_of_price = $type_of_price;
        $this->date_of_change = $date_of_change;
    }

    public function get_product_ids(): ?array
    {
        return $this->product_ids;
    }

    public function get_price(): ?array
    {
        return $this->price;
    }

    public function get_type_of_price(): ?array
    {
        return $this->type_of_price;
    }

    public function get_date_of_change(): ?array
    {
        return $this->date_of_change;
    }
}