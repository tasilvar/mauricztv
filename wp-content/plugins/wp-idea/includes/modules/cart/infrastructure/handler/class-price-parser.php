<?php

namespace bpmj\wpidea\modules\cart\infrastructure\handler;

class Price_Parser
{
    public function get_parsed_price_gross_from_label(string $label): float
    {
        $prices_without_html_string = preg_replace('/<[^>]*>/', ' ', str_replace(' ', '', $label));

        $prices = [];
        preg_match_all('/([0-9]+(\.[0-9]{2})?)([^ ]+)/', $prices_without_html_string, $prices);

        return !empty($prices[0][0]) ? (float)$prices[0][0] : 0;
    }
}