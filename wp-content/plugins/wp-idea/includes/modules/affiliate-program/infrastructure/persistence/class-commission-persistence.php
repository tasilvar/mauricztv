<?php

declare(strict_types=1);

namespace bpmj\wpidea\modules\affiliate_program\infrastructure\persistence;

use bpmj\wpidea\helpers\Price_Formatting;
use bpmj\wpidea\infrastructure\database\Interface_Database;
use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\modules\affiliate_program\core\entities\Commission;
use bpmj\wpidea\modules\affiliate_program\core\value_objects\Status;

class Commission_Persistence implements Interface_Commission_Persistence
{
    public const TABLE_NAME = 'wpi_affiliate_program_commissions';
    private const MAX_VAL = 4294967295;

    private Interface_Database $db;

    public function __construct(Interface_Database $db)
    {
        $this->db = $db;
    }

    public function insert(Commission $commission): void
    {
        $this->db->insert(self::TABLE_NAME, [
            'partner_id' => $commission->get_partner_id()->to_int(),
            'partner_affiliate_id' => $commission->get_partner_affiliate_id(),
            'partner_email' => $commission->get_partner_email(),
            'name' => $commission->get_client_name(),
            'email' => $commission->get_client_email(),
            'products' => json_encode($commission->get_purchased_product_ids()),
            'sale_amount' => $commission->get_sale_amount_in_fractions(),
            'percentage' => $commission->get_commission_percentage(),
            'amount' => $commission->get_commission_amount_in_fractions(),
            'created_at' => $commission->get_date()->format('Y-m-d H:i:s'),
            'status' => Status::STATUS_UNSETTLED,
            'campaign' => $commission->get_campaign()
        ]);
    }

    public function add_campaign_column_in_table(): void
    {
        $column_name = 'campaign';
        $type = 'varchar(50) NULL';

        $this->db->add_column_in_table(self::TABLE_NAME, $column_name, $type);
    }

    public function setup(): void
    {
        global $wpdb;

        $partners_table_name = $this->db->get_wp_table_name(Partner_Persistence::TABLE_NAME);

        $this->db->create_table_if_not_exists(
            self::TABLE_NAME,
            [
                'id int UNSIGNED NOT NULL AUTO_INCREMENT',
                'partner_id int UNSIGNED NOT NULL',
                'partner_affiliate_id varchar(50) NOT NULL',
                'partner_email varchar(255) NOT NULL',
                'name varchar(255) NOT NULL',
                'email varchar(255) NOT NULL',
                'products longtext NOT NULL',
                'sale_amount bigint UNSIGNED NOT NULL',
                'percentage tinyint UNSIGNED NOT NULL',
                'amount int UNSIGNED NOT NULL',
                'created_at datetime NOT NULL',
                'status varchar(50) NOT NULL',
                'campaign varchar(50) NULL'
            ],
            'id',
            [
                "FOREIGN KEY (partner_id) REFERENCES $partners_table_name(id)",
                "KEY (partner_affiliate_id)",
                "KEY (partner_email)"
            ]
        );
    }

    public function count_by_criteria(Commission_Query_Criteria $criteria): int
    {
        $where = $this->parse_criteria_to_where_clause($criteria);

        return $this->db->count(self::TABLE_NAME, $where);
    }

    public function find_by_id(int $id): array
    {
        $criteria = new Commission_Query_Criteria();
        $criteria->set_id($id);

        $where = $this->parse_criteria_to_where_clause($criteria);

        return $this->db->get_results(self::TABLE_NAME, [
            'id',
            'partner_id',
            'partner_affiliate_id',
            'partner_email',
            'name',
            'email',
            'products',
            'sale_amount',
            'percentage',
            'amount',
            'created_at',
            'status',
            'campaign'
        ], $where, 1, 0, null)[0];
    }

    public function find_by_criteria(
        Commission_Query_Criteria $criteria,
        int $per_page = 0,
        int $page = 1,
        ?Sort_By_Clause $sort_by = null
    ): array {
        $limit = ($per_page > 0) ? $per_page : 0;
        $skip = !$limit ? 0 : ($per_page * ($page - 1));
        $where = $this->parse_criteria_to_where_clause($criteria);

        return $this->db->get_results(self::TABLE_NAME, [
            'id',
            'partner_id',
            'partner_affiliate_id',
            'partner_email',
            'name',
            'email',
            'products',
            'sale_amount',
            'percentage',
            'amount',
            'created_at',
            'status',
            'campaign'
        ], $where, $limit, $skip, $sort_by);
    }

    public function sum_sale_amount_by_criteria(
        Commission_Query_Criteria $criteria,
        int $per_page = 0,
        int $page = 1,
        ?Sort_By_Clause $sort_by = null
    ): int {
        $limit = ($per_page > 0) ? $per_page : 0;
        $skip = !$limit ? 0 : ($per_page * ($page - 1));
        $where = $this->parse_criteria_to_where_clause($criteria);

        return $this->db->get_sum(self::TABLE_NAME, 'sale_amount', $where, $limit, $skip, $sort_by);
    }

    public function sum_commission_amount_by_criteria(
        Commission_Query_Criteria $criteria,
        int $per_page = 0,
        int $page = 1,
        ?Sort_By_Clause $sort_by = null
    ): int {
        $limit = ($per_page > 0) ? $per_page : 0;
        $skip = !$limit ? 0 : ($per_page * ($page - 1));
        $where = $this->parse_criteria_to_where_clause($criteria);

        return $this->db->get_sum(self::TABLE_NAME, 'amount', $where, $limit, $skip, $sort_by);
    }

    public function delete(int $id): void
    {
        $criteria = new Commission_Query_Criteria();
        $criteria->set_id($id);

        $where = $this->parse_criteria_to_where_clause($criteria);

        $this->db->delete_rows(self::TABLE_NAME, $where);
    }

    public function update(Commission $commission): void
    {
        $set = [
            ['status', $commission->get_status()->get_value()]
        ];

        $criteria = new Commission_Query_Criteria();
        $criteria->set_id($commission->get_id()->to_int());

        $where = $this->parse_criteria_to_where_clause($criteria);

        $this->db->update_rows(self::TABLE_NAME, $set, $where);
    }

    public function get_summary(
        int $per_page = 0,
        int $page = 1,
        ?Sort_By_Clause $sort_by = null,
        ?array $filters = null
    ): array {
        $limit = ($per_page > 0) ? $per_page : 0;
        $offset = !$limit ? 0 : ($per_page * ($page - 1));

        return $this->get_sums($limit, $offset, $sort_by, $filters);
    }

    private function parse_criteria_to_where_clause(Commission_Query_Criteria $criteria): array
    {
        $where = [];

        if ($criteria->get_id()) {
            $where[] = ['id', '=', $criteria->get_id()];
        }

        if ($criteria->get_partner_id()) {
            $where[] = ['partner_id', 'LIKE', $criteria->get_partner_id()];
        }

        if ($criteria->get_partner_email()) {
            $where[] = ['partner_email', 'LIKE', $criteria->get_partner_email()];
        }

        if ($criteria->get_name()) {
            $where[] = ['name', 'LIKE', $criteria->get_name()];
        }

        if ($criteria->get_email()) {
            $where[] = ['email', 'LIKE', $criteria->get_email()];
        }

        if ($criteria->get_date()) {
            $date = $criteria->get_date();
            $startDate = $date['startDate'];
            $endDate = $date['endDate'];

            if ($startDate) {
                $where[] = ['created_at', '>=', $startDate];
            }

            if ($endDate) {
                $where[] = ['created_at', '<=', $endDate];
            }
        }

        if ($criteria->get_products()) {
            $where[] = ['products', 'IN', $criteria->get_products()];
        }

        if ($criteria->get_sale_amount()) {
            $sale_amount = $criteria->get_sale_amount();
            $min = $this->amount_to_int((float)$sale_amount[0]) ?? 0;
            $max = $this->amount_to_int((float)$sale_amount[1]) ?? self::MAX_VAL;

            if ($min) {
                $where[] = ['sale_amount', 'MIN', $min];
            }

            if ($max) {
                $where[] = ['sale_amount', 'MAX', $max];
            }
        }

        if ($criteria->get_commission_percentage()) {
            $commission_percentage = $criteria->get_commission_percentage();
            $min = $commission_percentage[0] ?? 0;
            $max = $commission_percentage[1] ?? self::MAX_VAL;

            if ($min) {
                $where[] = ['percentage', 'MIN', $min];
            }

            if ($max) {
                $where[] = ['percentage', 'MAX', $max];
            }
        }

        if ($criteria->get_commission_amount()) {
            $commission_amount = $criteria->get_commission_amount();
            $min = $this->amount_to_int((float)$commission_amount[0]) ?? 0;
            $max = $this->amount_to_int((float)$commission_amount[1]) ?? self::MAX_VAL;

            if ($min) {
                $where[] = ['amount', 'MIN', $min];
            }

            if ($max) {
                $where[] = ['amount', 'MAX', $max];
            }
        }


        if ($criteria->get_status()) {
            $where[] = ['status', '=', $criteria->get_status()];
        }

        return $where;
    }

    private function amount_to_int(float $amount): int
    {
        return Price_Formatting::round_and_format_to_int($amount, Price_Formatting::MULTIPLY_BY_100);
    }

    private function get_sums(int $limit, $offset, ?Sort_By_Clause $sort_by = null, ?array $filters = null): array
    {
        return $this->db->get_sum(
            self::TABLE_NAME,
            ['sale_amount', 'amount'],
            $this->parse_filters_to_where_clause($filters),
            $limit,
            $offset,
            $sort_by,
            'partner_id',
            [
                'partner_email',
                'name',
                "sum(if(status = '" . Status::STATUS_UNSETTLED . "',amount,0)) as unsettled_amount_sum"
            ]
        );
    }

    private function parse_filters_to_where_clause(array $filters): array
    {
        $results = [];
        foreach ($filters as $filter) {
            if (in_array($filter['id'], ['email', 'name'])) {
                $results[] = [
                    $filter['id'],
                    'LIKE',
                    "%" . $filter['value'] . "%"
                ];
            }
        }
        return $results;
    }
}
