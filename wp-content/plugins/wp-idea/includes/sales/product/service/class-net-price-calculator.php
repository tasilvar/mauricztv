<?php

namespace bpmj\wpidea\sales\product\service;

class Net_Price_Calculator
{
    private const DIVIDE_BY_100 = 100;

    public function calculate_net_price(float $gross_price, int $vat_rate): float
    {
        $net_price = round(($gross_price / (1 + ((float)$vat_rate / self::DIVIDE_BY_100))), 2);

        if ($net_price < 0) {
            $net_price = 0.00;
        }

        return (float)number_format($net_price, 2, '.', '');
    }
}