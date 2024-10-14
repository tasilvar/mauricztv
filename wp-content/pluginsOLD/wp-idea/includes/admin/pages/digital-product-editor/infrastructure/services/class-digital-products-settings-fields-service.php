<?php

namespace bpmj\wpidea\admin\pages\digital_product_editor\infrastructure\services;

use bpmj\wpidea\admin\pages\product_editor\core\services\Checkboxes_Value_Changer;
use bpmj\wpidea\admin\settings\core\entities\fields\Abstract_Setting_Field;
use bpmj\wpidea\admin\settings\core\services\Interface_Settings_Fields_Service;
use bpmj\wpidea\app\digital_products\Digital_Products_App_Service;
use bpmj\wpidea\digital_products\dto\Digital_Product_DTO;
use bpmj\wpidea\digital_products\dto\Digital_Product_To_Dto_Mapper;
use bpmj\wpidea\sales\product\core\services\Product_Events;
use bpmj\wpidea\sales\product\dto\Product_DTO;
use bpmj\wpidea\sales\product\dto\Product_To_DTO_Mapper;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\sales\product\repository\Interface_Product_Repository;

class Digital_Products_Settings_Fields_Service implements Interface_Settings_Fields_Service
{
    private Product_ID $id;
    private Product_To_DTO_Mapper $product_to_DTO_mapper;
    private Interface_Product_Repository $product_repository;
    private Checkboxes_Value_Changer $checkboxes_value_changer;
    private Digital_Products_App_Service $app_service;
    private Digital_Product_To_Dto_Mapper $digital_product_to_dto_mapper;

    private ?Product_DTO $cached_product = null;
    private ?Digital_Product_DTO $cached_digital_product = null;
    private Product_Events $product_events;

    public function __construct(
        Product_ID $edited_product_id,
        Product_To_DTO_Mapper $product_to_DTO_mapper,
        Interface_Product_Repository $product_repository,
        Checkboxes_Value_Changer $checkboxes_value_changer,
        Digital_Products_App_Service $app_service,
        Digital_Product_To_Dto_Mapper $digital_product_to_dto_mapper,
        Product_Events $product_events
    ) {
        $this->id = $edited_product_id;
        $this->product_to_DTO_mapper = $product_to_DTO_mapper;
        $this->product_repository = $product_repository;
        $this->checkboxes_value_changer = $checkboxes_value_changer;
        $this->app_service = $app_service;
        $this->digital_product_to_dto_mapper = $digital_product_to_dto_mapper;
        $this->product_events = $product_events;
    }

    public function update_field(Abstract_Setting_Field $field): void
    {
        $field_name = $field->get_name();
        $product_dto = $this->fetch_and_cache_product_dto();
        $digital_product_dto = $this->fetch_and_cache_digital_product_dto();
        $old_value = '';

        if(!$product_dto || !$digital_product_dto){
            return;
        }

        $field_value = $this->checkboxes_value_changer->change_the_value($field_name, $field->get_value());

        if(property_exists($product_dto, $field_name)) {
            $old_value = $product_dto->$field_name;
            $product_dto->$field_name = $field_value;
        }
        if(property_exists($digital_product_dto, $field_name)) {
            $old_value = $digital_product_dto->$field_name;
            $digital_product_dto->$field_name = $field_value;
        }

        $this->app_service->save_digital_product($product_dto, $digital_product_dto);

        $this->product_events->emit_digital_product_field_value_updated_event(
            $field,
            $old_value,
            $field->get_value(),
            $this->id->to_int()
        );

        $this->clear_cache();
    }

    private function fetch_and_cache_product_dto(): ?Product_DTO
    {
        $product = $this->product_repository->find($this->id);

        if(!$product){
            return null;
        }

        $this->cached_product = $this->product_to_DTO_mapper->map_product_to_dto($product);

        return $this->cached_product;
    }

    private function fetch_and_cache_digital_product_dto(): ?Digital_Product_DTO
    {
        $digital_product = $this->app_service->find_digital_product_by_offer_id($this->id);

        if(!$digital_product){
            return null;
        }

        $this->cached_digital_product = $this->digital_product_to_dto_mapper->map_digital_product_to_dto($digital_product);

        return $this->cached_digital_product;
    }

    private function clear_cache(): void
    {
        $this->cached_product = null;
        $this->cached_digital_product = null;
    }

    public function get_field_value(Abstract_Setting_Field $field)
    {
        $field_name = $field->get_name();
        $product_dto = $this->get_product_from_cache() ?? $this->fetch_and_cache_product_dto();
        $digital_product_dto = $this->get_digital_product_from_cache() ?? $this->fetch_and_cache_digital_product_dto();

        if(!$product_dto || !$digital_product_dto){
            return null;
        }

        $field_value = $product_dto->$field_name ?? $digital_product_dto->$field_name ?? null;

        return $this->checkboxes_value_changer->change_the_value($field_name, $field_value);
    }

    private function get_product_from_cache(): ?Product_DTO
    {
        return $this->cached_product;
    }

    private function get_digital_product_from_cache(): ?Digital_Product_DTO
    {
        return $this->cached_digital_product;
    }
}