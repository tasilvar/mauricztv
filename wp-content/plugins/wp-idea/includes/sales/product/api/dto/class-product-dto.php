<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\sales\product\api\dto;

use bpmj\wpidea\sales\product\model\collection\Product_Variant_Collection;

class Product_DTO
{
    private int $id;
    private string $name;
    private ?float $price;
    private bool $variable_pricing_enabled;
    private ?Product_Variant_Collection $product_variants;

    private function __construct(int $id, string $name, ?float $price, bool $variable_pricing_enabled, ?Product_Variant_Collection $product_variants)
    {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->variable_pricing_enabled = $variable_pricing_enabled;
        $this->product_variants = $product_variants;
    }

    public static function create(
        int $id,
        string $name,
        ?float $price,
        bool $variable_pricing_enabled,
        ?Product_Variant_Collection $product_variants
    ): self
    {
        return new self(
            $id,
            $name,
            $price,
            $variable_pricing_enabled,
            $product_variants
        );
    }

    public function get_id(): int
    {
        return $this->id;
    }

    public function get_name(): string
    {
        return $this->name;
    }

    public function get_price(): ?float
    {
        return $this->price;
    }

    public function get_variable_pricing_enabled(): bool
    {
        return $this->variable_pricing_enabled;
    }

    public function get_product_variants(): ?Product_Variant_Collection
    {
        return $this->product_variants;
    }


}