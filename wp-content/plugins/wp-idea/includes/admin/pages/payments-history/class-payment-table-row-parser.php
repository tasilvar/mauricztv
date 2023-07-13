<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\payments_history;

use bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug;
use bpmj\wpidea\controllers\admin\Admin_Payment_History_Ajax_Controller;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\sales\order\Order;
use bpmj\wpidea\sales\payments\Interface_Payment_Gates;
use bpmj\wpidea\translator\Interface_Translator;

class Payment_Table_Row_Parser extends Abstract_Payment_Row_Parser
{
    private Interface_Url_Generator $url_generator;

    public function __construct(
        Interface_Url_Generator $url_generator,
        Client_Filter $client_filter,
        Interface_Translator $translator,
        Interface_Payment_Gates $payment_gates
    ) {
        parent::__construct($client_filter, $translator, $payment_gates);
        $this->url_generator = $url_generator;
    }

    public function get_row_data(Order $payment): array
    {
        $client_filtered_array = $this->client_filter->filtered_array($payment);
        $discount_code = $payment->get_discount_code();
        $increasing_sales_offer_type = $payment->get_increasing_sales_offer_type();
        $additional_fields = $payment->get_additional_fields();

        return array_merge($client_filtered_array, [
            'ID' => $payment->get_id(),
            'delivery_address' => $this->get_delivery_address($payment->get_delivery()),
            'date' => $this->get_formatted_date($payment->get_date()),
            'status' => $payment->get_status(),
            'status_label' => $payment->get_status_label(),
            'subtotal' => $payment->get_subtotal(),
            'total' => $payment->get_total(),
            'discount_code' => $discount_code ? $discount_code->get_value() : '-',
            'products' => $this->get_client_products($payment->get_cart_content()),
            'increasing_sales_offer_type' => $increasing_sales_offer_type ? $increasing_sales_offer_type->get_value() : '-',
            'currency' => $payment->get_currency(),
            'details_url' => $this->get_details_url($payment->get_id()),
            'delete_payment_url' => $this->get_delete_url($payment->get_id()),
            'client_profile_url' => $this->get_client_profile_url($payment->get_client()->get_id()),
            'resend_payment_email_url' => $this->get_resend_url($payment->get_id()),
            'first_checkbox' => $this->get_additional_checkbox_label($additional_fields->get_checkbox_checked()),
            'second_checkbox' => $this->get_additional_checkbox_label($additional_fields->get_checkbox2_checked()),
            'payment_method' => $this->payment_gates->get_gateway_checkout_label($payment),
            'recurring_payment' => $this->get_recurring_payment($payment),
        ]);
    }

    private function get_details_url(int $payment_id): string
    {
        return $this->url_generator->generate_admin_page_url('admin.php', [
            'page' => Admin_Menu_Item_Slug::PAYMENTS_HISTORY,
            'view' => 'order-details',
            'id' => $payment_id
        ]);
    }

    private function get_delete_url(int $payment_id): string
    {
        return $this->url_generator->generate(Admin_Payment_History_Ajax_Controller::class, 'delete', [
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create(),
            'id' => $payment_id
        ]);
    }

    private function get_resend_url(int $payment_id): string
    {
        return $this->url_generator->generate(Admin_Payment_History_Ajax_Controller::class, 'resend_email', [
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create(),
            'id' => $payment_id
        ]);
    }

    private function get_client_profile_url(int $client_id): string
    {
        return $this->url_generator->generate_admin_page_url('admin.php', [
            'id' => $client_id,
            'page' => Admin_Menu_Item_Slug::CLIENTS,
            'view' => 'overview'
        ]);
    }
}