<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\app\digital_products;

use bpmj\wpidea\data_types\ID;
use bpmj\wpidea\digital_products\dto\Digital_Product_DTO;
use bpmj\wpidea\digital_products\model\Digital_Product;
use bpmj\wpidea\digital_products\model\Digital_Product_ID;
use bpmj\wpidea\digital_products\repository\Interface_Digital_Product_Repository;
use bpmj\wpidea\digital_products\service\Digital_Product_Creator_Service;
use bpmj\wpidea\sales\product\core\services\Product_Events;
use bpmj\wpidea\sales\product\dto\Product_DTO;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\sales\product\Product_Collection;
use bpmj\wpidea\sales\product\repository\Interface_Product_Repository;
use bpmj\wpidea\sales\product\repository\Product_Query_Criteria;
use bpmj\wpidea\sales\product\service\Product_Creator_Service;

class Digital_Products_App_Service
{
    private Interface_Digital_Product_Repository $digital_product_repository;
    private Interface_Product_Repository $product_repository;
    private Product_Creator_Service $product_creator_service;
    private Digital_Product_Creator_Service $digital_product_creator_service;
    private Product_Events $product_events;

    public function __construct(
        Interface_Digital_Product_Repository $digital_product_repository,
        Interface_Product_Repository $product_repository,
        Product_Creator_Service $product_creator_service,
        Digital_Product_Creator_Service $digital_product_creator_service,
        Product_Events $product_events
    ) {
        $this->digital_product_repository = $digital_product_repository;
        $this->product_repository = $product_repository;
        $this->product_creator_service = $product_creator_service;
        $this->digital_product_creator_service = $digital_product_creator_service;
        $this->product_events = $product_events;
    }

    public function save_digital_product(Product_DTO $product_dto, Digital_Product_DTO $digital_product_dto): Product_ID
    {
        $resource_id = $this->digital_product_creator_service->save_digital_product($digital_product_dto);

        $product_dto->linked_resource_id = $resource_id->to_int();

        return $this->product_creator_service->save_product($product_dto);
    }

    public function find_all_offers(): Product_Collection
    {
        $offers = new Product_Collection();

        foreach ($this->digital_product_repository->find_all() as $digital_product) {
            $criteria = $this->get_query_criteria_for_digital_product_offers($digital_product->get_id());

            $offer = $this->product_repository->find_by_criteria(
                $criteria
            );

            if (!$offer) {
                continue;
            }

            $offers->add($offer->current());
        }

        return $offers;
    }

    public function find_digital_product_by_offer_id(Product_ID $id): ?Digital_Product
    {
        $product = $this->product_repository->find(
            $id
        );

        if (!$product || is_null($product->get_linked_resource_id())) {
            return null;
        }

        return $this->digital_product_repository->find(
            new Digital_Product_ID($product->get_linked_resource_id()->to_int())
        );
    }

    public function delete_product(Product_ID $id): void
    {
        $digital_product_name = $this->get_digital_product_name($id);

        $this->product_repository->delete($id);

        $this->product_events->emit_digital_product_deleted_event(
            $digital_product_name,
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

        if($product->sales_disabled()){
            $sales_disabled = false;
        }

        $product->change_sales_disabled($sales_disabled);

        $this->product_repository->save($product);

        $this->product_events->emit_digital_product_field_toggle_sales_updated_event(
            $product->get_name()->get_value(),
            $sales_disabled ? 'on' : 'off',
            $id->to_int()
        );
    }

    private function get_query_criteria_for_digital_product_offers(Digital_Product_ID $digital_product_id): Product_Query_Criteria
    {
        $resource_id = new ID($digital_product_id->to_int());
        $criteria = new Product_Query_Criteria();

        $criteria->set_linked_resource_id(
            $resource_id
        );

        return $criteria;
    }

    private function get_digital_product_name(Product_ID $id): string
    {
        $product = $this->product_repository->find($id);

        return $product ? $product->get_name()->get_value() : '';
    }
}