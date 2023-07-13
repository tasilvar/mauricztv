<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\sales\order\api;

use bpmj\wpidea\sales\order\api\dto\Order_DTO;
use bpmj\wpidea\sales\order\api\dto\Order_To_DTO_Mapper;
use bpmj\wpidea\sales\order\Interface_Orders_Repository;

class Order_API implements Interface_Order_API
{
    private Interface_Orders_Repository $order_repository;
    private Order_To_DTO_Mapper $order_to_DTO_mapper;

    public function __construct(
        Interface_Orders_Repository $order_repository,
        Order_To_DTO_Mapper $order_to_DTO_mapper
    ) {
        $this->order_repository = $order_repository;
        $this->order_to_DTO_mapper = $order_to_DTO_mapper;
    }

    public function find(int $payment_id): ?Order_DTO
    {
        $order = $this->order_repository->find_by_id($payment_id);

        if (!$order) {
            return null;
        }

        return $this->order_to_DTO_mapper->map($order);
    }

    public function get_meta(Order_DTO $order_dto, string $key)
    {
        $order = $this->order_repository->find_by_id($order_dto->get_id());

        if (!$order) {
            return null;
        }

        return $this->order_repository->get_meta($order, $key);
    }

    public function store_meta(Order_DTO $order_dto, string $key, string $value): void
    {
        $order = $this->order_repository->find_by_id($order_dto->get_id());

        if (!$order) {
            return;
        }

        $this->order_repository->store_meta($order, $key, $value);
    }
}