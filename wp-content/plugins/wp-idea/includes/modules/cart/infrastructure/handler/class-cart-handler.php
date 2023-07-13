<?php

namespace bpmj\wpidea\modules\cart\infrastructure\handler;

use bpmj\wpidea\modules\cart\core\collections\Cart_Item_Collection;
use bpmj\wpidea\modules\cart\core\entities\Cart_Item;
use bpmj\wpidea\modules\cart\core\handler\Interface_Cart_Handler;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\modules\cart\core\value_objects\{Price_ID, Product_ID};

class Cart_Handler implements Interface_Cart_Handler
{
    private Price_Parser $price_parser;
    private Interface_Translator $translator;

    public function __construct(
        Price_Parser $price_parser,
        Interface_Translator $translator
    ) {
        $this->price_parser = $price_parser;
        $this->translator = $translator;
    }

    public function is_checkout(): bool
    {
        return edd_is_checkout();
    }

    public function is_success_page(): bool
    {
        return edd_is_success_page();
    }

    public function get_item_discount_amount(array $item): float
    {
        return (float)edd_get_cart_item_discount_amount($item);
    }

    public function get_item_name(array $item): string
    {
        return edd_get_cart_item_name($item);
    }

    public function get_item_price(int $id, array $options): float
    {
        $label = edd_cart_item_price($id, $options);

        return $this->price_parser->get_parsed_price_gross_from_label($label);
    }

    public function get_total(): float
    {
        return (float)edd_get_cart_total();
    }

    public function get_subtotal(): float
    {
        return (float)edd_get_cart_subtotal();
    }

    public function get_content(): Cart_Item_Collection
    {
        $cart_contents = edd_get_cart_contents();

        $models = [];

        foreach ($cart_contents as $cart_content_item) {
            $models[] = $this->cart_content_item_to_model($cart_content_item);
        }

        return Cart_Item_Collection::create_from_array(
            $models
        );
    }

    public function get_formatted_price_with_currency(string $price, string $currency = ''): string
    {
        return edd_currency_filter($price, $currency);
    }

    public function get_formatted_amount(string $amount, bool $decimals = true): string
    {
        return edd_format_amount($amount, $decimals);
    }

    public function add(int $product_id, array $options = []): void
    {
        edd_add_to_cart($product_id, $options);
    }

    public function remove(int $product_id): void
    {
        edd_remove_from_cart($product_id);
    }

    public function set_error(string $error_id, string $message): void
    {
        edd_set_error($error_id, $this->translator->translate($message));
    }

    private function cart_content_item_to_model(array $item): Cart_Item
    {
        $price_id = isset($item['options']['price_id']) ? new Price_ID((int)$item['options']['price_id']) : null;

        return new Cart_Item(
            new Product_ID((int)$item['id']),
            $price_id,
            (int)$item['quantity']
        );
    }
}