<?php

namespace bpmj\wpidea\modules\affiliate_program\core\services;

use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\modules\affiliate_program\Affiliate_Program_Module;
use bpmj\wpidea\modules\affiliate_program\core\io\Interface_Commission_Rules_Provider;
use bpmj\wpidea\modules\affiliate_program\core\io\Interface_Orders_Client;
use bpmj\wpidea\modules\affiliate_program\core\repositories\Interface_Commission_Repository;

class Commission_Collector
{
    private Interface_Events $events;
    private Commissions_Calculator $commissions_calculator;
    private Interface_Orders_Client $orders_client;
    private Interface_Commission_Rules_Provider $commission_rules_provider;
    private Interface_Commission_Repository $commission_repository;

    public function __construct(
        Interface_Events $events,
        Commissions_Calculator $commissions_calculator,
        Interface_Orders_Client $orders_client,
        Interface_Commission_Rules_Provider $commission_rules_provider,
        Interface_Commission_Repository $commission_repository
    ) {
        $this->events = $events;
        $this->commissions_calculator = $commissions_calculator;
        $this->orders_client = $orders_client;
        $this->commission_rules_provider = $commission_rules_provider;
        $this->commission_repository = $commission_repository;
    }

    public function collect_for_order(int $payment_id): void
    {
        $order = $this->orders_client->get_order($payment_id);

        if (!$order) {
            return;
        }

        $current_commission_rate = $this->commission_rules_provider->get_rate();

        $commission = $this->commissions_calculator->calculate_commission($order, $current_commission_rate);

        if (!$commission) {
            return;
        }

        $this->commission_repository->create($commission);

        $this->events->emit(
            Affiliate_Program_Module::COMMISION_ATTRIBUTED,
            $order->get_partner(),
            $order->get_id()
        );
    }
}