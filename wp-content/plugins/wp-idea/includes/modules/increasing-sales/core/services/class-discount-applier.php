<?php

declare(strict_types=1);

namespace bpmj\wpidea\modules\increasing_sales\core\services;

use bpmj\wpidea\events\filters\Interface_Filters;
use bpmj\wpidea\helpers\Price_Formatting;
use bpmj\wpidea\modules\increasing_sales\core\entities\Offer;

class Discount_Applier
{
    private const SALE_PRICE = 'get_sale_price';
    private const SALE_PRICE_VARIANT = 'get_sale_price_for_variant';
    private Interface_Filters $filters;
    private Active_Offer_Provider $active_offer_provider;

    public function __construct(
        Interface_Filters $filters,
        Active_Offer_Provider $active_offer_provider
    ) {
        $this->filters = $filters;
        $this->active_offer_provider = $active_offer_provider;
    }

    public function init(): void
    {
        $active_offer = $this->active_offer_provider->get_active_offer();

        if (!$active_offer) {
            return;
        }

        $offered_product_variant_id = $active_offer->get_offered_product_variant_id() ? $active_offer->get_offered_product_variant_id()->to_int() : null;

        if (!$offered_product_variant_id) {
            $this->apply_for_offer($active_offer);
        } else {
            $this->apply_offer_for_variant($active_offer);
        }
    }

    private function apply_for_offer(Offer $offer): void
    {
        $this->filters->add(self::SALE_PRICE, function ($sale_price, $price, $download_id) use ($offer) {
            if (!$download_id) {
                return $sale_price;
            }

            if ((int)$download_id !== $offer->get_offered_product_id()->to_int()) {
                return $sale_price;
            }

            $discount = $offer->get_discount_in_fractions();

            if (!$discount) {
                return $sale_price;
            }

            return $this->get_calculated_price_after_discount((float)$price, $discount);
        }, 10, 3);
    }

    private function apply_offer_for_variant(Offer $offer): void
    {
        if (!$offer->get_discount_in_fractions()) {
            return;
        }

        $this->filters->add(self::SALE_PRICE_VARIANT, function ($prices, $download_id) use ($offer) {
            if (!$download_id) {
                return $prices;
            }

            if ((int)$download_id !== $offer->get_offered_product_id()->to_int()) {
                return $prices;
            }

            $offered_product_variant_id = $offer->get_offered_product_variant_id() ? $offer->get_offered_product_variant_id()->to_int() : null;

            foreach ($prices as $key => $price) {
                if ((int)$key !== $offered_product_variant_id) {
                    continue;
                }

                $regular_price = $price['regular_amount'] ?? $price['amount'];

                $sale_price = $this->get_calculated_price_after_discount((float)$regular_price, $offer->get_discount_in_fractions());

                $prices[$key]['regular_amount'] = $regular_price;
                $prices[$key]['amount'] = $sale_price;
                $prices[$key]['sale_price'] = $sale_price;
            }

            return $prices;
        }, 10, 2);
    }

    private function get_calculated_price_after_discount(float $price, ?int $discount): float
    {
        $discount = Price_Formatting::format_to_float($discount, Price_Formatting::DIVIDE_BY_100);

        if ($price < $discount) {
            return 0.01;
        }
        return ($price - $discount);
    }
}