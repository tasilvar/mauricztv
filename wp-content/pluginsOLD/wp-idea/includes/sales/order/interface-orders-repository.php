<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\sales\order;

interface Interface_Orders_Repository
{
    public function find_by_id(int $id): ?Order;

    public function find_by_criteria(Order_Query_Criteria $criteria): Order_Collection;

    public function count_by_criteria(Order_Query_Criteria $criteria): int;

    public function remove(int $id): void;

    public function store_meta(Order $order, string $key, string $value): void;

    public function get_meta(Order $order, string $key): string;
}
