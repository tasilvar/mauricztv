<?php

namespace bpmj\wpidea\modules\affiliate_program\core\services;

use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\modules\affiliate_program\core\entities\Partner;
use bpmj\wpidea\modules\affiliate_program\core\helpers\Interface_Encoder;
use bpmj\wpidea\modules\affiliate_program\core\io\Interface_Cookie_Based_Data_Provider;
use bpmj\wpidea\modules\affiliate_program\core\repositories\Interface_Partner_Repository;
use bpmj\wpidea\modules\affiliate_program\core\value_objects\Affiliate_ID;
use bpmj\wpidea\sales\order\Interface_Orders_Repository;

class Partner_To_Payment_Assigner
{
    private const MAX_LENGTH_STRING = 50;
    private const PAYMENT_CREATED_HOOK = 'edd_insert_payment';
    private const PAYMENT_AFFILIATE_ID_META_KEY = 'publigo_afp_id';
    private const PAYMENT_COMPAIGN_NAME_META_KEY = 'publigo_afp_campaign_name';

    private Interface_Actions $actions;
    private Interface_Orders_Repository $orders_repository;
    private Interface_Cookie_Based_Data_Provider $cookie_based_data_provider;
    private Interface_Partner_Repository $partner_repository;
    private Interface_Encoder $encoder;

    public function __construct(
        Interface_Actions $actions,
        Interface_Orders_Repository $orders_repository,
        Interface_Cookie_Based_Data_Provider $cookie_based_data_provider,
        Interface_Partner_Repository $partner_repository,
        Interface_Encoder $encoder

    ) {
        $this->actions = $actions;
        $this->orders_repository = $orders_repository;
        $this->cookie_based_data_provider = $cookie_based_data_provider;
        $this->partner_repository = $partner_repository;
        $this->encoder = $encoder;
    }

    public function init(): void
    {
        if (!is_null($this->cookie_based_data_provider->get_affiliate_id())) {
            $this->actions->add(self::PAYMENT_CREATED_HOOK, [$this, 'assign_partner_to_payment']);
        }
    }

    public function assign_partner_to_payment(int $payment_id): void
    {
        $order = $this->orders_repository->find_by_id($payment_id);
        $affiliate_id = $this->get_affiliate_id();

        if ($affiliate_id) {
            $this->orders_repository->store_meta(
                $order,
                self::PAYMENT_AFFILIATE_ID_META_KEY,
                $affiliate_id->get_affiliate_id()->as_string()
            );

            $campaign = $this->get_campaign_name();
            if ($campaign) {
                $this->orders_repository->store_meta(
                    $order,
                    self::PAYMENT_COMPAIGN_NAME_META_KEY,
                    $campaign
                );
            }
        }
    }

    private function get_affiliate_id(): ?Partner
    {
        $provided_affiliate_id = $this->cookie_based_data_provider->get_affiliate_id();

        if (is_null($provided_affiliate_id)) {
            return null;
        }

        $decoded_id_string = $this->encoder->base64_decode($provided_affiliate_id);

        return $this->partner_repository->find_by_affiliate_id(new Affiliate_ID($decoded_id_string));
    }

    private function get_campaign_name(): ?string
    {
        $provided_campaign_name = $this->cookie_based_data_provider->get_campaign_name();

        if (is_null($provided_campaign_name)) {
            return null;
        }

        $campaign_name = $this->encoder->base64_decode($provided_campaign_name);

        return $this->get_slugged_campaign_name($campaign_name);
    }

    private function get_slugged_campaign_name($string): string
    {
        $string = substr($string, 0, self::MAX_LENGTH_STRING);

        return preg_replace("/[^a-zA-Z0-9-_]+/", "", $string);
    }
}
