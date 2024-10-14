<?php

namespace bpmj\wpidea\sales\price_history\core\service;

use bpmj\wpidea\sales\price_history\core\model\Historic_Price;

class Decision_Maker
{
    public function should_price_change_be_recorded(
        $old_price,
        $new_price,
        ?float $old_promo_price,
        ?float $new_promo_price,
        ?Historic_Price $last_historic_price
    ): bool {
        if (($new_price === $old_price) && ($new_promo_price === $old_promo_price)) {
            return false;
        }

        $price_didnt_change_since_last_historic_entry = $this->price_didnt_change_since_last_historic_entry(
            $new_price,
            $new_promo_price,
            $last_historic_price
        );

        if ($price_didnt_change_since_last_historic_entry) {
            return false;
        }

        return true;
    }

    private function price_didnt_change_since_last_historic_entry(
        $new_price,
        ?float $new_promo_price,
        ?Historic_Price $last_historic_price
    ): bool {
        if (!$last_historic_price) {
            return false;
        }

        $last_regular_price = $last_historic_price->get_regular_price()->get_value();
        $last_promo_price = $last_historic_price->get_promo_price() ? $last_historic_price->get_promo_price()->get_value() : null;

        $regular_price_didnt_change = $last_regular_price === $new_price;
        $promo_price_didnt_change = $last_promo_price === $new_promo_price;

        return $regular_price_didnt_change && $promo_price_didnt_change;
    }
}