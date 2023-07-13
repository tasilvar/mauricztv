<?php

namespace bpmj\wpidea\modules\affiliate_program\core\services;

use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\modules\affiliate_program\api\dto\collections\Partner_External_Landing_Link_DTO_Collection;
use bpmj\wpidea\modules\affiliate_program\api\dto\Partner_External_Landing_Link_DTO;
use bpmj\wpidea\modules\affiliate_program\core\entities\Commission_Collection;
use bpmj\wpidea\modules\affiliate_program\core\entities\Partner;
use bpmj\wpidea\modules\affiliate_program\core\repositories\Interface_Commission_Repository;
use bpmj\wpidea\modules\affiliate_program\core\repositories\Interface_Partner_Repository;
use bpmj\wpidea\modules\affiliate_program\core\value_objects\Partner_Info;
use bpmj\wpidea\modules\affiliate_program\infrastructure\persistence\Commission_Query_Criteria;
use bpmj\wpidea\user\Interface_Current_User_Getter;

class Affiliate_Program_Data_Getter
{
    private Affiliate_Link_Generator $affiliate_link_generator;
    private Interface_Partner_Repository $partner_repository;
    private Interface_Current_User_Getter $current_user_getter;
    private Interface_External_Landing_Link_Service $external_landing_link_service;
    private Interface_Commission_Repository $commission_repository;

    public function __construct(
        Affiliate_Link_Generator $affiliate_link_generator,
        Interface_Partner_Repository $partner_repository,
        Interface_Current_User_Getter $current_user_getter,
        Interface_Commission_Repository $commission_repository,
        Interface_External_Landing_Link_Service $external_landing_link_service
    ) {
        $this->affiliate_link_generator = $affiliate_link_generator;
        $this->partner_repository = $partner_repository;
        $this->current_user_getter = $current_user_getter;
        $this->commission_repository = $commission_repository;
        $this->external_landing_link_service = $external_landing_link_service;
    }

    public function get_partner_info(): ?Partner_Info
    {
        $partner = $this->get_partner();

        if (!$partner) {
            return null;
        }

        $link = $this->affiliate_link_generator->get_partner_affiliate_link($partner)->get_value();
        $partner_id = $partner->get_affiliate_id()->as_string();
        $external_landing_links = $this->get_external_landing_link_dtos($partner);

        return new Partner_Info($partner_id, $link, $external_landing_links);
    }

    public function get_partner_commissions(): ?Commission_Collection
    {
        $partner = $this->get_partner();

        if (!$partner) {
            return null;
        }

        $criteria = new Commission_Query_Criteria();
        $criteria->set_partner_id($partner->get_id()->to_int());

        $sort_by = new Sort_By_Clause();
        $sort_by->sort_by('created_at', true);

        return $this->commission_repository->find_by_criteria($criteria, 0, 1, $sort_by);
    }

    private function get_external_landing_link_dtos(Partner $partner): Partner_External_Landing_Link_DTO_Collection
    {
        $links = $this->external_landing_link_service->find_all();
        $dto_collection = Partner_External_Landing_Link_DTO_Collection::create();

        foreach ($links as $item) {
            $affiliate_url = $this->affiliate_link_generator->get_partner_affiliate_link_to_external_landing(
                $partner,
                $item
            );

            $dto = Partner_External_Landing_Link_DTO::create(
                $item->get_id()->to_int(),
                $item->get_product_id()->to_int(),
                $affiliate_url->get_value()
            );

            $dto_collection->add($dto);
        }

        return $dto_collection;
    }

    private function get_partner(): ?Partner
    {
        $user = $this->current_user_getter->get();

        if (!$user) {
            return null;
        }

        return $this->partner_repository->find_by_user_id($user->get_id());
    }
}
