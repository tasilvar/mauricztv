<?php

namespace bpmj\wpidea\modules\affiliate_program\infrastructure\persistence;

use bpmj\wpidea\data_types\mail\Email_Address;
use bpmj\wpidea\data_types\personal_data\Full_Name;
use bpmj\wpidea\infrastructure\database\Interface_Database;
use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\modules\affiliate_program\core\entities\Partner;
use bpmj\wpidea\modules\affiliate_program\core\value_objects\Partner_ID;
use bpmj\wpidea\modules\affiliate_program\core\value_objects\Affiliate_ID;
use bpmj\wpidea\user\User_ID;
use bpmj\wpidea\modules\affiliate_program\core\repositories\Partner_Query_Criteria;
use bpmj\wpidea\Caps;
use bpmj\wpidea\modules\affiliate_program\core\value_objects\Status;
use bpmj\wpidea\events\Event_Name;
use bpmj\wpidea\modules\affiliate_program\core\entities\Partner_Dto;
use bpmj\wpidea\modules\affiliate_program\core\entities\Partner_Factory;
use bpmj\wpidea\modules\affiliate_program\core\entities\Partner_Dto_Factory;
use bpmj\wpidea\infrastructure\database\Interface_Sql_Helper;
use bpmj\wpidea\helpers\Price_Formatting;
use bpmj\wpidea\infrastructure\database\Sort_By;
use bpmj\wpidea\modules\affiliate_program\core\services\Affiliate_Link_Generator;

class Partner_Persistence implements Interface_Partner_Persistence
{
    public const TABLE_NAME = 'wpi_affiliate_program_partners';

    private const STATUS_ACTIVE_SQL_RETURN_VALUE = 1;
    private const STATUS_INCACTIVE_SQL_RETURN_VALUE = 0;

    private const QUERY_TYPE_SELECT = 'select';
    private const QUERY_TYPE_COUNT = 'select_count';
    private const USERMETA_TABLE_NAME = 'usermeta';
    private const CAPABILITIES_TABLE_NAME = 'capabilities';

    private Interface_Database $db;
    private Partner_Factory $partner_factory;
    private Partner_Dto_Factory $partner_dto_factory;
    private Interface_Sql_Helper $sql_helper;
    private Affiliate_Link_Generator $affiliate_link_generator;

    public function __construct(
        Interface_Database $db,
        Partner_Factory $partner_factory,
        Partner_Dto_Factory $partner_dto_factory,
        Interface_Sql_Helper $sql_helper,
        Affiliate_Link_Generator $affiliate_link_generator
    )
    {
        $this->db = $db;
        $this->partner_factory = $partner_factory;
        $this->partner_dto_factory = $partner_dto_factory;
        $this->sql_helper = $sql_helper;
        $this->affiliate_link_generator = $affiliate_link_generator;
    }

    public function setup(): void
    {
        $this->db->create_table_if_not_exists(
            self::TABLE_NAME,
            [
                'id int UNSIGNED NOT NULL AUTO_INCREMENT',
                'user_id bigint(20) UNSIGNED NOT NULL',
                'affiliate_id varchar(50) NOT NULL',
                'first_name varchar(255) NOT NULL',
                'last_name varchar(255) NOT NULL',
                'email varchar(255) NOT NULL',
                'created_at datetime NOT NULL DEFAULT NOW()'
            ],
            'id',
            [
                "KEY (affiliate_id)"
            ]
        );
    }

    public function insert(Partner $partner): void
    {
        $this->db->insert(self::TABLE_NAME, [
            'user_id' => $partner->get_user_id()->to_int(),
            'affiliate_id' => $partner->get_affiliate_id()->as_string(),
            'first_name' => $partner->get_full_name()->get_first_name(),
            'last_name' => $partner->get_full_name()->get_last_name(),
            'email' => $partner->get_email()->get_value()
        ]);
    }

    public function find_by_criteria(
        Partner_Query_Criteria $criteria,
        int $per_page = 0,
        int $page = 1,
        ?Sort_By_Clause $sort_by = null
    ): array {
        return $this->select_partners(self::QUERY_TYPE_SELECT, $per_page, $page, $criteria, $sort_by);
    }

    public function count_by_criteria(Partner_Query_Criteria $criteria): int
    {
        $total_count_result = $this->select_partners(self::QUERY_TYPE_COUNT, 0, 0, $criteria, null);

        return (int)($total_count_result[0]['total_count'] ?? 0);
    }

    /**
     * @throws \Exception
     */
    public function find_by_user_id(User_ID $user_id): ?Partner
    {
        $criteria = new Partner_Query_Criteria();
        $criteria->set_user_id($user_id);
        $results = $this->find_by_criteria($criteria, 1, 1);

        if (!$results) {
            return null;
        }

        $dto = $this->partner_dto_factory->create_dto_from_array($results[0]);

        return $this->partner_factory->create_partner_from_dto($dto);
    }

    /**
     * @throws \Exception
     */
    public function find_by_id(Partner_ID $partner_id): ?Partner
    {
        $criteria = new Partner_Query_Criteria();
        $criteria->set_id($partner_id);
        $results = $this->find_by_criteria($criteria, 1, 1);

        if (!$results) {
            return null;
        }

        $dto = $this->partner_dto_factory->create_dto_from_array($results[0]);

        return $this->partner_factory->create_partner_from_dto($dto);
    }

    /**
     * @throws \Exception
     */
    public function find_by_affiliate_id(Affiliate_ID $affiliate_id): ?Partner
    {
        $criteria = new Partner_Query_Criteria();
        $criteria->set_affiliate_id($affiliate_id);
        $results = $this->find_by_criteria($criteria, 1, 1);

        if (!$results) {
            return null;
        }

        $dto = $this->partner_dto_factory->create_dto_from_array($results[0]);

        return $this->partner_factory->create_partner_from_dto($dto);
    }

    private function parse_criteria_to_where_clause(Partner_Query_Criteria $criteria): array
    {
        $where = [];

        if ($criteria->get_id()) {
            $where[] = ['partners.id', '=', $criteria->get_id()->to_int()];
        }

        if ($criteria->get_user_id()) {
            $where[] = ['partners.user_id', '=', $criteria->get_user_id()->to_int()];
        }

        if ($criteria->get_affiliate_id()) {
            $where[] = ['partners.affiliate_id', '=', $criteria->get_affiliate_id()->as_string()];
        }

        if ($criteria->get_full_name_like()) {
            $where[] = ["CONCAT_WS(' ', partners.first_name, partners.last_name)", 'LIKE', $criteria->get_full_name_like()];
        }

        if ($criteria->get_email_like()) {
            $where[] = ['partners.email', 'LIKE', $criteria->get_email_like()];
        }

        if ($criteria->get_partner_link_like()) {
            $link_base = $this->affiliate_link_generator->get_affiliate_link_base();
            $where[] = ["CONCAT_WS('', '{$link_base}', partners.affiliate_id)", 'LIKE', $criteria->get_partner_link_like()];
        }

        return $where;
    }

    private function parse_criteria_to_having_clause(Partner_Query_Criteria $criteria): array
    {
        $having = [];

        if ($criteria->get_sales_sum_from()) {
            $sales_sum_from_in_fractions = Price_Formatting::round_and_format_to_int($criteria->get_sales_sum_from(), Price_Formatting::MULTIPLY_BY_100);
            $having[] = ['sales_sum_in_fractions', '>=', $sales_sum_from_in_fractions];
        }

        if ($criteria->get_sales_sum_to()) {
            $sales_sum_to_in_fractions = Price_Formatting::round_and_format_to_int($criteria->get_sales_sum_to(), Price_Formatting::MULTIPLY_BY_100);
            $having[] = ['sales_sum_in_fractions', '<=', $sales_sum_to_in_fractions];
        }

        if ($criteria->get_commissions_sum_from()) {
            $commissions_sum_from_in_fractions = Price_Formatting::round_and_format_to_int($criteria->get_commissions_sum_from(), Price_Formatting::MULTIPLY_BY_100);
            $having[] = ['commissions_sum_in_fractions', '>=', $commissions_sum_from_in_fractions];
        }

        if ($criteria->get_commissions_sum_to()) {
            $commissions_sum_to_in_fractions = Price_Formatting::round_and_format_to_int($criteria->get_commissions_sum_to(), Price_Formatting::MULTIPLY_BY_100);
            $having[] = ['commissions_sum_in_fractions', '<=', $commissions_sum_to_in_fractions];
        }

        if ($criteria->get_unsettled_commissions_sum_from()) {
            $unsettled_commissions_sum_from_in_fractions = Price_Formatting::round_and_format_to_int($criteria->get_unsettled_commissions_sum_from(), Price_Formatting::MULTIPLY_BY_100);
            $having[] = ['unsettled_commissions_sum_in_fractions', '>=', $unsettled_commissions_sum_from_in_fractions];
        }

        if ($criteria->get_unsettled_commissions_sum_to()) {
            $unsettled_commissions_sum_to_in_fractions = Price_Formatting::round_and_format_to_int($criteria->get_unsettled_commissions_sum_to(), Price_Formatting::MULTIPLY_BY_100);
            $having[] = ['unsettled_commissions_sum_in_fractions', '<=', $unsettled_commissions_sum_to_in_fractions];
        }

        if ($criteria->get_status()) {
            $status_value = $criteria->get_status() === Partner::STATUS_ACTIVE ? self::STATUS_ACTIVE_SQL_RETURN_VALUE : self::STATUS_INCACTIVE_SQL_RETURN_VALUE;
            $having[] = ['is_active', '=', $status_value];
        }

        return $having;
    }

    private function fix_column_names_in_sort_by_clause(?Sort_By_Clause $sort_by): ?Sort_By_Clause
    {
        if(!$sort_by) {
            return null;
        }

        $processed_sort_by = new Sort_By_Clause();

        foreach ($sort_by->get_all() as $item) {
            /** @var Sort_By $item */
            $property = $item->property;

            switch ($property) {
                case 'id':
                    $processed_sort_by->sort_by('partners.id', $item->desc);
                    break;

                case 'name':
                    $processed_sort_by->sort_by('partners.first_name', $item->desc);
                    $processed_sort_by->sort_by('partners.last_name', $item->desc);
                    break;

                case 'partner_email':
                    $processed_sort_by->sort_by('partners.email', $item->desc);
                    break;

                case 'partner_link':
                    $processed_sort_by->sort_by('partners.affiliate_id', $item->desc);
                    break;

                case 'sale_amount_sum':
                    $processed_sort_by->sort_by('sales_sum_in_fractions', $item->desc);
                    break;

                case 'amount_sum':
                    $processed_sort_by->sort_by('commissions_sum_in_fractions', $item->desc);
                    break;

                case 'unsettled_amount_sum':
                    $processed_sort_by->sort_by('unsettled_commissions_sum_in_fractions', $item->desc);
                    break;

                case 'status':
                    $processed_sort_by->sort_by('is_active', $item->desc);
                    break;
            }
        }

        return $processed_sort_by;
    }

    private function select_partners(string $query_type, int $per_page, int $page, Partner_Query_Criteria $criteria, ?Sort_By_Clause $sort_by)
    {
        $select_start = $query_type === self::QUERY_TYPE_COUNT ? 'SELECT COUNT(*) AS total_count from (SELECT' : 'SELECT';
        $select_end = $query_type === self::QUERY_TYPE_COUNT ? ') count_table_alias' : '';

        $limit = ($per_page > 0) ? $per_page : PHP_INT_MAX;
        $skip = !$limit ? 0 : ($per_page * ($page - 1));
        $limit_sql = "LIMIT {$skip}, {$limit}";

        $where = $this->parse_criteria_to_where_clause($criteria);
        $where_sql = $this->sql_helper->process_where_condition_to_sql($where);
        $having = $this->parse_criteria_to_having_clause($criteria);
        $having_sql = $this->sql_helper->process_having_condition_to_sql($having);

        $partners_table_name = $this->db->prepare_table_name(self::TABLE_NAME);
        $commissions_table_name = $this->db->prepare_table_name(Commission_Persistence::TABLE_NAME);
        $usermeta_table_name = $this->db->prepare_table_name(self::USERMETA_TABLE_NAME);
        $caps_meta_key = $this->db->prepare_table_name(self::CAPABILITIES_TABLE_NAME);
        $partner_role_name = Caps::ROLE_LMS_PARTNER;
        $status_unsettled = Status::STATUS_UNSETTLED;

        $order_by_sql = $this->sql_helper->process_order_by_clause(
            $this->fix_column_names_in_sort_by_clause($sort_by)
        );

        $status_is_active = self::STATUS_ACTIVE_SQL_RETURN_VALUE;
        $status_is_not_active = self::STATUS_INCACTIVE_SQL_RETURN_VALUE;

        return $this->db->execute(
            "${select_start}
                partners.id,
                partners.user_id,
                partners.affiliate_id,
                partners.first_name,
                partners.last_name,
                partners.email,
                partners.created_at,
                IFNULL(sum(commisions.sale_amount), 0) as sales_sum_in_fractions,
                IFNULL(sum(commisions.amount), 0) as commissions_sum_in_fractions,
                IFNULL(sum(CASE WHEN commisions.status = '{$status_unsettled}' THEN commisions.amount END), 0) as unsettled_commissions_sum_in_fractions,
                CASE WHEN usermeta.meta_value LIKE '%{$partner_role_name}%' THEN {$status_is_active} ELSE {$status_is_not_active} END is_active
            FROM ${partners_table_name} partners
            LEFT JOIN ${commissions_table_name} commisions ON partners.id = commisions.partner_id
            LEFT JOIN ${usermeta_table_name} usermeta ON partners.user_id = usermeta.user_id AND usermeta.meta_key = '{$caps_meta_key}'
            {$where_sql}
            GROUP BY partners.id, usermeta.meta_key, usermeta.meta_value
            {$having_sql}
            {$order_by_sql} {$limit_sql}
            {$select_end}
            "
        );
    }
}