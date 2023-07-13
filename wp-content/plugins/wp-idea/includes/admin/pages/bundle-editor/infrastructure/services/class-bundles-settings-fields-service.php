<?php

namespace bpmj\wpidea\admin\pages\bundle_editor\infrastructure\services;

use bpmj\wpidea\admin\pages\bundle_editor\core\events\Event_Name;
use bpmj\wpidea\admin\pages\product_editor\core\services\Checkboxes_Value_Changer;
use bpmj\wpidea\admin\settings\core\entities\fields\Abstract_Setting_Field;
use bpmj\wpidea\admin\settings\core\services\Interface_Settings_Fields_Service;
use bpmj\wpidea\app\bundles\Bundles_App_Service;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\sales\product\core\services\Product_Events;
use bpmj\wpidea\sales\product\dto\Product_DTO;
use bpmj\wpidea\sales\product\dto\Product_To_DTO_Mapper;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\sales\product\repository\Interface_Product_Repository;

class Bundles_Settings_Fields_Service implements Interface_Settings_Fields_Service
{
    private Product_ID $id;
    private Product_To_DTO_Mapper $product_to_DTO_mapper;
    private Interface_Product_Repository $product_repository;
    private Checkboxes_Value_Changer $checkboxes_value_changer;
    private Bundles_App_Service $bundles_app_service;
    private Interface_Events $events;

    private ?Product_DTO $cached_product = null;
    private Product_Events $product_events;

    public function __construct(
        Product_ID $edited_product_id,
        Product_To_DTO_Mapper $product_to_DTO_mapper,
        Interface_Product_Repository $product_repository,
        Checkboxes_Value_Changer $checkboxes_value_changer,
        Bundles_App_Service $bundles_app_service,
        Interface_Events $events,
        Product_Events $product_events
    ) {
        $this->id = $edited_product_id;
        $this->product_to_DTO_mapper = $product_to_DTO_mapper;
        $this->product_repository = $product_repository;
        $this->checkboxes_value_changer = $checkboxes_value_changer;
        $this->bundles_app_service = $bundles_app_service;
        $this->events = $events;
        $this->product_events = $product_events;
    }

    public function update_field(Abstract_Setting_Field $field): void
    {
        $field_name = $field->get_name();
        $product_dto = $this->fetch_and_cache_product_dto();
        $old_value = '';

        if (!$product_dto) {
            return;
        }

        $field_value = $this->checkboxes_value_changer->change_the_value($field_name, $field->get_value());

        if (property_exists($product_dto, $field_name)) {
            $old_value = $product_dto->$field_name;
            $product_dto->$field_name = $field_value;
        }

        $this->bundles_app_service->save_bundle($product_dto);

        $this->emit_field_updated_event($field);

        $this->product_events->emit_bundle_field_value_updated_event(
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

        if (!$product) {
            return null;
        }

        $this->cached_product = $this->product_to_DTO_mapper->map_product_to_dto($product);

        return $this->cached_product;
    }

    private function clear_cache(): void
    {
        $this->cached_product = null;
    }

    public function get_field_value(Abstract_Setting_Field $field)
    {
        $field_name = $field->get_name();
        $product_dto = $this->get_product_from_cache() ?? $this->fetch_and_cache_product_dto();

        if (!$product_dto) {
            return null;
        }

        $field_value = $product_dto->$field_name ?? null;

        return $this->checkboxes_value_changer->change_the_value($field_name, $field_value);
    }

    private function get_product_from_cache(): ?Product_DTO
    {
        return $this->cached_product;
    }

    private function emit_field_updated_event(Abstract_Setting_Field $field): void
    {
        $this->events->emit(Event_Name::FIELD_UPDATED, $field, $this->id);
    }
}