<?php

namespace bpmj\wpidea\modules\affiliate_program\core\entities;

class Partner_Stats
{
    private int $commissions_sum_in_fractions;
    private int $unsettled_commissions_sum_in_fractions;
    private int $sales_sum_in_fractions;

    public function __construct(
        int $commissions_sum_in_fractions,
        int $unsettled_commissions_sum_in_fractions,
        int $sales_sum_in_fractions
    )
    {
        $this->commissions_sum_in_fractions = $commissions_sum_in_fractions;
        $this->unsettled_commissions_sum_in_fractions = $unsettled_commissions_sum_in_fractions;
        $this->sales_sum_in_fractions = $sales_sum_in_fractions;
    }

    public function get_commissions_sum_in_fractions(): int
    {
        return $this->commissions_sum_in_fractions;
    }

    public function get_unsettled_commissions_sum_in_fractions(): int
    {
        return $this->unsettled_commissions_sum_in_fractions;
    }

    public function get_sales_sum_in_fractions(): int
    {
        return $this->sales_sum_in_fractions;
    }
}