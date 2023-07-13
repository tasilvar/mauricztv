<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\sales\product\api;

use bpmj\wpidea\courses\core\dto\{Price_Variants_DTO, Purchase_Limit_DTO};
use bpmj\wpidea\modules\increasing_sales\core\value_objects\Offered_Product_ID;
use bpmj\wpidea\modules\increasing_sales\core\value_objects\Offered_Variant_ID;
use bpmj\wpidea\sales\product\api\dto\Product_DTO;
use bpmj\wpidea\sales\product\api\dto\Product_DTO_Collection;
use bpmj\wpidea\user\User_ID;
use bpmj\wpidea\resources\Resource_Type;

interface Interface_Product_API
{
    public function find_all(): Product_DTO_Collection;

    public function find_by_criteria(Product_API_Search_Criteria $search_criteria): Product_DTO_Collection;

    public function find(int $product_id): ?Product_DTO;

    public function check_if_user_has_access_to_course_product(int $product_id, int $user_id): bool;

    public function offered_product_is_disabled_or_sold_out(
        Offered_Product_ID $offered_product_id,
        ?Offered_Variant_ID $offered_product_variant_id
    ): bool;

    public function get_price_variants(int $id): Price_Variants_DTO;

    public function get_purchase_limit(int $id): Purchase_Limit_DTO;

    public function update_purchase_limit(int $id, Purchase_Limit_DTO $purchase_limit_DTO): void;

	public function get_products_user_has_or_had_access_to(User_ID $user_id): Product_DTO_Collection;

    public function user_has_or_had_access_to_product(int $user_id, int $product_id): bool;

    public function get_resource_type_for_product_id(?int $product_id): ?Resource_Type;
}