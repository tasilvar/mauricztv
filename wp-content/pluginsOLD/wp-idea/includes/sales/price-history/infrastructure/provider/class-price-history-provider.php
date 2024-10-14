<?php

namespace bpmj\wpidea\sales\price_history\infrastructure\provider;

use bpmj\wpidea\data_types\ID;
use bpmj\wpidea\helpers\Price_Formatting;
use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\sales\price_history\core\model\Historic_Price;
use bpmj\wpidea\sales\price_history\core\model\Lowest_Price;
use bpmj\wpidea\sales\price_history\core\model\Lowest_Price_Collection;
use bpmj\wpidea\sales\price_history\core\model\Price_History;
use bpmj\wpidea\sales\price_history\core\provider\Interface_Price_History_Provider;
use bpmj\wpidea\sales\price_history\infrastructure\persistence\Interface_Price_History_Persistence;
use bpmj\wpidea\sales\price_history\infrastructure\persistence\Price_History_Query_Criteria;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\sales\product\model\Product_Price;
use bpmj\wpidea\sales\product\model\Variant_ID;
use DateTime;

class Price_History_Provider implements Interface_Price_History_Provider
{

    private Interface_Price_History_Persistence $persistence;

    public function __construct(
        Interface_Price_History_Persistence $persistence
    ) {
        $this->persistence = $persistence;
    }

    public function store_price_change(
        int $product_id,
        ?int $variant_id,
        float $old_price,
        float $new_price,
        ?float $old_promo_price,
        ?float $new_promo_price
    ): void {
        $this->persistence->store_price_change(
            $product_id,
            $variant_id,
            $old_price,
            $new_price,
            $old_promo_price,
            $new_promo_price
        );
    }

    public function find_last_price_for_product(int $product_id, ?int $variant_id): ?Historic_Price
    {
        $price = $this->persistence->find_last_price_for_product($product_id, $variant_id);

        if (!$price) {
            return null;
        }

        $new_regular_price = !is_null($price['new_regular_price']) ? new Product_Price($this->amount_to_float((int)$price['new_regular_price'])) : null;
        $new_promo_price = !is_null($price['new_promo_price']) ? new Product_Price($this->amount_to_float((int)$price['new_promo_price'])) : null;
        return Historic_Price::create(
            new DateTime($price['changed_at']),
            $new_regular_price,
            $new_promo_price
        );
    }

    public function get_product_with_active_promotion_lowest_price(int $product_id, bool $get_variants): Lowest_Price_Collection
    {
        $lowest_prices = [];
        foreach ($this->persistence->get_product_with_active_promotion_lowest_price($product_id, $get_variants) as $row) {
            $product_id = new Product_ID($row['product_id']);
            $variant_id = !is_null($row['product_variant_id']) ? new Variant_ID($row['product_variant_id']) : null;
            $lowest_price = new Product_Price($row['lowest_price']);
            $lowest_prices[] = new Lowest_Price($product_id, $variant_id, $lowest_price);
        }

        return Lowest_Price_Collection::create_from_array($lowest_prices);
    }

    public function delete_by_product(int $product_id, ?int $variant_id): void
    {
        $this->persistence->delete_by_product($product_id, $variant_id);
    }

    public function find_by_criteria(
        Price_History_Query_Criteria $criteria,
        int $per_page = 0,
        int $page = 1,
        ?Sort_By_Clause $sort_by = null
    ): Price_History {
        $items = [];
        foreach ($this->persistence->find_by_criteria($criteria, $per_page, $page, $sort_by) as $row) {
            $items[] = Historic_Price::create(
                new DateTime($row['changed_at']),
                !is_null($row['new_regular_price']) ? new Product_Price($this->amount_to_float((int)$row['new_regular_price'])) : null,
                !is_null($row['new_promo_price']) ? new Product_Price($this->amount_to_float((int)$row['new_promo_price'])) : null,
                new ID($row['id']),
                new Product_ID($row['product_id']),
                !is_null($row['product_variant_id']) ? new Variant_ID($row['product_variant_id']) : null,
                !is_null($row['previous_regular_price']) ? new Product_Price($this->amount_to_float((int)$row['previous_regular_price'])) : null,
                !is_null($row['previous_promo_price']) ? new Product_Price($this->amount_to_float((int)$row['previous_promo_price'])) : null,
            );
        }

        return Price_History::create_from_array($items);
    }

    private function amount_to_float(int $amount): float
    {
        return Price_Formatting::format_to_float($amount, Price_Formatting::DIVIDE_BY_100);
    }

    public function get_product_lowest_price(int $product_id, ?int $variant_id): ?Lowest_Price
    {
        $product_lowest_price = $this->persistence->get_product_lowest_price($product_id, $variant_id);

        if (is_null($product_lowest_price)) {
            return null;
        }

        return new Lowest_Price(
            new Product_ID($product_id),
            !is_null($variant_id) ? new Variant_ID($variant_id) : null,
            new Product_Price($product_lowest_price)
        );
    }
}