<?php

namespace bpmj\wpidea\modules\cart\api;


class Cart_API_Static_Helper
{
    private static Cart_API $cart_api;

    public static function init(Cart_API $cart_api): void
    {
        self::$cart_api = $cart_api;
    }

    public static function get_the_net_total_price(bool $ignore_discount = false): float
    {
        return self::$cart_api->get_the_net_total_price($ignore_discount);
    }

    public static function get_total_vat_price(bool $ignore_discount = false): float
    {
        return self::$cart_api->get_total_vat_price($ignore_discount);
    }

    public static function get_formatted_price_with_currency(string $price, string $currency = ''): string
    {
        return self::$cart_api->get_formatted_price_with_currency($price, $currency);
    }

    public static function get_formatted_amount(string $amount, bool $decimals = true): string
    {
        return self::$cart_api->get_formatted_amount($amount, $decimals);
    }

    public static function set_error(string $error_id, string $message): void
    {
        self::$cart_api->set_error($error_id, $message);
    }
}