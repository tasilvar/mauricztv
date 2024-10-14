<?php

declare(strict_types=1);

namespace bpmj\wpidea\sales\order;

use bpmj\wpidea\modules\increasing_sales\core\value_objects\Increasing_Sales_Offer_Type;
use bpmj\wpidea\sales\order\cart\Cart_Content;
use bpmj\wpidea\sales\order\client\Client;
use bpmj\wpidea\sales\order\invoice\Invoice;
use bpmj\wpidea\sales\order\additional\Additional_Fields;
use bpmj\wpidea\sales\order\value_objects\Recurring_Payment_Type;
use bpmj\wpidea\sales\order\delivery\Delivery;

class Order
{
    private int $id;
    private int $user_id;
    private ?string $date;
    private Client $client;
    private Invoice $invoice;
    private Cart_Content $cart_content;
    private string $status;
    private string $status_label;
    private float $subtotal;
    private float $total;
    private string $currency;
    private string $gateway;
    private Additional_Fields $additional_fields;
    private ?Discount_Code $discount_code;
    private ?Increasing_Sales_Offer_Type $increasing_sales_offer_type;
    private ?Recurring_Payment_Type $recurring_payment_type;
    private ?Delivery $delivery;

    public function __construct(
        int $post_payment_id,
        int $user_id,
        ?string $date,
        float $subtotal,
        float $total,
        string $currency,
        string $gateway,
        string $status,
        string $status_label,
        Cart_Content $cart_content,
        Client $client,
        Invoice $invoice,
        Additional_Fields $additional_fields,
        ?Discount_Code $discount_code = null,
        ?Increasing_Sales_Offer_Type $increasing_sales_offer_type = null,
        ?Recurring_Payment_Type $recurring_payment = null,
        ?Delivery $delivery = null
    ) {
        $this->id = $post_payment_id;
        $this->user_id = $user_id;
        $this->date = $date;
        $this->subtotal = $subtotal;
        $this->total = $total;
        $this->currency = $currency;
        $this->gateway = $gateway;
        $this->status = $status;
        $this->status_label = $status_label;
        $this->cart_content = $cart_content;
        $this->client = $client;
        $this->invoice = $invoice;
        $this->additional_fields = $additional_fields;
        $this->discount_code = $discount_code;
        $this->increasing_sales_offer_type = $increasing_sales_offer_type;
        $this->recurring_payment_type = $recurring_payment;
        $this->delivery = $delivery;
    }

    public function get_id(): int
    {
        return $this->id;
    }

    public function get_user_id(): int
    {
        return $this->user_id;
    }

    public function get_date(): ?string
    {
        return $this->date;
    }

    public function get_subtotal(): float
    {
        return $this->subtotal;
    }

    public function get_total(): float
    {
        return $this->total;
    }

    public function get_status(): string
    {
        return $this->status;
    }

    public function get_gateway(): string
    {
        return $this->gateway;
    }

    public function get_currency(): string
    {
        return $this->currency;
    }

    public function get_invoice(): Invoice
    {
        return $this->invoice;
    }

    public function get_delivery(): ?Delivery
    {
        return $this->delivery;
    }

    public function get_cart_content(): Cart_Content
    {
        return $this->cart_content;
    }

    public function get_client(): Client
    {
        return $this->client;
    }

    public function get_status_label(): string
    {
        return $this->status_label;
    }

    public function get_additional_fields(): Additional_Fields
    {
        return $this->additional_fields;
    }

    public function get_discount_code(): ?Discount_Code
    {
        return $this->discount_code;
    }

    public function get_increasing_sales_offer_type(): ?Increasing_Sales_Offer_Type
    {
        return $this->increasing_sales_offer_type;
    }

    public function get_recurring_payment_type(): ?Recurring_Payment_Type
    {
        return $this->recurring_payment_type;
    }
}
