<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\sales\product\model;

class Product_Variant
{
    private Variant_ID $id;
    private string $name;
    private float $amount;
    private ?float $sale_price;
    private bool $recurring_payments_enabled;
    private ?int $purchase_limit;
    private ?int $purchase_limit_items_left;
    private ?int $access_time;
    private ?string $access_time_unit;


    public function __construct(
        Variant_ID $id,
        string $name,
        float $amount,
        ?float $sale_price = null,
        bool $recurring_payments_enabled = false,
        ?int $purchase_limit = null,
        ?int $purchase_limit_items_left = null,
        ?int $access_time = null,
        ?string $access_time_unit = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->amount = $amount;
        $this->sale_price = $sale_price;
        $this->recurring_payments_enabled = $recurring_payments_enabled;
        $this->purchase_limit = $purchase_limit;
        $this->purchase_limit_items_left = $purchase_limit_items_left;
        $this->access_time = $access_time;
        $this->access_time_unit = $access_time_unit;
    }

    public function get_id(): Variant_ID
    {
        return $this->id;
    }

    public function get_name(): string
    {
        return $this->name;
    }

    public function get_amount(): float
    {
        return $this->amount;
    }

    public function get_sale_price(): ?float
    {
        return $this->sale_price;
    }

    public function get_recurring_payments_enabled(): bool
    {
        return $this->recurring_payments_enabled;
    }

    public function get_purchase_limit(): ?int
    {
        return $this->purchase_limit;
    }

    public function get_purchase_limit_items_left(): ?int
    {
        return $this->purchase_limit_items_left;
    }

    public function get_access_time(): ?int
    {
        return $this->access_time;
    }

    public function get_access_time_unit(): ?string
    {
        return $this->access_time_unit;
    }

}