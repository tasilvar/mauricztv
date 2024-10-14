<?php

declare(strict_types=1);

namespace bpmj\wpidea\modules\increasing_sales\core\services;

use bpmj\wpidea\modules\increasing_sales\core\entities\Offer;
use bpmj\wpidea\modules\increasing_sales\infrastructure\persistence\Interface_Offers_Persistence;

class Active_Offer_Provider
{
    private Offer_Cookie_Manager $offer_cookie_manager;
    private Interface_Offers_Persistence $offers_persistence;
    private Offer_To_Cart_Matcher $offer_to_cart_matcher;

    public function __construct(
        Offer_Cookie_Manager $offer_cookie_manager,
        Interface_Offers_Persistence $offers_persistence,
        Offer_To_Cart_Matcher $offer_to_cart_matcher
    )
    {
        $this->offer_cookie_manager = $offer_cookie_manager;
        $this->offers_persistence = $offers_persistence;
        $this->offer_to_cart_matcher = $offer_to_cart_matcher;
    }

    public function maybe_clean_invalid_cookie(): void
    {
        if($this->get_active_offer()) {
            return;
        }

        $this->offer_cookie_manager->clear_offer_cookie();
    }

    public function get_active_offer(): ?Offer
    {
        $id_from_cookie = $this->offer_cookie_manager->get_offer_cookie_value();

        if (!$id_from_cookie) {
            return null;
        }

        $offer = $this->offers_persistence->find_by_id($id_from_cookie);

        if (!$offer) {
            return null;
        }

        if(!$this->offer_to_cart_matcher->validate_applied_offer($offer)){
            return null;
        }

        return $offer;
    }
}