<?php

declare(strict_types=1);

namespace bpmj\wpidea\modules\increasing_sales\core\services;

use bpmj\wpidea\modules\cart\api\Cart_API;
use bpmj\wpidea\modules\increasing_sales\core\entities\Offer;
use bpmj\wpidea\modules\increasing_sales\core\value_objects\Increasing_Sales_Offer_Type;
use bpmj\wpidea\modules\increasing_sales\core\value_objects\Product_ID;
use bpmj\wpidea\modules\increasing_sales\core\value_objects\Variant_ID;
use bpmj\wpidea\modules\increasing_sales\infrastructure\persistence\Interface_Offers_Persistence;
use bpmj\wpidea\modules\increasing_sales\infrastructure\persistence\Offer_Query_Criteria;
use bpmj\wpidea\sales\product\api\Interface_Product_API;
use bpmj\wpidea\user\api\Interface_User_API;

class Offer_To_Cart_Matcher
{
    private Interface_Offers_Persistence $offers_persistence;
    private Interface_Product_API $product_api;
    private Interface_User_API $user_api;
    private Cart_API $cart_api;

    public function __construct(
        Interface_Offers_Persistence $offers_persistence,
        Cart_API $cart_api,
        Interface_Product_API $product_api,
        Interface_User_API $user_api
    ) {
        $this->offers_persistence = $offers_persistence;
        $this->cart_api = $cart_api;
        $this->product_api = $product_api;
        $this->user_api = $user_api;
    }

    public function get_offer_that_can_be_applied_to_current_cart(): ?Offer
    {
        $offers = $this->offers_persistence->find_by_criteria(new Offer_Query_Criteria());

        $cart_contents = $this->cart_api->get_cart_content();

        if ($cart_contents->is_empty()) {
            return null;
        }

        $current_offer = null;

        foreach ($cart_contents as $item) {
            $id = $item->get_item_product_id() ? $item->get_item_product_id()->to_int() : null;

            if (!$id) {
                continue;
            }

            $id = new Product_ID($id);
            $variant_id = $item->get_item_price_id() ? $item->get_item_price_id()->to_int() : null;
            $variant_id = $variant_id ? new Variant_ID($variant_id) : null;

            foreach ($offers as $offer) {
                $type_upsell = new Increasing_Sales_Offer_Type(Increasing_Sales_Offer_Type::UPSELL);
                $offer_is_upsell = $offer->get_offer_type()->equals($type_upsell);

                $allow_offer_for_same_product_with_different_variant = $offer_is_upsell;
                $offered_product_is_already_in_cart = $this->cart_api->offered_product_is_already_in_cart(
                    $offer->get_offered_product_id()->to_int(),
                    $offer->get_offered_product_variant_id() ? $offer->get_offered_product_variant_id()->to_int() : null,
                    $allow_offer_for_same_product_with_different_variant
                );

                $offered_product_is_disabled_or_sold_out = $this->product_api->offered_product_is_disabled_or_sold_out(
                    $offer->get_offered_product_id(),
                    $offer->get_offered_product_variant_id()
                );

                if ($offered_product_is_disabled_or_sold_out) {
                    continue;
                }

                if ($this->user_has_access_to_the_course_product($offer->get_offered_product_id()->to_int())) {
                    continue;
                }

                if (!$offer->get_product_id()->equals($id)) {
                    continue;
                }

                if (!$variant_id && $offer->get_product_variant_id()) {
                    continue;
                }

                if ($variant_id && (!$offer->get_product_variant_id() || !$offer->get_product_variant_id()->equals($variant_id))) {
                    continue;
                }

                if ($offered_product_is_already_in_cart) {
                    continue;
                }

                $current_offer = $offer;
            }
        }

        return $current_offer;
    }

    public function validate_applied_offer(Offer $offer): bool
    {
        $cart_contents = $this->cart_api->get_cart_content();

        if ($cart_contents->is_empty()) {
            return false;
        }

        foreach ($cart_contents as $item) {
            $id = $item->get_item_product_id() ? $item->get_item_product_id()->to_int() : null;

            if (!$id) {
                continue;
            }

            $id = new Product_ID($id);
            $variant_id = $item->get_item_price_id() ? $item->get_item_price_id()->to_int() : null;
            $variant_id = $variant_id ? new Variant_ID($variant_id) : null;


            $type_bump = new Increasing_Sales_Offer_Type(Increasing_Sales_Offer_Type::BUMP);
            $type_upsell = new Increasing_Sales_Offer_Type(Increasing_Sales_Offer_Type::UPSELL);
            $offer_is_bump = $offer->get_offer_type()->equals($type_bump);
            $offer_is_upsell = $offer->get_offer_type()->equals($type_upsell);

            $allow_offer_for_same_product_with_different_variant = $offer_is_upsell;
            $offered_product_is_in_cart = $this->cart_api->offered_product_is_already_in_cart(
                $offer->get_offered_product_id()->to_int(),
                $offer->get_offered_product_variant_id() ? $offer->get_offered_product_variant_id()->to_int() : null,
                $allow_offer_for_same_product_with_different_variant
            );

            if ($offer_is_bump && !$offer->get_product_id()->equals($id)) {
                continue;
            }

            if ($offer_is_bump && !$variant_id && $offer->get_product_variant_id()) {
                continue;
            }

            if ($offer_is_bump && $variant_id && (!$offer->get_product_variant_id() || !$offer->get_product_variant_id()->equals($variant_id))) {
                continue;
            }

            if (!$offered_product_is_in_cart) {
                continue;
            }

            return true;
        }

        return false;
    }

    private function user_has_access_to_the_course_product(int $product_id): bool
    {
        $current_user_id = $this->user_api->get_current_user_id();

        if (!$current_user_id) {
            return false;
        }

        return $this->product_api->check_if_user_has_access_to_course_product($product_id, $current_user_id->to_int());
    }
}