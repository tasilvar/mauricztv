<?php

declare(strict_types=1);

namespace bpmj\wpidea\sales\order\services;

use bpmj\wpidea\sales\order\Interface_Orders_Repository;

class Orders_Service implements Interface_Orders_Service
{
    private Interface_Orders_Repository $orders_repository;

    public function __construct(Interface_Orders_Repository $orders_repository)
    {
        $this->orders_repository = $orders_repository;
    }

    public function resend_notification(int $payment_id): void
    {
        $order = $this->orders_repository->find_by_id($payment_id);
        if (!$order) {
            return;
        }

        $vouchers = $order->get_additional_fields()->get_voucher_codes();

        if (empty($vouchers)) {
            edd_email_purchase_receipt($payment_id, false);
            return;
        }

        $products = $order->get_cart_content()->get_item_details();
        foreach ($products as $product) {
            bpmj_eddcm_process_buy_as_gift($product['id'], $payment_id);
        }
    }

    public function revoke(int $order_id): void
    {
        $order = $this->orders_repository->find_by_id($order_id);
        if (!$order) {
            return;
        }

        $payment = new \EDD_Payment($order_id);
        $payment->update_status('revoked');
        $payment->save();
    }
}