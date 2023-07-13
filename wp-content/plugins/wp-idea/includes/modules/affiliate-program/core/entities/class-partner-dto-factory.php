<?php

namespace bpmj\wpidea\modules\affiliate_program\core\entities;

class Partner_Dto_Factory
{
    public function create_dto_from_array(array $partner_data): Partner_Dto
    {
        $dto = new Partner_Dto();
        $dto->id = $partner_data['id'] ?? null;
        $dto->user_id = $partner_data['user_id'];
        $dto->affiliate_id = $partner_data['affiliate_id'];
        $dto->first_name = $partner_data['first_name'] ?? '';
        $dto->last_name = $partner_data['last_name'] ?? '';
        $dto->email = $partner_data['email'];
        $dto->created_at = $partner_data['created_at'] ?? null;
        $dto->is_active = $partner_data['is_active'] ?? false;
        $dto->commissions_sum_in_fractions = $partner_data['commissions_sum_in_fractions'] ?? null;
        $dto->unsettled_commissions_sum_in_fractions = $partner_data['unsettled_commissions_sum_in_fractions'] ?? null;
        $dto->sales_sum_in_fractions = $partner_data['sales_sum_in_fractions'] ?? null;

        return $dto;
    }
}