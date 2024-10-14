<?php
declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\payments_history;

use bpmj\wpidea\sales\order\Order;

class Payment_Export_Row_Parser extends Abstract_Payment_Row_Parser
{
    protected const DELIVERY_ADDRESS_SEPARATOR = ', ';

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
            'status_label' => $payment->get_status_label(),
            'total' => $payment->get_total(),
            'discount_code' => $discount_code ? $discount_code->get_value() : '',
            'products' => $this->get_client_products($payment->get_cart_content()),
            'increasing_sales_offer_type' => $increasing_sales_offer_type ? $increasing_sales_offer_type->get_value() : '-',
            'first_checkbox' => $this->get_additional_checkbox_label($additional_fields->get_checkbox_checked()),
            'second_checkbox' => $this->get_additional_checkbox_label($additional_fields->get_checkbox2_checked()),
            'payment_method' => $this->payment_gates->get_gateway_checkout_label($payment),
            'recurring_payment' => $this->get_recurring_payment($payment),
        ]);
    }
}