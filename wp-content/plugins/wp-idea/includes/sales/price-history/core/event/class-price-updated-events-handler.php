<?php

namespace bpmj\wpidea\sales\price_history\core\event;

use bpmj\wpidea\sales\product\core\event\Event_Name;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\events\Interface_Event_Handler;
use bpmj\wpidea\sales\price_history\core\provider\Interface_Price_History_Provider;
use bpmj\wpidea\sales\price_history\core\provider\Interface_Product_Data_Provider;
use bpmj\wpidea\sales\price_history\core\model\Historic_Price;
use bpmj\wpidea\sales\price_history\core\service\Decision_Maker;

class Price_Updated_Events_Handler implements Interface_Event_Handler
{
    private Interface_Events $events;
    private Interface_Price_History_Provider $price_history_provider;
    private Interface_Product_Data_Provider $product_data_provider;
    private Decision_Maker $decision_maker;

    public function __construct(
        Interface_Events $events,
        Interface_Price_History_Provider $price_history_provider,
        Interface_Product_Data_Provider $product_data_provider,
        Decision_Maker $decision_maker
    ) {
        $this->events = $events;
        $this->price_history_provider = $price_history_provider;
        $this->product_data_provider = $product_data_provider;
        $this->decision_maker = $decision_maker;
    }

    public function init(): void
    {
        $this->events->on(Event_Name::REGULAR_PRICE_UPDATED, [$this, 'handle_regular_price_updated'], 10, 2);
        $this->events->on(Event_Name::PROMO_PRICE_UPDATED, [$this, 'handle_promo_price_updated'], 10, 2);
        $this->events->on(Event_Name::VARIABLE_PRICES_UPDATED, [$this, 'handle_variable_prices_updated'], 10, 2);
    }

    public function handle_regular_price_updated($product_id, $new_value): void
    {
        $int_product_id = (int)$product_id;

        if (empty($int_product_id)) {
            return;
        }

        $new_price = $new_value === '' ? 0 : (float)$new_value;
        $old_price = $this->product_data_provider->get_product_regular_price($int_product_id);

        if($old_price === null) {
            return;
        }

        $new_promo_price = $this->product_data_provider->get_product_promo_price($int_product_id);
        $old_promo_price = $new_promo_price;

        $this->maybe_store_price_change($int_product_id, null, $new_price, $old_price, $new_promo_price, $old_promo_price);
    }

    public function handle_promo_price_updated($product_id, $new_value): void
    {
        $int_product_id = (int)$product_id;

        if (empty($int_product_id)) {
            return;
        }

        $new_promo_price = (is_null($new_value) || ($new_value === '')) ? null : (float)$new_value;
        $old_promo_price = $this->product_data_provider->get_product_promo_price($int_product_id);

        $new_price = $this->product_data_provider->get_product_regular_price($int_product_id);
        $old_price = $new_price;

        $this->maybe_store_price_change($int_product_id, null, $new_price, $old_price, $new_promo_price, $old_promo_price);
    }

    public function handle_variable_prices_updated($product_id, $new_value): void
    {
        $int_product_id = (int)$product_id;

        if (empty($int_product_id)) {
            return;
        }

        $old_value = $this->product_data_provider->get_product_variable_prices($int_product_id);

        if (!is_array($new_value) || !is_array($old_value)) {
            return;
        }

        $new_value = array_filter($new_value);

        $this->variable_price_deleted($product_id, $old_value, $new_value);

        foreach ($new_value as $variant_id => $price_data) {
            $is_new_variant_created_and_not_updated = !isset($old_value[$variant_id]);

            if($is_new_variant_created_and_not_updated) {
                continue;
            }

                $new_variant_price = (float)$price_data['amount'];
                $old_variant_price = (float)$old_value[$variant_id]['amount'];

                $new_promo_variant_price = isset($price_data['sale_price']) && $price_data['sale_price'] !== '' ? (float)$price_data['sale_price'] : null;
                $old_promo_variant_price = isset($old_value[$variant_id]['sale_price']) && $old_value[$variant_id]['sale_price'] !== '' ? (float)$old_value[$variant_id]['sale_price'] : null;

                $this->maybe_store_price_change(
                    $int_product_id,
                    $variant_id,
                    $new_variant_price,
                    $old_variant_price,
                    $new_promo_variant_price,
                    $old_promo_variant_price
                );
        }

    }

    private function variable_price_deleted(int $product_id, array $old_value, array $new_value): void
    {
        foreach ($old_value as $variant_id => $price_data) {
            $variant_exists = isset($new_value[$variant_id]);

            if ($variant_exists) {
                continue;
            }

            $this->price_history_provider->delete_by_product($product_id, $variant_id);
        }
    }

    private function maybe_store_price_change(
        int $product_id,
        ?int $variant_id,
        $new_price,
        $old_price,
        ?float $new_promo_price,
        ?float $old_promo_price
    ): void {
        $last_historic_price = $this->get_last_historic_price_for_product($product_id, $variant_id);

        if(!$this->decision_maker->should_price_change_be_recorded(
            $old_price,
            $new_price,
            $old_promo_price,
            $new_promo_price,
            $last_historic_price
        )) {
            return;
        }

        $this->price_history_provider->store_price_change(
            $product_id,
            $variant_id,
            $old_price,
            $new_price,
            $old_promo_price,
            $new_promo_price
        );
    }

    private function get_last_historic_price_for_product(
        int $product_id,
        ?int $variant_id
    ): ?Historic_Price
    {
        return $this->price_history_provider->find_last_price_for_product($product_id, $variant_id);
    }
}