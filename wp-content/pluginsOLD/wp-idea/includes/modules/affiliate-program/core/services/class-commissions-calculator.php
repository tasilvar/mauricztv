<?php

namespace bpmj\wpidea\modules\affiliate_program\core\services;

use bpmj\wpidea\helpers\Price_Formatting;
use bpmj\wpidea\modules\affiliate_program\core\entities\Commission;
use bpmj\wpidea\modules\affiliate_program\core\entities\Order;
use bpmj\wpidea\modules\affiliate_program\core\value_objects\Commission_Rate;
use bpmj\wpidea\modules\affiliate_program\core\value_objects\Status;

class Commissions_Calculator
{
    public function calculate_commission(Order $order, Commission_Rate $commission_rate): ?Commission
    {
        $partner = $order->get_partner();

        if (!$partner) {
            return null;
        }

        $order_total = $order->get_total();

        if ($order_total <= 0) {
            return null;
        }

        $commission_percentage = $commission_rate->get();
        $commission_amount = ($order_total * $commission_percentage / 100);

        return Commission::create(
            null,
            $partner->get_id(),
            $partner->get_affiliate_id()->as_string(),
            $partner->get_email()->get_value(),
            $order->get_client_name(),
            $order->get_client_email(),
            $order->get_purchased_product_ids(),
            $this->amount_to_int($order_total),
            $commission_percentage,
            $this->amount_to_int($commission_amount),
            $order->get_date(),
            new Status(Status::STATUS_UNSETTLED),
            $order->get_campaign()
        );
    }

    private function amount_to_int(float $amount): int
    {
        return Price_Formatting::round_and_format_to_int($amount, Price_Formatting::MULTIPLY_BY_100);
    }
}
