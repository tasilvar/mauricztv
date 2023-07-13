<?php

namespace bpmj\wpidea\modules\affiliate_program\core\entities;

use bpmj\wpidea\user\User_ID;
use bpmj\wpidea\modules\affiliate_program\core\value_objects\Affiliate_ID;
use bpmj\wpidea\data_types\personal_data\Full_Name;
use bpmj\wpidea\data_types\mail\Email_Address;
use bpmj\wpidea\modules\affiliate_program\core\value_objects\Partner_ID;

class Partner_Factory
{
    public function create_partner_from_dto(Partner_Dto $dto): Partner
    {
        return new Partner(
            $dto->id ? new Partner_ID($dto->id) : null,
            new User_ID($dto->user_id),
            new Affiliate_ID($dto->affiliate_id),
            new Full_Name($dto->first_name ?? '', $dto->last_name ?? ''),
            new Email_Address($dto->email),
            $dto->created_at ? new \DateTime($dto->created_at) : null,
            $dto->is_active,
            $this->get_stats($dto)
        );
    }

    private function get_stats(Partner_Dto $dto): Partner_Stats
    {
        return new Partner_Stats(
            $dto->commissions_sum_in_fractions ?? 0,
            $dto->unsettled_commissions_sum_in_fractions ?? 0,
            $dto->sales_sum_in_fractions ?? 0

        );
    }
}