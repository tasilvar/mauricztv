<?php

declare(strict_types=1);

namespace bpmj\wpidea\app\bundles;

use bpmj\wpidea\admin\Edit_Course;
use bpmj\wpidea\digital_products\model\Digital_Product_ID;
use bpmj\wpidea\digital_products\repository\Interface_Digital_Product_Repository;
use bpmj\wpidea\resources\Resource_Type;
use bpmj\wpidea\sales\product\core\services\Product_Events;
use bpmj\wpidea\sales\product\dto\Product_DTO;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\sales\product\Product_Collection;
use bpmj\wpidea\sales\product\repository\Interface_Product_Repository;
use bpmj\wpidea\sales\product\repository\Product_Query_Criteria;
use bpmj\wpidea\sales\product\service\Product_Creator_Service;
use bpmj\wpidea\service\model\Service_ID;
use bpmj\wpidea\service\repository\Interface_Service_Repository;

class Bundles_App_Service
{
    private Product_Creator_Service $product_creator_service;
    private Interface_Product_Repository $product_repository;
    private Interface_Digital_Product_Repository $digital_product_repository;
    private Interface_Service_Repository $service_repository;
    private Product_Events $product_events;

    public function __construct(
        Product_Creator_Service $product_creator_service,
        Interface_Product_Repository $product_repository,
        Interface_Digital_Product_Repository $digital_product_repository,
        Interface_Service_Repository $service_repository,
        Product_Events $product_events
    ) {
        $this->product_creator_service = $product_creator_service;
        $this->product_repository = $product_repository;
        $this->digital_product_repository = $digital_product_repository;
        $this->service_repository = $service_repository;
        $this->product_events = $product_events;
    }

    public function save_bundle(Product_DTO $product_dto): Product_ID
    {
        return $this->product_creator_service->save_product($product_dto);
    }

    public function find_all_offers(): Product_Collection
    {
        $criteria = new Product_Query_Criteria();
        $criteria->set_is_bundle(true);

        return $this->product_repository->find_by_criteria($criteria);
    }

    public function get_bundle_items(Product_ID $bundle_id): Bundle_Item_Display_Model_Collection
    {
        $criteria = new Product_Query_Criteria();

        $criteria->set_is_bundle(false);
        $criteria->set_in_bundle($bundle_id);

        return $this->get_bundle_item_collection_from_products(
            $this->product_repository->find_by_criteria($criteria)
        );
    }

    public function get_all_bundlable_items(): Bundle_Item_Display_Model_Collection
    {
        $criteria = new Product_Query_Criteria();

        $criteria->set_is_bundle(false);

        return $this->get_bundle_item_collection_from_products(
            $this->product_repository->find_by_criteria($criteria)
        );
    }

    public function product_belongs_to_any_bundle(Product_ID $product_id): bool
    {
        $criteria = new Product_Query_Criteria();
        $criteria->set_is_bundle(true);
        $bundles = $this->product_repository->find_by_criteria($criteria);

        foreach ($bundles as $bundle) {
            $bundle_items = $this->get_bundle_items($bundle->get_id());

            foreach ($bundle_items as $bundle_item) {
                if ($bundle_item->get_id()->equals($product_id)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function delete_product(Product_ID $id): void
    {
        $bundle_name = $this->get_bundle_name($id);

        $this->product_repository->delete($id);

        $this->product_events->emit_bundle_deleted_event(
            $bundle_name,
            $id->to_int()
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

        $this->product_events->emit_bundle_field_toggle_sales_updated_event(
            $product->get_name()->get_value(),
            $sales_disabled ? 'on' : 'off',
            $id->to_int()
        );
    }

    public function get_add_to_cart_popup_html(Product_ID $id): string
    {
        return Edit_Course::get_add_to_cart_popup_html($id->to_int());
    }

    private function get_bundle_item_collection_from_products(Product_Collection $products
    ): Bundle_Item_Display_Model_Collection {
        $collection = new Bundle_Item_Display_Model_Collection();

        foreach ($products as $product) {
            if ($product->is_bundle()) {
                continue;
            }

            $linked_digital_product = $this->digital_product_repository->find(new Digital_Product_ID($product->get_linked_resource_id()->to_int()));
            $linked_service = $this->service_repository->find(new Service_ID($product->get_linked_resource_id()->to_int()));

            if ($linked_digital_product) {
                $resource_type = new Resource_Type(Resource_Type::DIGITAL_PRODUCT);
            } elseif ($linked_service) {
                $resource_type = new Resource_Type(Resource_Type::SERVICE);
            } else {
                $resource_type = new Resource_Type(Resource_Type::COURSE);
            }

            $model = new Bundle_Item_Display_Model(
                $product->get_name(),
                $product->get_id(),
                $resource_type,
                $product->get_price()
            );

            $collection->add($model);
        }

        return $collection;
    }

    public function disable_sale(Product_ID $product_id): void
    {
        $product = $this->product_repository->find($product_id);

        if (!$product) {
            return;
        }

        $product->change_sales_disabled(true);

        $this->product_repository->save($product);
    }

    private function get_bundle_name(Product_ID $id): string
    {
        $product = $this->product_repository->find($id);

        return $product ? $product->get_name()->get_value() : '';
    }
}