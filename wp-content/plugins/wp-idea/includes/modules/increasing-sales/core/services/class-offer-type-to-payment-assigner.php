<?php

namespace bpmj\wpidea\modules\increasing_sales\core\services;

use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\events\Event_Name;
use bpmj\wpidea\modules\increasing_sales\infrastructure\persistence\Interface_Offers_Persistence;
use bpmj\wpidea\sales\order\Interface_Orders_Repository;

class Offer_Type_To_Payment_Assigner
{
    private const PAYMENT_OFFER_TYPE_META_KEY = 'publigo_increasing_sales_offer_type';

    private Interface_Offers_Persistence $offers_persistence;
    private Interface_Actions $actions;
    private Interface_Orders_Repository $orders_repository;
    private Offer_Cookie_Manager $offer_cookie_manager;

    public function __construct(
        Interface_Offers_Persistence $offers_persistence,
        Interface_Actions $actions,
        Interface_Orders_Repository $orders_repository,
        Offer_Cookie_Manager $offer_cookie_manage
    ) {
        $this->offers_persistence = $offers_persistence;
        $this->actions = $actions;
        $this->orders_repository = $orders_repository;
        $this->offer_cookie_manager = $offer_cookie_manage;
    }

    public function init(): void
    {
        if (!is_null($this->offer_cookie_manager->get_offer_cookie_value())) {
            $this->actions->add(Event_Name::ORDER_CREATED, [$this, 'assign_offer_type_to_payment']);
        }
    }

    public function assign_offer_type_to_payment(int $payment_id): void
    {
        $order = $this->orders_repository->find_by_id($payment_id);

        if (!$order) {
            return;
        }

        $offer_type = $this->get_offer_type();

        if (!$offer_type) {
            return;
        }

        $this->orders_repository->store_meta(
            $order,
            self::PAYMENT_OFFER_TYPE_META_KEY,
            $offer_type
        );
    }

    private function get_offer_type(): ?string
    {
        $id_from_cookie = $this->offer_cookie_manager->get_offer_cookie_value();

        if (!$id_from_cookie) {
            return null;
        }

        $offer = $this->offers_persistence->find_by_id($id_from_cookie);

        if (!$offer) {
            return null;
        }

        return $offer->get_offer_type()->get_value();
    }

}
