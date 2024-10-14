<?php

namespace bpmj\wpidea\modules\google_analytics\core\providers;

use bpmj\wpidea\infrastructure\system\System;
use bpmj\wpidea\modules\cart\api\Cart_API;
use bpmj\wpidea\sales\order\api\dto\Order_DTO;
use bpmj\wpidea\sales\product\api\dto\Product_DTO;
use bpmj\wpidea\sales\product\api\Interface_Product_API;
use bpmj\wpidea\modules\google_analytics\core\collections\Event_Item_Collection;
use bpmj\wpidea\modules\google_analytics\core\entities\{Event, Event_Item};

class Event_Data_Provider
{
    private const BEGIN_CHECKOUT_EVENT_NAME = 'begin_checkout';
    private const PURCHASE_EVENT_NAME = 'purchase';
    private const REMOVE_FROM_CART_EVENT_NAME = 'remove_from_cart';
    private const VIEW_ITEM_EVENT_NAME = 'view_item';
    private const ADD_TO_CART_EVENT_NAME = 'add_to_cart';

    private string $system_currency;
    private System $system;
    private Interface_Product_API $product_api;

    public function __construct(
        System $system,
        Interface_Product_API $product_api
    ) {
        $this->system = $system;
        $this->product_api = $product_api;
    }

    public function get_event_for_begin_checkout(Cart_API $cart_api): Event
    {
        $models = [];
        $total = 0;
        foreach ($cart_api->get_cart_content() as $cart_item) {
            $product_id = $cart_item->get_item_product_id()->to_int();
            $price = $cart_api->get_item_price($cart_item);
            $quantity = $cart_item->get_item_quantity();
            $product_variant_name = $cart_item->get_item_price_id() ? $this->get_product_variant_name(
                $product_id,
                $cart_item->get_item_price_id()->to_int()
            ) : '';

            $models[] = new Event_Item(
                $product_id,
                $cart_api->get_item_name($cart_item),
                $product_variant_name,
                $cart_item->get_item_quantity(),
                $cart_api->get_item_price($cart_item),
                $cart_api->get_item_discount_amount($cart_item)
            );

            $total += $quantity * $price;
        }

        $items = Event_Item_Collection::create_from_array($models);

        $content = [
            'currency' => $this->get_currency(),
            'value' => $total,
            'coupon' => '',
            'items' => $items,
        ];

        return $this->get_event(self::BEGIN_CHECKOUT_EVENT_NAME, $content);
    }

    public function get_event_for_purchase(Order_DTO $order): Event
    {
        $models = [];

        foreach ($order->get_cart_content()->get_item_details() as $cart_item_detail) {
            $item_number = $cart_item_detail['item_number'];
            $price_id = $item_number['options']['price_id'];
            $product_variant_name = !empty($price_id) ? $this->get_product_variant_name((int)$cart_item_detail['id'], (int)$price_id) : '';

            $models[] = new Event_Item(
                $cart_item_detail['id'],
                $cart_item_detail['name'],
                $product_variant_name,
                $cart_item_detail['quantity'],
                (float)$cart_item_detail['price'],
                (float)$cart_item_detail['discount']
            );
        }

        $items = Event_Item_Collection::create_from_array($models);

        $content = [
            'currency' => $this->get_currency(),
            'transaction_id' => (string)$order->get_id(),
            'value' => $order->get_total(),
            'items' => $items
        ];

        return $this->get_event(self::PURCHASE_EVENT_NAME, $content);
    }

    public function get_event_for_remove_from_cart(Product_DTO $product, ?int $variant_id): Event
    {
        $content = $this->get_event_content_to_array($product, $variant_id);

        return $this->get_event(self::REMOVE_FROM_CART_EVENT_NAME, $content);
    }

    public function get_event_for_view_item(Product_DTO $product): Event
    {
        $content = $this->get_event_content_to_array($product);

        return $this->get_event(self::VIEW_ITEM_EVENT_NAME, $content);
    }

    public function get_event_for_add_to_cart(Product_DTO $product, ?int $variant_id): Event
    {
        $content = $this->get_event_content_to_array($product, $variant_id);

        return $this->get_event(self::ADD_TO_CART_EVENT_NAME, $content);
    }

    private function get_event_content_to_array(Product_DTO $product, ?int $variant_id = null): array
    {
        $product_id = $product->get_id();
        $product_variant_name = $variant_id ? $this->get_product_variant_name($product_id, $variant_id) : '';

        $models[] = new Event_Item(
            $product_id,
            $product->get_name(),
            $product_variant_name,
            1,
            $product->get_price()
        );

        $items = Event_Item_Collection::create_from_array($models);

        return [
            'currency' => $this->get_currency(),
            'value' => $product->get_price(),
            'items' => $items
        ];
    }

    private function get_event(string $event_name, array $content): Event
    {
        $transaction_id = !empty($content['transaction_id']) ? $content['transaction_id'] : null;

        return new Event(
            $event_name,
            $transaction_id,
            $content['currency'],
            $content['value'],
            $content['items']
        );
    }

    private function get_product_variant_name(int $product_id, int $price_id): string
    {
        $price_variants = $this->product_api->get_price_variants($product_id);

        if (!$price_variants->has_pricing_variants) {
            return '';
        }

        $product_variant_name = '';
        foreach ($price_variants->variable_prices as $key => $variant) {
            if ($key === $price_id) {
                $product_variant_name = $variant['name'];
            }
        }

        return $product_variant_name;
    }

    private function get_currency(): string
    {
        if (!isset($this->system_currency)) {
            $this->system_currency = $this->system->get_system_currency();
        }

        return $this->system_currency;
    }
}
