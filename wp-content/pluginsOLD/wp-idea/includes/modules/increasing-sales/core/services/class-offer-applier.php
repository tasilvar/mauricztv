<?php

declare(strict_types=1);

namespace bpmj\wpidea\modules\increasing_sales\core\services;

use bpmj\wpidea\modules\cart\api\Cart_API;
use bpmj\wpidea\modules\increasing_sales\core\entities\Offer;
use bpmj\wpidea\modules\increasing_sales\core\value_objects\Increasing_Sales_Offer_Type;
use bpmj\wpidea\modules\increasing_sales\infrastructure\persistence\Interface_Offers_Persistence;
use bpmj\wpidea\modules\increasing_sales\infrastructure\persistence\Offer_Query_Criteria;


class Offer_Applier
{
    private Interface_Offers_Persistence $offers_persistence;
    private Cart_API $cart_api;
    private Offer_Cookie_Manager $offer_cookie_manager;

    public function __construct(
        Interface_Offers_Persistence $offers_persistence,
        Cart_API $cart_api,
        Offer_Cookie_Manager $offer_cookie_manager
    ) {
        $this->offers_persistence = $offers_persistence;
        $this->cart_api = $cart_api;
        $this->offer_cookie_manager = $offer_cookie_manager;
    }

    public function find_and_apply_offer_by_offer_id(int $offer_id): void
    {
        $offer = $this->offers_persistence->find_by_id($offer_id);

        if (!$offer) {
            return;
        }

        $this->update_offer_type_bump($offer);

        $this->update_offer_type_upsell($offer);

        $this->offer_cookie_manager->set_offer_cookie($offer->get_id()->to_int());
    }

    private function update_offer_type_bump(Offer $offer): void
    {
        $options = $this->get_offered_product_variant_id_in_options_array($offer);

        if ($offer->get_offer_type()->get_value() === Increasing_Sales_Offer_Type::BUMP) {
            $this->cart_api->add($offer->get_offered_product_id()->to_int(), $options);
        }
    }

    private function update_offer_type_upsell(Offer $offer): void
    {
        $options = $this->get_offered_product_variant_id_in_options_array($offer);

        if ($offer->get_offer_type()->get_value() === Increasing_Sales_Offer_Type::UPSELL) {
            $key = $this->get_cart_key_by_product_id($offer->get_product_id()->to_int());
            if ($key === null) {
                return;
            }

            $this->cart_api->remove($key);
            $this->cart_api->add($offer->get_offered_product_id()->to_int(), $options);
        }
    }

    private function get_offered_product_variant_id_in_options_array(Offer $offer): array
    {
        $options = [];

        $offered_product_variant_id = $offer->get_offered_product_variant_id();

        if ($offered_product_variant_id) {
            $options = ['price_id' => $offered_product_variant_id->to_int()];
        }

        return $options;
    }

    private function get_cart_key_by_product_id(int $product_id): ?int
    {
        foreach ($this->cart_api->get_cart_content() as $index => $cart_content) {
            $item_product_id = $cart_content->get_item_product_id() ? $cart_content->get_item_product_id()->to_int() : null;
            if ($item_product_id === $product_id) {
                return $index;
            }
        }

        return null;
    }

}