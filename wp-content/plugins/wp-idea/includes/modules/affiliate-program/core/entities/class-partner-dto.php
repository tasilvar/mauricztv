<?php

namespace bpmj\wpidea\modules\affiliate_program\core\entities;

class Partner_Dto
{
    public ?int $id = null;

    public int $user_id;

    public string $affiliate_id;

    public string $email;

    public bool $is_active = false;

    public string $first_name = '';

    public string $last_name = '';

    public ?string $created_at = null;

    public ?int $commissions_sum_in_fractions = null;

    public ?int $unsettled_commissions_sum_in_fractions = null;

    public ?int $sales_sum_in_fractions = null;
}