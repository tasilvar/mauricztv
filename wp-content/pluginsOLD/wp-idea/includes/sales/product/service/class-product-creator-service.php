<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\sales\product\service;

use bpmj\wpidea\modules\logs\core\events\Settings_Field_Value_Changed_Event_Payload;
use bpmj\wpidea\events\Event_Name;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\sales\product\acl\Interface_Product_Variable_Prices_ACL;
use bpmj\wpidea\sales\product\api\Interface_Product_API;
use bpmj\wpidea\sales\product\core\services\Product_Events;
use bpmj\wpidea\sales\product\dto\Product_DTO;
use bpmj\wpidea\sales\product\dto\Product_To_DTO_Mapper;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\sales\product\repository\Interface_Product_Repository;
use bpmj\wpidea\translator\Interface_Translator;

class Product_Creator_Service
{
    private Interface_Product_Repository $product_repository;
    private Product_To_DTO_Mapper $product_to_DTO_mapper;
    private Interface_Product_Variable_Prices_ACL $product_variable_prices_acl;
    private Interface_Product_API $product_api;
    private Interface_Events $events;
    private Interface_Translator $translator;
    private Product_Events $product_events;

    public function __construct(
        Interface_Product_Repository $product_repository,
        Product_To_DTO_Mapper $product_to_DTO_mapper,
        Interface_Product_Variable_Prices_ACL $product_variable_prices_acl,
        Interface_Product_API $product_api,
        Product_Events $product_events
    ) {
        $this->product_repository = $product_repository;
        $this->product_to_DTO_mapper = $product_to_DTO_mapper;
        $this->product_variable_prices_acl = $product_variable_prices_acl;
        $this->product_api = $product_api;
        $this->product_events = $product_events;
    }

    public function save_product(Product_DTO $product_DTO): Product_ID
    {
        $product = $this->product_to_DTO_mapper->map_dto_to_product($product_DTO);

        return $this->product_repository->save($product);
    }

    public function get_variable_prices(int $post_id): string
    {
        return $this->product_variable_prices_acl->get_variable_prices($post_id);
    }

    public function save_variable_prices(int $product_id, array $fields): array
    {
        $this->emit_variable_prices_updated_event($product_id, $fields);

        return $this->product_variable_prices_acl->save($product_id, $fields);
    }

    private function emit_variable_prices_updated_event(int $product_id, array $fields): void
    {
        $price_variants = $this->product_api->get_price_variants($product_id);
        $old_value = $price_variants->variable_prices;

        $this->product_events->emit_bundle_variable_prices_updated_event($old_value, $fields, $product_id);
    }
}
