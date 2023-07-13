<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\sales\order\services;

interface Interface_Orders_Service
{
    public function resend_notification(int $payment_id): void;
    public function revoke(int $order_id): void;
}