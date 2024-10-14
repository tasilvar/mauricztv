<?php
declare(strict_types=1);

namespace bpmj\wpidea\modules\increasing_sales\infrastructure\persistence;

class Offer_Query_Criteria
{
    public ?int $id;
    public ?int $product_variant_id;
    public ?array $products;
    public ?string $offer_type;
    public ?array $offered_products;
    public ?array $discounts;

    public function __construct(
        ?int $id = null,
        ?array $products = null,
        ?int $product_variant_id = null,
        ?string $offer_type = null,
        ?array $offered_products = null,
        ?array $discounts = null
    ) {
        $this->id = $id;
        $this->products = $products;
        $this->product_variant_id = $product_variant_id;
        $this->offer_type = $offer_type;
        $this->offered_products = $offered_products;
        $this->discounts = $discounts;
    }

    public function get_id(): ?int
    {
        return $this->id;
    }

    public function product_variant_id(): ?int
    {
        return $this->product_variant_id;
    }

    public function get_products(): ?array
    {
        return $this->products;
    }

    public function get_offer_type(): ?string
    {
        return $this->offer_type;
    }

    public function get_offered_products(): ?array
    {
        return $this->offered_products;
    }

    public function get_discounts(): ?array
    {
        return $this->discounts;
    }
}