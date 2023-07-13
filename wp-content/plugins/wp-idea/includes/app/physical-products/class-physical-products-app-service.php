<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\app\physical_products;

use bpmj\wpidea\data_types\ID;
use bpmj\wpidea\modules\cart\api\Cart_API;
use bpmj\wpidea\physical_product\dto\Physical_Product_DTO;
use bpmj\wpidea\physical_product\model\Bundle_With_Physical_Product;
use bpmj\wpidea\physical_product\model\Physical_Product;
use bpmj\wpidea\physical_product\model\Physical_Product_Collection;
use bpmj\wpidea\physical_product\model\Physical_Product_ID;
use bpmj\wpidea\physical_product\model\Physical_Product_Name;
use bpmj\wpidea\physical_product\repository\Interface_Physical_Product_Repository;
use bpmj\wpidea\physical_product\service\Interface_Physical_Product_Creator_Service;
use bpmj\wpidea\sales\product\core\services\Product_Events;
use bpmj\wpidea\sales\product\dto\Product_DTO;
use bpmj\wpidea\sales\product\model\Product;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\sales\product\Product_Collection;
use bpmj\wpidea\sales\product\repository\Interface_Product_Repository;
use bpmj\wpidea\sales\product\repository\Product_Query_Criteria;
use bpmj\wpidea\sales\product\service\Product_Creator_Service;

class Physical_Products_App_Service
{
    private Interface_Physical_Product_Repository $physical_product_repository;
    private Interface_Product_Repository $product_repository;
    private Product_Creator_Service $product_creator_service;
    private Interface_Physical_Product_Creator_Service $physical_product_creator_service;
    private Cart_API $cart_api;
    private Product_Events $product_events;

    public function __construct(
        Interface_Physical_Product_Repository $physical_product_repository,
        Interface_Product_Repository $product_repository,
        Product_Creator_Service $product_creator_service,
        Interface_Physical_Product_Creator_Service $physical_product_creator_service,
        Cart_API $cart_api,
        Product_Events $product_events
    ) {
        $this->physical_product_repository = $physical_product_repository;
        $this->product_repository = $product_repository;
        $this->product_creator_service = $product_creator_service;
        $this->physical_product_creator_service = $physical_product_creator_service;
        $this->cart_api = $cart_api;
        $this->product_events = $product_events;
    }

    public function save_physical_product(
        Product_DTO $product_dto,
        Physical_Product_DTO $physical_product_dto
    ): Product_ID {
        $resource_id = $this->physical_product_creator_service->save_physical_product($physical_product_dto);

        $product_dto->linked_resource_id = $resource_id->to_int();

        return $this->product_creator_service->save_product($product_dto);
    }

    public function find_all_offers(): Product_Collection
    {
        $offers = new Product_Collection();

        foreach ($this->physical_product_repository->find_all() as $physical_product) {
            $criteria = $this->get_query_criteria_for_digital_product_offers($physical_product->get_id());

            $offer = $this->product_repository->find_by_criteria(
                $criteria
            );

            if ($offer->is_empty()) {
                continue;
            }

            $offers->add($offer->current());
        }

        return $offers;
    }

    public function find_physical_product_by_offer_id(Physical_Product_ID $id): ?Physical_Product
    {
        $product = $this->product_repository->find(
            new Product_ID($id->to_int())
        );

        if (!$product || is_null($product->get_linked_resource_id())) {
            return null;
        }

        return $this->physical_product_repository->find(
            new Physical_Product_ID($product->get_linked_resource_id()->to_int())
        );
    }

    public function find_bundle_with_physical_product_by_product_id(Product_ID $id): ?Bundle_With_Physical_Product
    {
        $product = $this->product_repository->find(
            new Product_ID($id->to_int())
        );

        if (!$product) {
            return null;
        }

        if(!$product->is_bundle()) {
            return null;
        }

        if(!$this->bundle_contains_physical_product($product)) {
            return null;
        }

        return Bundle_With_Physical_Product::create(
            new Physical_Product_ID($id->to_int()),
            new Physical_Product_Name($product->get_name()->get_value())
        );
    }

    public function delete_product(Product_ID $product_id): void
    {
        $physical_product_name = $this->get_physical_product_name($product_id);

        $this->product_repository->delete($product_id);

        $this->product_events->emit_physical_product_deleted_event(
            $physical_product_name,
            $product_id->to_int()
        );
    }

    public function toggle_sales(Product_ID $id): void
    {
        $product = $this->product_repository->find($id);

        if (!$product) {
            return;
        }

        $sales_disabled = true;

        if ($product->sales_disabled()) {
            $sales_disabled = false;
        }

        $product->change_sales_disabled($sales_disabled);

        $this->product_repository->save($product);

        $this->product_events->emit_physical_product_field_toggle_sales_updated_event(
            $product->get_name()->get_value(),
            $sales_disabled ? 'on' : 'off',
            $id->to_int()
        );
    }

    public function is_physical_product_in_the_cart(): bool
    {
        return (bool)iterator_count($this->get_physical_products_in_the_cart());
    }

    public function get_physical_products_in_the_cart(): Physical_Product_Collection
    {
        $collection = new Physical_Product_Collection();

        foreach ($this->cart_api->get_cart_content() as $index => $cart_content) {
            $item_product_id = $cart_content->get_item_product_id() ? $cart_content->get_item_product_id()->to_int() : null;
            if (!$item_product_id) {
                continue;
            }

            $physical_product = $this->find_physical_product_by_offer_id(new Physical_Product_ID($item_product_id))
                ?? $this->find_bundle_with_physical_product_by_product_id(new Product_ID($item_product_id));

            if (!$physical_product) {
                continue;
            }

            $collection->add($physical_product);
        }

        return $collection;
    }

    private function get_query_criteria_for_digital_product_offers(Physical_Product_ID $physical_product_id
    ): Product_Query_Criteria {
        $resource_id = new ID($physical_product_id->to_int());
        $criteria = new Product_Query_Criteria();

        $criteria->set_linked_resource_id(
            $resource_id
        );

        return $criteria;
    }

    private function bundle_contains_physical_product(Product $bundle): bool
    {
        foreach ($bundle->get_bundled_products() as $bundled_product_id) {
            $bundled_product = $this->find_physical_product_by_offer_id(new Physical_Product_ID((int)$bundled_product_id));

            if(!$bundled_product) {
                continue;
            }

            return true;
        }

        return false;
    }

    private function get_physical_product_name(Product_ID $id): string
    {
        $product = $this->product_repository->find($id);

        return $product ? $product->get_name()->get_value() : '';
    }
}