<?php

namespace bpmj\wpidea\modules\cart\core\handler;

use bpmj\wpidea\modules\cart\core\collections\Cart_Item_Collection;

interface Interface_Cart_Handler
{
    public function is_checkout(): bool;

    public function is_success_page(): bool;

    public function get_item_discount_amount(array $item): float;

    public function get_item_name(array $item): string;

    public function get_item_price(int $id, array $options): float;

    public function get_total(): float;

    public function get_subtotal(): float;

    public function get_content(): Cart_Item_Collection;

    public function get_formatted_price_with_currency(string $price, string $currency = ''): string;

    public function get_formatted_amount(string $amount, bool $decimals = true): string;

    public function add(int $product_id, array $options = []): void;

    public function remove(int $product_id): void;

    public function set_error(string $error_id, string $message): void;
}