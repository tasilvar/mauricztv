<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\modules\cart\core\entities;

class Fee
{

    private string $id;
    private string $label;
    private float $amount;
    private float $net_amount;
    private int $tax_rate;

    private function __construct(
        string $id,
        string $label,
        float $amount,
        float $net_amount,
        int $tax_date
    )
    {
        $this->id = $id;
        $this->label = $label;
        $this->amount = $amount;
        $this->net_amount = $net_amount;
        $this->tax_rate = $tax_date;
    }

    public static function create(
        string $id,
        string $label,
        float $amount,
        float $net_amount,
        int $tax_rate
    ): self
    {
        return new self($id, $label, $amount, $net_amount, $tax_rate);
    }

    public function get_id(): string
    {
        return $this->id;
    }

    public function get_label(): string
    {
        return $this->label;
    }

    public function get_amount(): float
    {
        return $this->amount;
    }

    public function get_net_amount(): float
    {
        return $this->net_amount;
    }

    public function get_tax_rate(): int
    {
        return $this->tax_rate;
    }
}