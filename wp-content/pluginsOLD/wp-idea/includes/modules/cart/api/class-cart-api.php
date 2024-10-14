<?php

namespace bpmj\wpidea\modules\cart\api;

use bpmj\wpidea\modules\cart\core\collections\Cart_Item_Collection;
use bpmj\wpidea\modules\cart\core\entities\Cart_Item;
use bpmj\wpidea\modules\cart\core\services\Cart;
use bpmj\wpidea\modules\cart\core\value_objects\Price_ID;
use bpmj\wpidea\modules\cart\core\value_objects\Product_ID;

class Cart_API
{
    private Cart $cart;

    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
    }

    public function get_item_name(Cart_Item $cart_item): string
    {
        return $this->cart->get_item_name($cart_item);
    }

    public function get_item_price(Cart_Item $cart_item): float
    {
        return $this->cart->get_item_price($cart_item);
    }

    public function get_item_discount_amount(Cart_Item $cart_item): float
    {
        return $this->cart->get_item_discount_amount($cart_item);
    }

    public function is_checkout(): bool
    {
        return $this->cart->is_checkout();
    }

    public function is_success_page(): bool
    {
        return $this->cart->is_success_page();
    }

    public function get_the_net_total_price(bool $ignore_discount = false): float
    {
        return $this->cart->get_the_net_total_price($ignore_discount);
    }

    public function get_total_vat_price(bool $ignore_discount = false): float
    {
        return $this->cart->get_total_vat_price($ignore_discount);
    }

    public function get_cart_content(): Cart_Item_Collection
    {
        return $this->cart->get_cart_content();
    }

    public function get_formatted_price_with_currency(string $price, string $currency = ''): string
    {
        return $this->cart->get_formatted_price_with_currency($price, $currency);
    }

    public function get_formatted_amount(string $amount, bool $decimals = true): string
    {
        return $this->cart->get_formatted_amount($amount, $decimals);
    }

    public function add(int $product_id, array $options = []): void
    {
        $this->cart->add($product_id, $options);
    }

    public function remove(int $product_id): void
    {
        $this->cart->remove($product_id);
    }

    public function offered_product_is_already_in_cart(
        int $product_id,
        ?int $product_variant_id,
        bool $check_for_variant
    ): bool {
        $product_id_model = new Product_ID($product_id);
        $product_variant_id_model = $product_variant_id ? new Price_ID($product_variant_id) : null;

        return $this->cart->offered_product_is_already_in_cart(
            $product_id_model,
            $product_variant_id_model,
            $check_for_variant
        );
    }

    public function set_error(string $error_id, string $message): void
    {
        $this->cart->set_error($error_id, $message);
    }
}