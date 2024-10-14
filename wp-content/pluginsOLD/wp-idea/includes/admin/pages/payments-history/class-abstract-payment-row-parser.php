<?php
declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\payments_history;

use bpmj\wpidea\sales\order\delivery\Delivery;
use bpmj\wpidea\sales\order\Order;
use bpmj\wpidea\sales\order\cart\Cart_Content;
use bpmj\wpidea\sales\order\value_objects\Recurring_Payment_Type;
use bpmj\wpidea\sales\payments\Interface_Payment_Gates;
use bpmj\wpidea\translator\Interface_Translator;

abstract class Abstract_Payment_Row_Parser
{
    protected const DELIVERY_ADDRESS_SEPARATOR = '<br>';

    protected Client_Filter $client_filter;
    protected Interface_Translator $translator;
    protected Interface_Payment_Gates $payment_gates;
    public function __construct(
        Client_Filter $client_filter,
        Interface_Translator $translator,
        Interface_Payment_Gates $payment_gates
    ) {
        $this->client_filter = $client_filter;
        $this->translator = $translator;
        $this->payment_gates = $payment_gates;
    }

    public function get_parsed_row(Order $payment): array
    {
        return $this->get_row_data($payment);
    }

    abstract public function get_row_data(Order $payment): array;

    protected function get_formatted_date(?string $date): ?string
    {
        if (is_null($date)) {
            return null;
        }

        $date_object = (new \DateTime($date));

        return $date_object->format('Y-m-d H:i:s');
    }

    protected function  get_client_products(Cart_Content $cart): string
    {
        return implode(', ', $cart->get_item_names());
    }

    protected function get_additional_checkbox_label(bool $value): string
    {
        return $value ? $this->translator->translate('orders.column.additional_checkbox.yes') : $this->translator->translate(
            'orders.column.additional_checkbox.no'
        );
    }

    protected function get_recurring_payment(Order $payment): string
    {
        $recurring_payment_type = !is_null($payment->get_recurring_payment_type()) ? $payment->get_recurring_payment_type()->get_value() : Recurring_Payment_Type::RECURRING_PAYMENT_NO;
        return $this->translator->translate('orders.recurring_payment.' . $recurring_payment_type);
    }

    protected function get_delivery_address(?Delivery $delivery): string
    {
        if (!$delivery) {
            return '-';
        }

        $apartment_number = '';

        if ($delivery->get_apartment_number()) {
            $apartment_number = '/' . $delivery->get_apartment_number();
        }

        $company = '';
        if (!empty($delivery->get_receiver_company())) {
            $company = $delivery->get_receiver_company() . static::DELIVERY_ADDRESS_SEPARATOR;
        }

        return $delivery->get_receiver_first_name() . ' ' . $delivery->get_receiver_last_name() . static::DELIVERY_ADDRESS_SEPARATOR .
            $company .
            $delivery->get_street() . ' ' . $delivery->get_building_number() . $apartment_number . static::DELIVERY_ADDRESS_SEPARATOR .
            $delivery->get_postal_code() . ' ' . $delivery->get_city() . static::DELIVERY_ADDRESS_SEPARATOR .
            $delivery->get_phone();
    }
}