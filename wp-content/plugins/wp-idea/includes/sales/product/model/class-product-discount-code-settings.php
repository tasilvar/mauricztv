<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\sales\product\model;

class Product_Discount_Code_Settings
{
    private ?string $sell_discount_code;
    private ?string $discount_code_period_validity;

    public function __construct(
        ?string $sell_discount_code,
        ?string $discount_code_period_validity
    ) {
        $this->sell_discount_code = $sell_discount_code;
        $this->discount_code_period_validity = $discount_code_period_validity;
    }

    public function get_sell_discount_code(): ?string
    {
        return $this->sell_discount_code ?? null;
    }

    public function get_sell_discount_time(): ?string
    {
        return $this->get_break_down_discount_code_period_validity() ? $this->get_break_down_discount_code_period_validity()[0] : null;
    }

    public function get_sell_discount_time_type(): ?string
    {
        return $this->get_break_down_discount_code_period_validity() ? $this->get_break_down_discount_code_period_validity()[1] : null;
    }

    public function get_discount_code_period_validity(): ?string
    {
        return $this->discount_code_period_validity ?? null;
    }

    private function get_break_down_discount_code_period_validity(): ?array
    {
        return $this->discount_code_period_validity ? explode('-', $this->discount_code_period_validity) : null;
    }

}