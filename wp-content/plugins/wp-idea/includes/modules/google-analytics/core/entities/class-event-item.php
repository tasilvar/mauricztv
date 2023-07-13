<?php

declare(strict_types=1);

namespace bpmj\wpidea\modules\google_analytics\core\entities;

class Event_Item
{
    private int $item_id;
    private string $item_name;
    private string $item_variant;
    private int $quantity;
    private ?float $discount;
    private float $price;

    public function __construct(
        int $item_id,
        string $item_name,
        string $item_variant,
        int $quantity,
        float $price,
        ?float $discount = null
    ) {
        $this->item_id = $item_id;
        $this->item_name = $item_name;
        $this->item_variant = $item_variant;
        $this->quantity = $quantity;
        $this->price = $price;
        $this->discount = $discount;
    }

    public function get_item_id(): int
    {
        return $this->item_id;
    }

    public function get_item_name(): string
    {
        return $this->item_name;
    }

    public function get_item_variant(): string
    {
        return $this->item_variant;
    }

    public function get_quantity(): int
    {
        return $this->quantity;
    }

    public function get_price(): float
    {
        return $this->price;
    }

    public function get_discount(): ?float
    {
        return $this->discount;
    }
}
