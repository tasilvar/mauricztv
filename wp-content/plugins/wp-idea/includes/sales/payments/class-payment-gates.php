<?php
declare(strict_types=1);

namespace bpmj\wpidea\sales\payments;

use bpmj\wpidea\sales\order\Order;

class Payment_Gates implements Interface_Payment_Gates
{
    public function get_registered_gates(): array
    {
        return edd_get_payment_gateways();
    }

    public function get_gateway_checkout_label(Order $payment): string
    {
        return edd_get_gateway_checkout_label($payment->get_gateway());
    }

    public function get_gateway_admin_label(Order $payment): string
    {
        return edd_get_gateway_admin_label($payment->get_gateway());
    }
}