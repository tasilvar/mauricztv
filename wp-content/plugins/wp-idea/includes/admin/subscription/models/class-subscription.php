<?php

namespace bpmj\wpidea\admin\subscription\models;

use bpmj\wpidea\options\Interface_Options;
use bpmj\wpidea\Packages;
use bpmj\wpidea\Software_License;
use bpmj\wpidea\Software_Variant_Core;

class Subscription
{
    private Packages $packages;
    private $type;

    private $expiration_date;
    private $subscription_system_data;
    private $software_instance_type;
    private $software_variant;
    private $license;
    private $options;

    private const SUBSCRIPTION_INTERVAL_YEAR = 'YEAR';
    private const SUBSCRIPTION_INTERVAL_MONTH = 'MONTH';

    private const RENEW_URL_BASE = [
        self::SUBSCRIPTION_INTERVAL_MONTH => 'https://upsell.pl/zamowienie/?edd_action=add_to_cart&download_id=31761&edd_options[price_id]=',
        self::SUBSCRIPTION_INTERVAL_YEAR => 'https://upsell.pl/zamowienie/?edd_action=add_to_cart&download_id=48812&edd_options[price_id]='
    ];
    private const RENEW_URL_PRICE_ID = [
        Subscription_Const::PLAN_START => 13,
        Subscription_Const::PLAN_PLUS => 14,
        Subscription_Const::PLAN_PRO => 15,
    ];

    public function __construct(Subscription_Expiration_Date $subscription_expiration_date,
        Interface_Readable_Subscription_System_Data $subscription_system_data,
        Software_Instance_Type $software_instance_type,
        Software_Variant_Core $software_variant,
        License $license,
        Interface_Options $options,
        Packages $packages)
    {
        $this->subscription_system_data = $subscription_system_data;
        $this->expiration_date = $subscription_expiration_date;
        $this->software_instance_type = $software_instance_type;
        $this->software_variant = $software_variant;
        $this->license = $license;
        $this->options = $options;
        $this->packages = $packages;

        $this->load_type_from_software_variant();
    }

    private function load_type_from_software_variant(): void
    {
        $this->type = $this->software_variant->is_saas() ? Subscription_Const::TYPE_GO : Subscription_Const::TYPE_BOX;
    }

    public function get_expiration_date(): Subscription_Expiration_Date
    {
        return $this->expiration_date;
    }

    public function get_type(): int
    {
        return $this->type;
    }

    public function is_go(): bool
    {
        return $this->get_type() === Subscription_Const::TYPE_GO;
    }

    public function get_id(): string
    {
        return $this->subscription_system_data->get('id') ?? ($this->license->get_key() ? md5($this->license->get_key()) : '');
    }

    public function get_subscriber_email(): string
    {
        return $this->subscription_system_data->get('email') ?? ''; // anonymous for BOX
    }

    public function get_full_name(): string
    {
        return ($this->is_go() ? Subscription_Const::TYPE_SAAS_NAME : Subscription_Const::TYPE_ON_PREMISE_NAME) . ' ' . strtoupper($this->license->get_type());
    }

    public function get_status(): string
    {
        if($this->software_instance_type->is_dev()) {
            return Subscription_Const::STATUS_DEV;
        }

        if($this->is_go() && $this->software_instance_type->is_trial()) {
            return Subscription_Const::STATUS_TRIALING;
        }

        if(!$this->expiration_date->is_exceeded()) {
            return Subscription_Const::STATUS_ACTIVE;
        }

        return Subscription_Const::STATUS_CANCELLED;
    }

    public function get_value(): float
    {
        $subscription_value = Subscription_Const::BASE_VALUE_START;

        switch($this->license->get_type()) {
            case Subscription_Const::PLAN_PLUS:
                $subscription_value = Subscription_Const::BASE_VALUE_PLUS;
                break;
            case Subscription_Const::PLAN_PRO:
                $subscription_value = Subscription_Const::BASE_VALUE_PRO;
                break;
        }

        return !$this->is_go() ? $subscription_value : $this->recalculate_value_for_saas($subscription_value);
    }

    private function recalculate_value_for_saas(int $value): int
    {
        $subscription_value = $value / 10.0;
        $subscription_value -= 2.7;
        return (int)$subscription_value;
    }

    public function get_interval(): string
    {
        $subscription_interval = self::SUBSCRIPTION_INTERVAL_YEAR;

        if($this->is_go()) {
            $subscription_interval = self::SUBSCRIPTION_INTERVAL_MONTH;
        }

        return $subscription_interval;
    }

    public function get_trial_start_timestamp(): int
    {
        return $this->subscription_system_data->get('created') ?? $this->options->get('wpi_first_installation_timestamp') ?? 0;
    }

    public function get_trial_end_timestamp(): int
    {
        return $this->subscription_system_data->get('to') ?? $this->options->get('wpi_first_installation_timestamp') ?? 0;
    }

    public function get_plan(): string
    {
        return $this->license->get_type();
    }

    private function remove_schema_from_url(string $url): string
    {
        return preg_replace("/^(https?:\/\/)/", "", $url);
    }

    public function get_renew_url(string $domain): string
    {
        $domain = $this->remove_schema_from_url($domain);
        $link_base = self::RENEW_URL_BASE[$this->get_interval()] ?? '';
        $uid = $this->get_id();
        $price_id = (string)(self::RENEW_URL_PRICE_ID[$this->get_plan()] ?? '');

        return $link_base . $price_id . '&go_uid=' . $uid . '&go_domain=' . $domain;
    }

    public function get_license_key(): string
    {
        return $this->license->get_key();
    }

    public function has_access_to_for_active_license(string $future): bool
    {
        return Software_License::is_valid() && $this->packages->has_access_to_feature($future);
    }
}
