<?php

namespace bpmj\wpidea\modules\affiliate_program\infrastructure\repositories;

use bpmj\wpidea\data_types\mail\Email_Address;
use bpmj\wpidea\data_types\personal_data\Full_Name;
use bpmj\wpidea\modules\affiliate_program\core\entities\Partner_Collection;
use bpmj\wpidea\modules\affiliate_program\core\value_objects\Affiliate_ID;
use bpmj\wpidea\modules\affiliate_program\core\entities\Partner;
use bpmj\wpidea\modules\affiliate_program\core\value_objects\Partner_ID;
use bpmj\wpidea\modules\affiliate_program\core\repositories\Interface_Partner_Repository;
use bpmj\wpidea\modules\affiliate_program\infrastructure\persistence\Interface_Partner_Persistence;
use bpmj\wpidea\modules\affiliate_program\core\repositories\Partner_Query_Criteria;
use bpmj\wpidea\user\User_ID;
use bpmj\wpidea\modules\affiliate_program\core\entities\Partner_Factory;
use bpmj\wpidea\modules\affiliate_program\core\entities\Partner_Dto;
use bpmj\wpidea\modules\affiliate_program\core\entities\Partner_Dto_Factory;
use bpmj\wpidea\infrastructure\database\Sort_By_Clause;

class Partner_WP_Repository implements Interface_Partner_Repository
{
    private Interface_Partner_Persistence $persistence;
    private Partner_Factory $partner_factory;
    private Partner_Dto_Factory $partner_dto_factory;

    public function __construct(
        Interface_Partner_Persistence $persistence,
        Partner_Factory $partner_factory,
        Partner_Dto_Factory $partner_dto_factory
    )
    {
        $this->persistence = $persistence;
        $this->partner_factory = $partner_factory;
        $this->partner_dto_factory = $partner_dto_factory;
    }

    public function create(Partner $partner): void
    {
        $this->persistence->insert($partner);
    }

    public function find_by_id(Partner_ID $id): ?Partner
    {
        return $this->persistence->find_by_id($id);
    }

    public function find_by_affiliate_id(Affiliate_ID $affiliate_id): ?Partner
    {
        return $this->persistence->find_by_affiliate_id($affiliate_id);
    }

    /**
     * @throws \Exception
     */
    public function create_partner_model_from_user(int $user_id): Partner
    {
        $wp_user = get_userdata($user_id);

        $dto = new Partner_Dto();
        $dto->user_id = $user_id;
        $dto->affiliate_id = $this->get_random_unique_affiliate_id_slug();
        $dto->first_name = $wp_user->first_name;
        $dto->last_name = $wp_user->last_name;
        $dto->email = $wp_user->user_email;

        return $this->partner_factory->create_partner_from_dto($dto);
    }

    public function find_by_user_id(User_ID $id): ?Partner
    {
        return $this->persistence->find_by_user_id($id);
    }

    public function find_by_criteria(
        Partner_Query_Criteria $criteria,
        int $page = 1,
        int $per_page = 10,
        Sort_By_Clause $sort_by = null
    ): Partner_Collection {
        $data = $this->persistence->find_by_criteria($criteria, $per_page, $page, $sort_by);
        $collection = new Partner_Collection();
        foreach ($data as $row) {
            $dto = $this->partner_dto_factory->create_dto_from_array($row);
            $partner = $this->partner_factory->create_partner_from_dto($dto);
            $collection->add($partner);
        }
        return $collection;
    }

    public function count_by_criteria(Partner_Query_Criteria $criteria): int
    {
        return $this->persistence->count_by_criteria($criteria);
    }

    private function get_random_unique_affiliate_id_slug(): string
    {
        $id_string = substr(strtolower(md5(time() + rand(0, 99999999))), 0, 6);

        if ($this->find_by_affiliate_id(new Affiliate_ID($id_string))) {
            return $this->get_random_unique_affiliate_id_slug();
        }

        return $id_string;
    }
}