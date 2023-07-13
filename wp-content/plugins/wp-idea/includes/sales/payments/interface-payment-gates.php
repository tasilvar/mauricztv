<?php
declare(strict_types=1);

namespace bpmj\wpidea\sales\payments;

use bpmj\wpidea\sales\order\Order;

interface Interface_Payment_Gates
{
    public function get_registered_gates(): array;

    public function get_gateway_checkout_label(Order $payment): string;

    public function get_gateway_admin_label(Order $payment): string;
}