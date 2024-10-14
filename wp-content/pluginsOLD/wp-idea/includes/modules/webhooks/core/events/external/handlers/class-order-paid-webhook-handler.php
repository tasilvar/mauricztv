<?php

namespace bpmj\wpidea\modules\webhooks\core\events\external\handlers;

use bpmj\wpidea\events\Event_Name;
use bpmj\wpidea\events\Interface_Event_Handler;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\modules\webhooks\core\factories\Interface_Webhook_Factory;
use bpmj\wpidea\modules\webhooks\core\services\Interface_Webhook_Sender;
use bpmj\wpidea\modules\webhooks\core\services\Webhook_Prepare_Urls;
use bpmj\wpidea\modules\webhooks\core\value_objects\Webhook_Types_Of_Events;
use bpmj\wpidea\sales\order\Interface_Orders_Repository;
use bpmj\wpidea\sales\order\Order;

class Order_Paid_Webhook_Handler implements Interface_Event_Handler
{

    private Interface_Events $events;
    private Interface_Orders_Repository $order_repository;
    private Interface_Webhook_Sender $webhook_sender;
    private Webhook_Prepare_Urls $webhook_prepare_urls;
    private Interface_Webhook_Factory $webhook_factory;

    public function __construct(
        Interface_Events $events,
        Interface_Orders_Repository $order_repository,
        Interface_Webhook_Sender $webhook_sender,
        Webhook_Prepare_Urls $webhook_prepare_urls,
        Interface_Webhook_Factory $webhook_factory
    ) {
        $this->events = $events;
        $this->order_repository = $order_repository;
        $this->webhook_sender = $webhook_sender;
        $this->webhook_prepare_urls = $webhook_prepare_urls;
        $this->webhook_factory = $webhook_factory;
    }


    public function init(): void
    {
        $this->events->on(Event_Name::ORDER_COMPLETED, [$this, 'send_order_details_webhook'], 10, 1);
    }

    public function send_order_details_webhook(int $payment_id): void
    {
        $order = $this->order_repository->find_by_id($payment_id);

        if (!$order) {
            return;
        }

        $data = $this->prepare_data($order);
        $webhook_urls = $this->webhook_prepare_urls->get_urls(Webhook_Types_Of_Events::ORDER_PAID);

        foreach ($webhook_urls as $url) {
            $webhook = $this->webhook_factory->create(Webhook_Types_Of_Events::ORDER_PAID, $url);

            $this->webhook_sender->send_data($webhook, $data);
        }
    }

    private function prepare_data(Order $order): array
    {
        $data = [
            'id' => $order->get_id(),
            'status' => $order->get_status_label(),
            'currency' => $order->get_currency(),
            'date_completed' => $order->get_date(),
            'total' => $order->get_total(),
            'payment_method' => $order->get_gateway(),
            'items' => $this->get_items($order),
            'customer' => [
                'first_name' => $order->get_client()->get_first_name(),
                'last_name' => $order->get_client()->get_last_name(),
                'email' => $order->get_client()->get_email()
            ]
        ];

        $billing_address = $this->get_billing_address_as_array($order);
        if ($billing_address) {
            $data['biling_address'] = $billing_address;
        }

        $additional_fields = $this->get_additional_fields_as_array($order);
        if ($additional_fields) {
            $data['additional_fields'] = $additional_fields;
        }

        return $data;
    }

    private function get_items(Order $order): array
    {
        $items = [];

        foreach ($order->get_cart_content()->get_item_details() as $cart_item_detail) {
            $items[] = [
                'name' => $cart_item_detail['name'],
                'id' => $cart_item_detail['id'],
                'price_id' => $cart_item_detail['item_number']['options']['price_id'] ?? null,
                'quantity' => $cart_item_detail['quantity'],
                'discount' => $cart_item_detail['discount'],
                'subtotal' => $cart_item_detail['subtotal'],
                'price' => $cart_item_detail['price'],
            ];
        }

        return $items;
    }

    private function get_billing_address_as_array(Order $order): array
    {
        $invoice = $order->get_invoice();

        $apartment_number = $invoice->get_invoice_apartment_number();

        if (!$invoice->get_invoice_person_name() && !$invoice->get_invoice_company_name()) {
            return [];
        }

        if ($invoice->get_invoice_person_name()) {
            $data['person_name'] = $invoice->get_invoice_person_name();
        }

        if ($invoice->get_invoice_company_name()) {
            $data['company_name'] = $invoice->get_invoice_company_name();
            $data['tax_id'] = $invoice->get_invoice_nip();
        }

        $data['street'] = $invoice->get_invoice_street();
        $data['building_number'] = $invoice->get_invoice_building_number();

        if($apartment_number){
            $data['apartment_number'] = $apartment_number;
        }

        $data['postal'] = $invoice->get_invoice_postcode();
        $data['city'] = $invoice->get_invoice_city();
        $data['country_code'] = $invoice->get_invoice_country();

        return $data;
    }

    private function get_additional_fields_as_array(Order $order): array
    {
        $additional_fields = [];

        $phone_no = $order->get_client()->get_phone_no();
        $order_additional_fields = $order->get_additional_fields();

        if ($order_additional_fields->get_buy_as_gift()) {
            $additional_fields['buy_as_gift'] = true;
            $additional_fields['voucher_codes'] = $order_additional_fields->get_voucher_codes();
        }

        if ($phone_no) {
            $additional_fields['phone_no'] = $phone_no;
        }

        if ($order_additional_fields->get_checkbox_checked()) {
            $additional_fields['additional_checkbox_checked'] = true;
            $additional_fields['additional_checkbox_description'] = $order_additional_fields->get_checkbox_description(
            );
        }
        if ($order_additional_fields->get_checkbox2_checked()) {
            $additional_fields['additional_checkbox2_checked'] = true;
            $additional_fields['additional_checkbox2_description'] = $order_additional_fields->get_checkbox2_description(
            );
        }
        if ($order_additional_fields->get_order_comment()) {
            $additional_fields['order_comment'] = $order_additional_fields->get_order_comment();
        }

        return $additional_fields;
    }
}
