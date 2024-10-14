<?php

declare(strict_types=1);

namespace bpmj\wpidea\modules\affiliate_program\infrastructure\io;

use bpmj\wpidea\modules\affiliate_program\core\entities\Order;
use bpmj\wpidea\modules\affiliate_program\core\io\Interface_Orders_Client;
use bpmj\wpidea\modules\affiliate_program\core\repositories\Interface_Partner_Repository;
use bpmj\wpidea\modules\affiliate_program\core\value_objects\Affiliate_ID;
use bpmj\wpidea\sales\order\cart\Cart_Content;
use bpmj\wpidea\sales\order\Interface_Orders_Repository;

class Orders_Client implements Interface_Orders_Client
{
    private const PAYMENT_AFFILIATE_ID_META_KEY = 'publigo_afp_id';
    private const PAYMENT_COMPAIGN_NAME_META_KEY = 'publigo_afp_campaign_name';

    private Interface_Orders_Repository $orders_repository;
    private Interface_Partner_Repository $partner_repository;

    public function __construct(
        Interface_Orders_Repository $orders_repository,
        Interface_Partner_Repository $partner_repository
    ) {
        $this->orders_repository = $orders_repository;
        $this->partner_repository = $partner_repository;
    }

    public function get_order(int $order_id): ?Order
    {
        $order = $this->orders_repository->find_by_id($order_id);

        if (!$order) {
            return null;
        }

        $affiliate_id = new Affiliate_ID(
            $this->orders_repository->get_meta($order, self::PAYMENT_AFFILIATE_ID_META_KEY)
        );
        $partner = $this->partner_repository->find_by_affiliate_id($affiliate_id);

        $campaign = $this->orders_repository->get_meta($order, self::PAYMENT_COMPAIGN_NAME_META_KEY);

        return Order::create(
            $order_id,
            $order->get_total(),
            $order->get_client()->get_full_name(),
            $order->get_client()->get_email(),
            $this->get_product_ids_from_cart($order->get_cart_content()),
            new \DateTimeImmutable($order->get_date()),
            $partner,
            $campaign
        );
    }

    private function get_product_ids_from_cart(Cart_Content $cart): array
    {
        $items = [];

        foreach ($cart->get_item_details() as $cart_item_detail) {
            $items[] = $cart_item_detail['id'];
        }

        return $items;
    }
}
