<?php
declare(strict_types=1);

namespace bpmj\wpidea\modules\increasing_sales\core\entities;

use bpmj\wpidea\modules\increasing_sales\core\value_objects\{
    Offer_ID,
    Product_ID,
    Variant_ID,
    Offered_Product_ID,
    Offered_Variant_ID,
    Increasing_Sales_Offer_Type
};

class Offer
{
    private ?Offer_ID $id;
    private Product_ID $product_id;
    private ?Variant_ID $product_variant_id;
    private Increasing_Sales_Offer_Type $offer_type;
    private Offered_Product_ID $offered_product_id;
    private ?Offered_Variant_ID $offered_product_variant_id;
    private ?string $title;
    private ?string $description;
    private ?string $image;
    private ?int $discount_in_fractions;

    private function __construct(
        ?Offer_ID $id,
        Product_ID $product_id,
        ?Variant_ID $product_variant_id,
        Increasing_Sales_Offer_Type $offer_type,
        Offered_Product_ID $offered_product_id,
        ?Offered_Variant_ID $offered_product_variant_id,
        ?string $title,
        ?string $description,
        ?string $image,
        ?int $discount_in_fractions
    )
    {
        $this->id = $id;
        $this->product_id = $product_id;
        $this->product_variant_id = $product_variant_id;
        $this->offer_type = $offer_type;
        $this->offered_product_id = $offered_product_id;
        $this->offered_product_variant_id = $offered_product_variant_id;
        $this->title = $title;
        $this->description = $description;
        $this->image = $image;
        $this->discount_in_fractions = $discount_in_fractions;
    }

    public static function create(
        ?Offer_ID $id,
        Product_ID $product_id,
        ?Variant_ID $product_variant_id,
        Increasing_Sales_Offer_Type $offer_type,
        Offered_Product_ID $offered_product_id,
        ?Offered_Variant_ID $offered_product_variant_id,
        ?string $title,
        ?string $description,
        ?string $image,
        ?int $discount_in_fractions
    ): self
    {
        return new self(
            $id,
            $product_id,
            $product_variant_id,
            $offer_type,
            $offered_product_id,
            $offered_product_variant_id,
            $title,
            $description,
            $image,
            $discount_in_fractions
        );
    }

    public function get_id(): ?Offer_ID
    {
        return $this->id;
    }

    public function get_product_id(): Product_ID
    {
        return $this->product_id;
    }

    public function get_product_variant_id(): ?Variant_ID
    {
        return $this->product_variant_id;
    }

    public function get_offer_type(): Increasing_Sales_Offer_Type
    {
        return $this->offer_type;
    }

    public function get_offered_product_id(): Offered_Product_ID
    {
        return $this->offered_product_id;
    }

    public function get_offered_product_variant_id(): ?Offered_Variant_ID
    {
        return $this->offered_product_variant_id;
    }

    public function get_title(): ?string
    {
        return $this->title;
    }

    public function get_description(): ?string
    {
        return $this->description;
    }

    public function get_image(): ?string
    {
        return $this->image;
    }

    public function get_discount_in_fractions(): ?int
    {
        return $this->discount_in_fractions;
    }
}