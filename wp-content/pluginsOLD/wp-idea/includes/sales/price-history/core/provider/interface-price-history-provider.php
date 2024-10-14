<?php

namespace bpmj\wpidea\sales\price_history\core\provider;

use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\sales\price_history\core\model\Historic_Price;
use bpmj\wpidea\sales\price_history\core\model\Lowest_Price;
use bpmj\wpidea\sales\price_history\core\model\Lowest_Price_Collection;
use bpmj\wpidea\sales\price_history\core\model\Price_History;
use bpmj\wpidea\sales\price_history\infrastructure\persistence\Price_History_Query_Criteria;

interface Interface_Price_History_Provider
{
    public function store_price_change(
        int $product_id,
        ?int $variant_id,
        float $old_price,
        float $new_price,
        ?float $old_promo_price,
        ?float $new_promo_price
    ): void;

    public function find_last_price_for_product(int $product_id, ?int $variant_id): ?Historic_Price;

    public function get_product_with_active_promotion_lowest_price(int $product_id, bool $get_variants): Lowest_Price_Collection;

    public function delete_by_product(int $product_id, ?int $variant_id): void;

    public function find_by_criteria(
        Price_History_Query_Criteria $criteria,
        int $per_page = 0,
        int $page = 1,
        ?Sort_By_Clause $sort_by = null
    ): Price_History;

    public function get_product_lowest_price(int $product_id, ?int $variant_id): ?Lowest_Price;
}