<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\sales\product\api;

use bpmj\wpidea\courses\core\dto\Price_Variants_DTO;
use bpmj\wpidea\courses\core\dto\Purchase_Limit_DTO;
use bpmj\wpidea\data_types\exceptions\Object_Uninitialized_Exception;
use bpmj\wpidea\modules\increasing_sales\core\value_objects\Offered_Product_ID;
use bpmj\wpidea\modules\increasing_sales\core\value_objects\Offered_Variant_ID;
use bpmj\wpidea\resources\Resource_Type;
use bpmj\wpidea\sales\product\api\dto\Product_Collection_To_DTO_Collection_Mapper;
use bpmj\wpidea\sales\product\api\dto\Product_DTO;
use bpmj\wpidea\sales\product\api\dto\Product_DTO_Collection;
use bpmj\wpidea\sales\product\api\dto\Product_To_DTO_Mapper;
use bpmj\wpidea\sales\product\model\Product;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\sales\product\providers\Interface_Product_Config_Provider;
use bpmj\wpidea\sales\product\repository\Interface_Product_Repository;
use bpmj\wpidea\user\User_ID;

class Product_API implements Interface_Product_API
{
    private const VARIABLE_PRICING_META_KEY = '_variable_pricing';
    private const VARIABLE_PRICES_META_KEY = 'edd_variable_prices';
    private const PURCHASE_LIMIT_META_KEY = '_bpmj_eddcm_purchase_limit';
    private const PURCHASE_LIMIT_ITEMS_LEFT_META_KEY = '_bpmj_eddcm_purchase_limit_items_left';
    private const PURCHASE_LIMIT_UNLIMITED_META_KEY = '_bpmj_eddcm_purchase_limit_unlimited';
    private const RESOURCE_TYPE_META_NAME = 'wpi_resource_type';
    private const PRODUCT_TYPE_META_NAME = '_edd_product_type';

    private static ?Product_API $instance = null;
    private Interface_Product_Repository $product_repository;
    private Product_Collection_To_DTO_Collection_Mapper $product_collection_to_DTO_collection_mapper;
    private Api_Search_Criteria_To_Product_Query_Criteria_Mapper $criteria_to_product_query_criteria_mapper;
    private Product_To_DTO_Mapper $product_to_DTO_mapper;
    private Interface_Product_Config_Provider $product_config_provider;


    public function __construct(
        Interface_Product_Repository $product_repository,
        Product_Collection_To_DTO_Collection_Mapper $product_collection_to_DTO_collection_mapper,
        Api_Search_Criteria_To_Product_Query_Criteria_Mapper $criteria_to_product_query_criteria_mapper,
        Product_To_DTO_Mapper $product_to_DTO_mapper,
        Interface_Product_Config_Provider $product_config_provider
    ) {
        $this->product_repository = $product_repository;
        $this->product_collection_to_DTO_collection_mapper = $product_collection_to_DTO_collection_mapper;
        $this->criteria_to_product_query_criteria_mapper = $criteria_to_product_query_criteria_mapper;
        $this->product_to_DTO_mapper = $product_to_DTO_mapper;
        $this->product_config_provider = $product_config_provider;

        self::$instance = $this;
    }

    /**
     * @throws Object_Uninitialized_Exception
     */

    public static function get_instance(): Product_API
    {
        if (!isset(self::$instance)) {
            throw new Object_Uninitialized_Exception();
        }

        return self::$instance;
    }


    public function find_all(): Product_DTO_Collection
    {
        $found_products = $this->product_repository->find_all();

        return $this->product_collection_to_DTO_collection_mapper->map($found_products);
    }

    public function find_by_criteria(Product_API_Search_Criteria $search_criteria): Product_DTO_Collection
    {
        $query_criteria = $this->criteria_to_product_query_criteria_mapper->map($search_criteria);

        $found_products = $this->product_repository->find_by_criteria($query_criteria);

        return $this->product_collection_to_DTO_collection_mapper->map($found_products);
    }

    public function find(int $product_id): ?Product_DTO
    {
        $id = new Product_ID($product_id);
        $product = $this->product_repository->find($id);

        if (!$product) {
            return null;
        }

        return $this->product_to_DTO_mapper->map($product);
    }

    public function check_if_user_has_access_to_course_product(int $product_id, int $user_id): bool
    {
        $course = WPI()->courses->get_course_by_product($product_id);
        if (!$course) {
            return false;
        }

        $course_id = get_post_meta($course->ID, 'course_id', true);
        $restricted_to = [['download' => $product_id]];

        $access = bpmj_eddpc_user_can_access($user_id, $restricted_to, $course_id);

        if ('valid' === $access['status']) {
            return true;
        }

        return false;
    }

    public function offered_product_is_disabled_or_sold_out(
        Offered_Product_ID $offered_product_id,
        ?Offered_Variant_ID $offered_product_variant_id
    ): bool {
        $product = $this->product_repository->find(new Product_ID($offered_product_id->to_int()));

        if (!$product) {
            return false;
        }

        if ($product->sales_disabled() || $this->product_is_sold_out($product, $offered_product_variant_id)) {
            return true;
        }

        return false;
    }

    public function get_price_variants(int $id): Price_Variants_DTO
    {
        $product_id = new Product_ID($id);

        $has_pricing_variants = $this->product_repository->get_meta($product_id, self::VARIABLE_PRICING_META_KEY);
        $purchase_limit_items_left = $this->product_repository->get_meta($product_id, self::VARIABLE_PRICES_META_KEY);

        $price_variants_DTO = new Price_Variants_DTO();
        $price_variants_DTO->has_pricing_variants = !empty($has_pricing_variants) ? true : false;
        $price_variants_DTO->variable_prices = $purchase_limit_items_left ?? [];

        return $price_variants_DTO;
    }

    public function get_purchase_limit(int $id): Purchase_Limit_DTO
    {
        $product_id = new Product_ID($id);

        $purchase_limit = $this->product_repository->get_meta($product_id, self::PURCHASE_LIMIT_META_KEY);
        $purchase_limit_items_left = $this->product_repository->get_meta($product_id, self::PURCHASE_LIMIT_ITEMS_LEFT_META_KEY);

        $purchase_limit_DTO = new Purchase_Limit_DTO();
        $purchase_limit_DTO->limit = $purchase_limit ? (int)$purchase_limit : null;
        $purchase_limit_DTO->items_left = $purchase_limit_items_left ? (int)$purchase_limit_items_left : null;

        return $purchase_limit_DTO;
    }

    public function update_purchase_limit(int $id, Purchase_Limit_DTO $purchase_limit_DTO): void
    {
        $product_id = new Product_ID($id);

        $limit = !is_null($purchase_limit_DTO->limit) ? $purchase_limit_DTO->limit : '';
        $items_left = !is_null($purchase_limit_DTO->items_left) ? $purchase_limit_DTO->items_left : '';
        $unlimited = $purchase_limit_DTO->unlimited ? '1' : '';

        $this->product_repository->update_meta($product_id, self::PURCHASE_LIMIT_META_KEY, $limit);
        $this->product_repository->update_meta($product_id, self::PURCHASE_LIMIT_ITEMS_LEFT_META_KEY, $items_left);
        $this->product_repository->update_meta($product_id, self::PURCHASE_LIMIT_UNLIMITED_META_KEY, $unlimited);
    }

    private function product_is_sold_out(?Product $product, ?Offered_Variant_ID $offered_variant_id): bool
    {
        if (!$product) {
            return false;
        }

        if (!$offered_variant_id) {
            return $this->is_active_purchase_limit($product->get_purchase_limit(), $product->get_purchase_limit_items_left());
        }

        if (!$product->get_product_variants()) {
            return false;
        }

        foreach ($product->get_product_variants() as $variant) {
            if ($offered_variant_id->to_int() === $variant->get_id()->to_int()) {
                return $this->is_active_purchase_limit($variant->get_purchase_limit(), $variant->get_purchase_limit_items_left());
            }
        }

        return false;
    }

    private function is_active_purchase_limit(?int $purchase_limit, ?int $purchase_limit_items_left): bool
    {
        return ((int)$purchase_limit > 0 && (int)$purchase_limit_items_left <= 0);
    }

    public function get_products_user_has_or_had_access_to(User_ID $user_id): Product_DTO_Collection
    {
        $products = $this->product_repository->find_products_user_has_or_had_access_to($user_id);

        return $this->product_collection_to_DTO_collection_mapper->map($products);
    }

    public function user_has_or_had_access_to_product(int $user_id, int $product_id): bool
    {
        return $this->product_repository->user_has_or_had_access_to_product(new User_ID($user_id), new Product_ID($product_id));
    }

    public function has_access_to_custom_purchase_links(): bool
    {
        return $this->product_config_provider->has_access_to_custom_purchase_links();
    }

    public function get_resource_type_for_product_id(?int $product_id): ?Resource_Type
    {
        if (!$product_id) {
            return null;
        }

        $product_type_meta = $this->product_repository->get_meta(new Product_ID($product_id), self::PRODUCT_TYPE_META_NAME);
        $product_type_meta = $product_type_meta === Resource_Type::BUNDLE ? Resource_Type::BUNDLE : null;

        $resource_type_meta = $this->product_repository->get_meta(new Product_ID($product_id), self::RESOURCE_TYPE_META_NAME);

        $type = $product_type_meta ?: $resource_type_meta;

        return new Resource_Type($type ?: Resource_Type::COURSE);
    }
}