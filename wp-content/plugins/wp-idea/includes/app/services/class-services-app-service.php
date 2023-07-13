<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\app\services;

use bpmj\wpidea\data_types\ID;
use bpmj\wpidea\sales\product\core\services\Product_Events;
use bpmj\wpidea\sales\product\dto\Product_DTO;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\sales\product\Product_Collection;
use bpmj\wpidea\sales\product\repository\Interface_Product_Repository;
use bpmj\wpidea\sales\product\repository\Product_Query_Criteria;
use bpmj\wpidea\sales\product\service\Product_Creator_Service;
use bpmj\wpidea\service\dto\Service_DTO;
use bpmj\wpidea\service\model\Service;
use bpmj\wpidea\service\model\Service_ID;
use bpmj\wpidea\service\repository\Interface_Service_Repository;
use bpmj\wpidea\service\service\Service_Creator_Service;

class Services_App_Service
{
    private Interface_Service_Repository $service_repository;
    private Interface_Product_Repository $product_repository;
    private Product_Creator_Service $product_creator_service;
    private Service_Creator_Service $service_creator_service;
    private Product_Events $product_events;

    public function __construct(
        Interface_Service_Repository $service_repository,
        Interface_Product_Repository $product_repository,
        Product_Creator_Service $product_creator_service,
        Service_Creator_Service $service_creator_service,
        Product_Events $product_events
    ) {
        $this->service_repository = $service_repository;
        $this->product_repository = $product_repository;
        $this->product_creator_service = $product_creator_service;
        $this->service_creator_service = $service_creator_service;
        $this->product_events = $product_events;
    }

    public function save_service(Product_DTO $product_dto, Service_DTO $service_dto): Product_ID
    {
        $resource_id = $this->service_creator_service->save_service($service_dto);

        $product_dto->linked_resource_id = $resource_id->to_int();

        return $this->product_creator_service->save_product($product_dto);
    }

    public function find_all_offers(): Product_Collection
    {
        $offers = new Product_Collection();

        foreach ($this->service_repository->find_all() as $digital_product) {
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

    public function find_service_by_offer_id(Service_ID $id): ?Service
    {
        $product = $this->product_repository->find(
            new Product_ID($id->to_int())
        );

        if (!$product || is_null($product->get_linked_resource_id())) {
            return null;
        }

        return $this->service_repository->find(
            new Service_ID($product->get_linked_resource_id()->to_int())
        );
    }

    public function delete_product(Product_ID $product_id): void
    {
        $service_name = $this->get_service_name($product_id);

        $this->product_repository->delete($product_id);

        $this->product_events->emit_services_deleted_event(
            $service_name,
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

        if($product->sales_disabled()){
            $sales_disabled = false;
        }

        $product->change_sales_disabled($sales_disabled);

        $this->product_repository->save($product);

        $this->product_events->emit_service_field_toggle_sales_updated_event(
            $product->get_name()->get_value(),
            $sales_disabled ? 'on' : 'off',
            $id->to_int()
        );
    }

    private function get_query_criteria_for_digital_product_offers(Service_ID $service_id
    ): Product_Query_Criteria {
        $resource_id = new ID($service_id->to_int());
        $criteria = new Product_Query_Criteria();

        $criteria->set_linked_resource_id(
            $resource_id
        );

        return $criteria;
    }

    private function get_service_name(Product_ID $id): string
    {
        $product = $this->product_repository->find($id);

        return $product ? $product->get_name()->get_value() : '';
    }
}