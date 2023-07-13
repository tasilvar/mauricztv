<?php

namespace bpmj\wpidea\sales\price_history\infrastructure\persistence;

use bpmj\wpidea\helpers\Price_Formatting;
use bpmj\wpidea\infrastructure\database\Interface_Database;
use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\sales\price_history\core\model\Historic_Price;
use bpmj\wpidea\sales\product\legal\Information_About_Lowest_Price;
use bpmj\wpidea\sales\product\service\Variant_IDs_Parser;

class Price_History_Persistence implements Interface_Price_History_Persistence
{
    public const TABLE_NAME = 'wpi_price_history';

    private Interface_Database $db;
    private Variant_IDs_Parser $variant_ids_parser;

    public function __construct(
        Interface_Database $db,
        Variant_IDs_Parser $variant_ids_parser
    ) {
        $this->db = $db;
        $this->variant_ids_parser = $variant_ids_parser;
    }

    public function setup(): void
    {
        $this->db->create_table_if_not_exists(
            self::TABLE_NAME,
            [
                'id int UNSIGNED NOT NULL AUTO_INCREMENT',
                'product_id bigint(20) UNSIGNED NOT NULL',
                'product_variant_id TINYINT UNSIGNED NULL',
                'previous_regular_price int UNSIGNED NOT NULL',
                'new_regular_price int UNSIGNED NOT NULL',
                'previous_promo_price int UNSIGNED NULL',
                'new_promo_price int UNSIGNED NULL',
                'changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            ],
            'id',
        );
    }

    public function store_price_change(
        int $product_id,
        ?int $variant_id,
        float $old_price,
        float $new_price,
        ?float $old_promo_price,
        ?float $new_promo_price
    ): void {
        $this->db->insert(self::TABLE_NAME, [
            'product_id' => $product_id,
            'product_variant_id' => $variant_id,
            'previous_regular_price' => $this->amount_to_int($old_price),
            'new_regular_price' => $this->amount_to_int($new_price),
            'previous_promo_price' => $old_promo_price !== null ? $this->amount_to_int($old_promo_price) : null,
            'new_promo_price' => $new_promo_price !== null ? $this->amount_to_int($new_promo_price) : null
        ]);
    }

    public function find_last_price_for_product(int $product_id, ?int $variant_id): ?array
    {
        $limit = 1;
        $skip = 0;
        $sort_by = new Sort_By_Clause();
        $sort_by->sort_by('changed_at', true);

        $result = $this->db->get_results(self::TABLE_NAME, [
            'id',
            'product_id',
            'product_variant_id',
            'previous_regular_price',
            'new_regular_price',
            'previous_promo_price',
            'new_promo_price',
            'changed_at'
        ], [
            ['product_id', '=', $product_id],
            ['product_variant_id', $variant_id ? '=' : 'IS', $variant_id ?: 'NULL'],
        ], $limit, $skip, $sort_by);

        return $result[0] ?? null;
    }

    public function get_product_with_active_promotion_lowest_price(int $product_id, bool $get_variants): array
    {
        $get_variants_sql = $get_variants ? 'is not null' : 'is null';
        $date_range = Information_About_Lowest_Price::MAX_DATE_DIFFERENCE;
        $table_name = $this->db->prepare_table_name(self::TABLE_NAME);

        $sql = "
                select ph.product_id,
                       ph.product_variant_id,
                       min(least(ph.previous_regular_price,
                                 ph.new_regular_price,
                                 coalesce(NULLIF(ph.previous_promo_price, 0), ph.previous_regular_price),
                                 coalesce(NULLIF(IF(active_promotions.promo_start_date != ph.changed_at, ph.new_promo_price, null), 0), ph.new_regular_price)))
                           as lowest_price
                from {$table_name} ph
                         inner join (select ph.product_id,
                                              ph.product_variant_id,
                                              last_promo_change_date.promo_change_date as promo_start_date
                                       from {$table_name} ph
                                                inner join (select product_id, product_variant_id, max(changed_at) as promo_change_date
                                                            from {$table_name}
                                                            where product_id = {$product_id} 
                                                                and product_variant_id {$get_variants_sql}
                                                            group by product_id, product_variant_id) last_promo_change_date 
                                                    on last_promo_change_date.product_id = ph.product_id 
                                                            and IFNULL(last_promo_change_date.product_variant_id, 'no_variant') = IFNULL(ph.product_variant_id, 'no_variant') 
                                                            and  ph.changed_at = last_promo_change_date.promo_change_date
                                       where coalesce(new_promo_price, 0) > 0) active_promotions 
                                    on ph.product_id = active_promotions.product_id 
                                    and IFNULL(ph.product_variant_id, 'no_variant') = IFNULL(active_promotions.product_variant_id, 'no_variant')
                where ph.product_id = {$product_id} 
                    and ph.product_variant_id {$get_variants_sql} 
                    and ph.changed_at >= (date(active_promotions.promo_start_date) - interval {$date_range} day)
                group by ph.product_id, ph.product_variant_id
                ";

        return $this->db->execute($sql);
    }

    public function delete_by_product(int $product_id, ?int $variant_id): void
    {
        $where = [];

        if ($variant_id) {
            $where[] = ['product_variant_id', '=', $variant_id];
        }

        $where[] = ['product_id', '=', $product_id];

        $this->db->delete_rows(self::TABLE_NAME, $where);

    }

    public function find_by_criteria(
        Price_History_Query_Criteria $criteria,
        int $per_page = 0,
        int $page = 1,
        ?Sort_By_Clause $sort_by = null
    ): array
    {
        $is_paginated = $per_page > 0;
        $skip = !$is_paginated ? 0 : ($per_page * ($page - 1));
        $where = $this->parse_criteria_to_where_clause($criteria);

        return $this->db->get_results(
            self::TABLE_NAME,
            $this->get_query_columns(),
            $where,
            $per_page,
            $skip,
            $sort_by
        );
    }

    private function parse_criteria_to_where_clause(Price_History_Query_Criteria $criteria): array
    {
        $where = [];

        if (!empty($criteria->get_product_ids())) {
            $where[] = ['product_id', 'IN', $criteria->get_product_ids()];
        }

        if ($criteria->get_price()) {
            $price = $criteria->get_price();
            $min = $this->amount_to_int((float)$price[0]);
            $max = $this->amount_to_int((float)$price[1]);

            $price_where_condition = 'case when previous_regular_price != new_regular_price then new_regular_price else coalesce(new_promo_price, 0) end';
            if ($min) {
                $where[] = [$price_where_condition, '>=', $min];
            }

            if ($max) {
                $where[] = [$price_where_condition, '<=', $max];
            }
        }

        if (!empty($criteria->get_date_of_change())) {
            $date_range = $criteria->get_date_of_change();
            $startDate = $date_range['startDate'];
            $endDate = $date_range['endDate'];

            if (!empty($startDate)) {
                $where[] = ['changed_at', '>=', $startDate];
            }

            if (!empty($endDate)) {
                $where[] = ['changed_at', '<=', $endDate];
            }
        }

        if (count($criteria->get_type_of_price() ?? []) === 1) {
            $type_of_price = $criteria->get_type_of_price();
            if (in_array('regular', $type_of_price)) {
                $where[] = ['previous_regular_price', '!=', 'new_regular_price'];
            } else {
                $where[] = ['coalesce(previous_promo_price, 0)', '!=', 'coalesce(new_promo_price, 0)'];
            }
        }

        return $where;
    }

    private function get_query_columns(): array
    {
        return [
            'id',
            'product_id',
            'product_variant_id',
            'previous_regular_price',
            'new_regular_price',
            'previous_promo_price',
            'new_promo_price',
            'changed_at',
        ];
    }

    private function amount_to_int(float $amount): int
    {
        return Price_Formatting::round_and_format_to_int($amount, Price_Formatting::MULTIPLY_BY_100);
    }

    public function count_by_criteria(Price_History_Query_Criteria $criteria): int
    {
        return $this->db->count(self::TABLE_NAME, $this->parse_criteria_to_where_clause($criteria));
    }

    public function get_product_lowest_price(int $product_id, ?int $variant_id): ?float
    {
        $table_name = $this->db->prepare_table_name(self::TABLE_NAME);
        $variant_sql = is_null($variant_id) ? 'is null' : '= ' . $variant_id;
        $date_range = Information_About_Lowest_Price::MAX_DATE_DIFFERENCE;

        $sql = "
                 select min(least(ph.previous_regular_price,
                         ph.new_regular_price,
                         coalesce(NULLIF(ph.previous_promo_price, 0), ph.previous_regular_price),
                         coalesce(NULLIF(ph.new_promo_price, 0), ph.new_regular_price)
                        )) as lowest_price
                from {$table_name} ph
                where ph.product_id = {$product_id} 
                    and ph.product_variant_id {$variant_sql} 
                    and ph.changed_at >= (date(current_timestamp) - interval {$date_range} day)
                group by ph.product_id, ph.product_variant_id
                ";

        $result = $this->db->execute($sql);

        return $result[0]['lowest_price'] ?? null;
    }
}