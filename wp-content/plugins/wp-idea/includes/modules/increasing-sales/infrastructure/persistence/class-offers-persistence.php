<?php

declare(strict_types=1);

namespace bpmj\wpidea\modules\increasing_sales\infrastructure\persistence;

use bpmj\wpidea\helpers\Price_Formatting;
use bpmj\wpidea\infrastructure\database\Interface_Database;
use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\modules\increasing_sales\core\entities\Offer;
use bpmj\wpidea\modules\increasing_sales\core\value_objects\{
    Offer_ID,
    Product_ID,
    Variant_ID,
    Offered_Product_ID,
    Offered_Variant_ID,
    Increasing_Sales_Offer_Type
};
use bpmj\wpidea\modules\increasing_sales\core\collections\Offer_Collection;

class Offers_Persistence implements Interface_Offers_Persistence
{
    public const TABLE_NAME = 'wpi_increasing_sales_offers';
    private const MAX_VAL = 4294967295;

    private Interface_Database $db;

    public function __construct(Interface_Database $db)
    {
        $this->db = $db;
    }

    public function setup(): void
    {
        $this->db->create_table_if_not_exists(
            self::TABLE_NAME,
            [
                'id int UNSIGNED NOT NULL AUTO_INCREMENT',
                'product_id bigint(20) UNSIGNED NOT NULL',
                'product_variant_id TINYINT UNSIGNED NULL',
                'offer_type varchar(50) NOT NULL',
                'offered_product_id bigint(20) UNSIGNED NOT NULL',
                'offered_product_variant_id TINYINT UNSIGNED NULL',
                'title varchar(255) NULL',
                'description text NULL',
                'image varchar(255) NULL',
                'discount int UNSIGNED NULL',
                'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
                'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
            ],
            'id',
        );
    }

    public function insert(Offer $offer): void
    {
        $product_variant_id = $offer->get_product_variant_id() ? $offer->get_product_variant_id()->to_int() : null;
        $offered_product_variant_id = $offer->get_offered_product_variant_id() ? $offer->get_offered_product_variant_id()->to_int() : null;

        $this->db->insert(self::TABLE_NAME, [
            'product_id' => $offer->get_product_id()->to_int(),
            'product_variant_id' => $product_variant_id,
            'offer_type' => $offer->get_offer_type()->get_value(),
            'offered_product_id' => $offer->get_offered_product_id()->to_int(),
            'offered_product_variant_id' => $offered_product_variant_id,
            'title' => $offer->get_title(),
            'description' => $offer->get_description(),
            'image' => $offer->get_image(),
            'discount' => $offer->get_discount_in_fractions(),
        ]);
    }

    public function update(Offer $offer): void
    {
        $product_variant_id = $offer->get_product_variant_id() ? $offer->get_product_variant_id()->to_int() : null;
        $offered_product_variant_id = $offer->get_offered_product_variant_id() ? $offer->get_offered_product_variant_id()->to_int() : null;

        $this->db->update_rows(self::TABLE_NAME, [
            ['product_id', $offer->get_product_id()->to_int()],
            ['product_variant_id', $product_variant_id],
            ['offer_type', $offer->get_offer_type()->get_value()],
            ['offered_product_id', $offer->get_offered_product_id()->to_int()],
            ['offered_product_variant_id', $offered_product_variant_id],
            ['title', $offer->get_title()],
            ['description', $offer->get_description()],
            ['image', $offer->get_image()],
            ['discount', $offer->get_discount_in_fractions()],
        ], [['id', '=', $offer->get_id()->to_int()]]);
    }

    public function delete(int $id): void
    {
        $where = $this->parse_criteria_to_where_clause(new Offer_Query_Criteria($id));

        $this->db->delete_rows(self::TABLE_NAME, $where);
    }

    public function count_by_criteria(Offer_Query_Criteria $criteria): int
    {
        $where = $this->parse_criteria_to_where_clause($criteria);

        return $this->db->count(self::TABLE_NAME, $where);
    }

    public function find_by_id(int $id): ?Offer
    {
        $criteria = new Offer_Query_Criteria($id);

        $where = $this->parse_criteria_to_where_clause($criteria);

        $row = $this->db->get_results(
            self::TABLE_NAME,
            $this->get_query_columns()
            , $where,
            1,
            0,
            null)[0];

        if (!$row) {
            return null;
        }

        return $this->row_to_model($row);
    }

    public function find_by_criteria(
        Offer_Query_Criteria $criteria,
        int $per_page = 0,
        int $page = 1,
        ?Sort_By_Clause $sort_by = null
    ): Offer_Collection {
        $is_paginated = $per_page > 0;
        $skip = !$is_paginated ? 0 : ($per_page * ($page - 1));
        $where = $this->parse_criteria_to_where_clause($criteria);

        $rows = $this->db->get_results(
            self::TABLE_NAME,
            $this->get_query_columns(),
            $where,
            $per_page,
            $skip,
            $sort_by
        );

        return Offer_Collection::create_from_array(
            $this->rows_to_models($rows)
        );
    }

    private function rows_to_models(array $rows): array
    {
        $models = [];

        foreach ($rows as $row) {
            $models[] = $this->row_to_model($row);
        }

        return $models;
    }

    private function row_to_model(array $row): Offer
    {
        return Offer::create(
            new Offer_ID((int)$row['id']),
            new Product_ID((int)$row['product_id']),
            !empty($row['product_variant_id']) ? new Variant_ID((int)($row['product_variant_id'])) : null,
            new Increasing_Sales_Offer_Type($row['offer_type']),
            new Offered_Product_ID((int)$row['offered_product_id']),
            !empty($row['offered_product_variant_id']) ? new Offered_Variant_ID((int)($row['offered_product_variant_id'])) : null,
            $row['title'],
            $row['description'],
            $row['image'],
            !empty($row['discount']) ? (int)($row['discount']) : null
        );
    }

    private function parse_criteria_to_where_clause(Offer_Query_Criteria $criteria): array
    {
        $where = [];

        if ($criteria->get_id()) {
            $where[] = ['id', '=', $criteria->get_id()];
        }

        if ($criteria->get_products()) {
            $where[] = ['product_id', 'IN', $criteria->get_products()];
        }

        if ($criteria->product_variant_id()) {
            $where[] = ['product_variant_id', '=', $criteria->product_variant_id()];
        }

        if ($criteria->get_offer_type()) {
            $where[] = ['offer_type', '=', $criteria->get_offer_type()];
        }

        if ($criteria->get_offered_products()) {
            $where[] = ['offered_product_id', 'IN', $criteria->get_offered_products()];
        }

        if ($criteria->get_discounts()) {
            $discounts = $criteria->get_discounts();
            $min = $this->amount_to_int((float)$discounts[0]) ?? 0;
            $max = $this->amount_to_int((float)$discounts[1]) ?? self::MAX_VAL;

            if ($min) {
                $where[] = ['discount', 'MIN', $min];
            }

            if ($max) {
                $where[] = ['discount', 'MAX', $max];
            }
        }

        return $where;
    }

    private function amount_to_int(float $amount): int
    {
        return Price_Formatting::round_and_format_to_int($amount, Price_Formatting::MULTIPLY_BY_100);
    }

    private function get_query_columns(): array
    {
        return [
            'id',
            'product_id',
            'product_variant_id',
            'offer_type',
            'offered_product_id',
            'offered_product_variant_id',
            'title',
            'description',
            'image',
            'discount'
        ];
    }
}