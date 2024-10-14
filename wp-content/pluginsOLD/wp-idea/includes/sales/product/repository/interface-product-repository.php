<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\sales\product\repository;

use bpmj\wpidea\sales\product\model\Product;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\sales\product\Product_Collection;
use bpmj\wpidea\user\User_ID;

interface Interface_Product_Repository
{
    public function find(Product_ID $id): ?Product;

    public function find_all(): Product_Collection;

    public function find_by_criteria(Product_Query_Criteria $criteria): Product_Collection;

    public function save(Product $product): Product_ID;

    public function delete(Product_ID $id): void;

    public function get_meta(Product_ID $id, string $key);

    public function update_meta(Product_ID $id, string $key, $value): void;

	public function find_products_user_has_or_had_access_to(User_ID $user_id): Product_Collection;

    public function user_has_or_had_access_to_product(User_ID $user_id, Product_ID $product_id): bool;
}
