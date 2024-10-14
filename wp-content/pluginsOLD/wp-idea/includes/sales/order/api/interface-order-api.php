<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\sales\order\api;

use bpmj\wpidea\sales\order\api\dto\Order_DTO;

interface Interface_Order_API
{
    public function find(int $payment_id): ?Order_DTO;

    public function get_meta(Order_DTO $order_dto, string $key);

    public function store_meta(Order_DTO $order_dto, string $key, string $value): void;
}