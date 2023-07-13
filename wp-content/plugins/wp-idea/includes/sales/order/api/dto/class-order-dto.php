<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\sales\order\api\dto;

use bpmj\wpidea\sales\order\cart\Cart_Content;
use bpmj\wpidea\sales\order\client\Client;
use bpmj\wpidea\sales\order\invoice\Invoice;

class Order_DTO
{
    private int $id;
    private float $total;
    private Cart_Content $cart_content;
    private Client $client;
    private Invoice $invoice;

    private function __construct(int $id, Cart_Content $cart_content, Client $client, Invoice $invoice, float $total)
    {
        $this->id = $id;
        $this->cart_content = $cart_content;
        $this->client = $client;
        $this->invoice = $invoice;
        $this->total = $total;
    }

    public static function create(
        int $id,
        Cart_Content $cart_content,
        Client $client,
        Invoice $invoice,
        float $total
    ): self {
        return new self(
            $id,
            $cart_content,
            $client,
            $invoice,
            $total
        );
    }

    public function get_id(): int
    {
        return $this->id;
    }

    public function get_cart_content(): Cart_Content
    {
        return $this->cart_content;
    }

    public function get_client(): Client
    {
        return $this->client;
    }

    public function get_invoice(): Invoice
    {
        return $this->invoice;
    }

    public function get_total(): float
    {
        return $this->total;
    }
}