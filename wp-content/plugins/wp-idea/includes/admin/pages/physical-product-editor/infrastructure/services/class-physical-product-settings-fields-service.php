<?php

namespace bpmj\wpidea\admin\pages\physical_product_editor\infrastructure\services;

use bpmj\wpidea\admin\pages\product_editor\core\services\Checkboxes_Value_Changer;
use bpmj\wpidea\admin\settings\core\entities\fields\Abstract_Setting_Field;
use bpmj\wpidea\admin\settings\core\services\Interface_Settings_Fields_Service;
use bpmj\wpidea\app\physical_products\Physical_Products_App_Service;
use bpmj\wpidea\physical_product\dto\Physical_Product_DTO;
use bpmj\wpidea\sales\product\core\services\Product_Events;
use bpmj\wpidea\sales\product\dto\Product_DTO;
use bpmj\wpidea\sales\product\dto\Product_To_DTO_Mapper;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\sales\product\repository\Interface_Product_Repository;

class Physical_Product_Settings_Fields_Service implements Interface_Settings_Fields_Service
{
    private Product_ID $id;
    private Product_To_DTO_Mapper $product_to_DTO_mapper;
    private Interface_Product_Repository $product_repository;
    private Checkboxes_Value_Changer $checkboxes_value_changer;
    private Physical_Products_App_Service $physical_products_app_service;

    private ?Product_DTO $cached_product = null;
    private Product_Events $product_events;

    public function __construct(
        Product_ID $edited_product_id,
        Product_To_DTO_Mapper $product_to_DTO_mapper,
        Interface_Product_Repository $product_repository,
        Physical_Products_App_Service $physical_products_app_service,
        Checkboxes_Value_Changer $checkboxes_value_changer,
        Product_Events $product_events
    ) {
        $this->id = $edited_product_id;
        $this->product_to_DTO_mapper = $product_to_DTO_mapper;
        $this->product_repository = $product_repository;
        $this->physical_products_app_service = $physical_products_app_service;
        $this->checkboxes_value_changer = $checkboxes_value_changer;
        $this->product_events = $product_events;
    }

    public function update_field(Abstract_Setting_Field $field): void
    {
        $field_name = $field->get_name();
        $product_dto = $this->fetch_and_cache_product();
        $old_value = '';

        if(!$product_dto){
            return;
        }

        $physical_product_dto = $this->create_physical_product_dto_from_product_dto($product_dto);

        $field_value = $this->checkboxes_value_changer->change_the_value($field_name, $field->get_value());

        if(property_exists($product_dto, $field_name)) {
            $old_value = $product_dto->$field_name;
            $product_dto->$field_name = $field_value;
        }

        if(property_exists($physical_product_dto, $field_name)) {
            $old_value = $physical_product_dto->$field_name;
            $physical_product_dto->$field_name = $field_value;
        }

        $this->physical_products_app_service->save_physical_product($product_dto, $physical_product_dto);

        $this->product_events->emit_physical_product_field_value_updated_event(
            $field,
            $old_value,
            $field->get_value(),
            $this->id->to_int()
        );

        $this->clear_product_cache();
    }

    private function fetch_and_cache_product(): ?Product_DTO
    {
        $product = $this->product_repository->find($this->id);

        if(!$product){
            return null;
        }

        $this->cached_product = $this->product_to_DTO_mapper->map_product_to_dto($product);

        return $this->cached_product;
    }

    private function clear_product_cache(): void
    {
        $this->cached_product = null;
    }

    public function get_field_value(Abstract_Setting_Field $field)
    {
        $field_name = $field->get_name();
        $dto = $this->get_product_from_cache() ?? $this->fetch_and_cache_product();

        if(!$dto){
            return null;
        }

        $field_value = $dto->$field_name ?? null;
        
        return $this->checkboxes_value_changer->change_the_value($field_name, $field_value);
    }

    private function get_product_from_cache(): ?Product_DTO
    {
        return $this->cached_product;
    }

    private function create_physical_product_dto_from_product_dto(Product_DTO $product_dto): Physical_Product_DTO
    {
        $service_dto = new Physical_Product_DTO();
        $service_dto->id = $product_dto->id;
        $service_dto->name = $product_dto->name;

        return $service_dto;
    }
}