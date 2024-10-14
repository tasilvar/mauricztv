<?php

namespace bpmj\wpidea\modules\cart\core\services;

use bpmj\wpidea\modules\cart\core\collections\Cart_Item_Collection;
use bpmj\wpidea\modules\cart\core\entities\Cart_Item;
use bpmj\wpidea\modules\cart\core\handler\Interface_Cart_Handler;
use bpmj\wpidea\modules\cart\core\value_objects\Price_ID;
use bpmj\wpidea\modules\cart\core\value_objects\Product_ID as Cart_Product_ID;
use bpmj\wpidea\modules\increasing_sales\core\value_objects\Product_ID as Increasing_Sales_Product_ID;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\sales\product\model\Variant_ID;
use bpmj\wpidea\sales\product\service\Product_Vat_Rate_Getter;
use bpmj\wpidea\sales\product\service\Net_Price_Calculator;

class Cart
{

    private Interface_Cart_Handler $cart_handler;
    private Fees $fees;
    private Product_Vat_Rate_Getter $product_vat_rate_getter;
    private Net_Price_Calculator $net_price_calculator;

    public function __construct(
        Interface_Cart_Handler $cart_handler,
        Fees $fees,
        Product_Vat_Rate_Getter $product_vat_rate_getter,
        Net_Price_Calculator $net_price_calculator
    ) {
        $this->cart_handler = $cart_handler;
        $this->fees = $fees;
        $this->product_vat_rate_getter = $product_vat_rate_getter;
        $this->net_price_calculator = $net_price_calculator;
    }

    public function is_checkout(): bool
    {
        return $this->cart_handler->is_checkout();
    }

    public function is_success_page(): bool
    {
        return $this->cart_handler->is_success_page();
    }

    public function get_item_name(Cart_Item $cart_item): string
    {
        $item = $this->get_cart_item_model_to_array($cart_item);

        return $this->cart_handler->get_item_name($item);
    }

    public function get_item_price(Cart_Item $cart_item): float
    {
        $item = $this->get_cart_item_model_to_array($cart_item);

        return $this->cart_handler->get_item_price($item['id'], $item['options']);
    }

    public function get_item_discount_amount(Cart_Item $cart_item): float
    {
        $item = $this->get_cart_item_model_to_array($cart_item);

        return $this->cart_handler->get_item_discount_amount($item);
    }

    public function get_the_net_total_price(bool $ignore_discount = false): float
    {
        $net_price_array = [];

        foreach ($this->get_cart_content() as $item_cart) {
            $item = $this->get_cart_item_model_to_array($item_cart);

            $discount = $ignore_discount ? 0 : $this->cart_handler->get_item_discount_amount($item);
            $item_price = $this->cart_handler->get_item_price($item_cart->get_item_product_id()->to_int(), $item['options']);
            $net_price = $this->get_the_net_price($item_price, $item_cart->get_item_product_id()->to_int(), $discount);
            $net_price_array[] = $net_price;
        }

        foreach ($this->fees->get_fees() as $fee) {
            if(!$fee->get_net_amount()) {
                continue;
            }

            $net_price_array[] = $fee->get_net_amount();
        }

        return array_sum($net_price_array);
    }

    public function get_total_vat_price(bool $ignore_discount = false): float
    {
        $cart_total = $ignore_discount ? $this->calculate_total_without_discounts() : $this->cart_handler->get_total();
        $total_net_price = $this->get_the_net_total_price($ignore_discount);

        if ($total_net_price <= 0) {
            return 0;
        }

        return (float)number_format(($cart_total - $total_net_price), 2, '.', '');
    }

    public function get_cart_content(): Cart_Item_Collection
    {
        return $this->cart_handler->get_content();
    }

    public function get_formatted_price_with_currency(string $price, string $currency = ''): string
    {
        return $this->cart_handler->get_formatted_price_with_currency($price, $currency);
    }

    public function get_formatted_amount(string $amount, bool $decimals = true): string
    {
        return $this->cart_handler->get_formatted_amount($amount, $decimals);
    }

    public function add(int $product_id, array $options = []): void
    {
        $this->cart_handler->add($product_id, $options);
    }

    public function remove(int $product_id): void
    {
        $this->cart_handler->remove($product_id);
    }

    public function offered_product_is_already_in_cart(
        Cart_Product_ID $offered_product_id,
        ?Price_ID $offered_product_variant_id,
        bool $check_for_variant
    ): bool {
        $compared_product_id = new Increasing_Sales_Product_ID($offered_product_id->to_int());
        $compared_product_variant_id = $offered_product_variant_id
            ? new Variant_ID($offered_product_variant_id->to_int())
            : null;


        foreach ($this->cart_handler->get_content()->to_array() as $item) {
            $id = $item->get_item_product_id() ? $item->get_item_product_id()->to_int() : null;

            if (!$id) {
                continue;
            }

            $id = new Increasing_Sales_Product_ID($id);
            $variant_id = $item->get_item_price_id() ? $item->get_item_price_id()->to_int() : null;
            $variant_id = $variant_id ? new Variant_ID($variant_id) : null;

            if (!$id->equals($compared_product_id)) {
                continue;
            }

            if (!$check_for_variant) {
                return true;
            }

            if (!$variant_id && !$compared_product_variant_id) {
                return true;
            }

            if ($variant_id && $compared_product_variant_id && $variant_id->equals($compared_product_variant_id)) {
                return true;
            }
        }

        return false;
    }

    private function get_the_net_price(float $price, int $item_id, float $discount): float
    {
        $vat_rate = $this->product_vat_rate_getter->get_vat_rate_for_product(new Product_ID($item_id));

        $discount = round($discount, 2);

        $price_gross = $price - $discount;

        return $this->net_price_calculator->calculate_net_price($price_gross, $vat_rate);
    }

    private function calculate_total_without_discounts(): float
    {
        $subtotal = $this->cart_handler->get_subtotal();
        $total = $subtotal;

        if ($total < 0) {
            $total = 0.00;
        }

        return $total;
    }



    private function get_cart_item_model_to_array(Cart_Item $cart_item): array
    {
        $item_price_id = $cart_item->get_item_price_id() ? $cart_item->get_item_price_id()->to_int() : null;

        return [
            'id' => $cart_item->get_item_product_id()->to_int(),
            'options' => [
                'price_id' => $item_price_id
            ],
            'quantity' => $cart_item->get_item_quantity()
        ];
    }

    public function set_error(string $error_id, string $message): void
    {
        $this->cart_handler->set_error($error_id, $message);
    }
}