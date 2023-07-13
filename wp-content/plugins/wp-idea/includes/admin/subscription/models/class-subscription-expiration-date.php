<?php

namespace bpmj\wpidea\admin\subscription\models;

use bpmj\wpidea\admin\helpers\Date_Helper;
use bpmj\wpidea\settings\LMS_Settings;
use bpmj\wpidea\Software_Variant_Core;
use DateTime;

class Subscription_Expiration_Date
{
    private const TRIAL_EXPIRATION_DATE_SLUG = 'trial_version_expiration_date';
    private const BOX_EXPIRATION_DATE_SLUG = 'bpmj_wpidea_license_expires';
    private const LIFETIME_SLUG = 'lifetime';

    private $subscription_type;
    private $software_variant;


    public function __construct(Software_Variant_Core $software_variant)
    {
        $this->software_variant = $software_variant;
        $this->subscription_type = $software_variant->is_saas() ? Subscription_Const::TYPE_GO : Subscription_Const::TYPE_BOX;
    }


   public function get(): string
    {
        if($this->subscription_type == Subscription_Const::TYPE_GO){
            return LMS_Settings::get_option(self::TRIAL_EXPIRATION_DATE_SLUG, '');
        }

        return License::get_expiration_date();
    }

    public function update(string $expiration_date): void
    {
        if($this->subscription_type == Subscription_Const::TYPE_BOX){
            LMS_Settings::update(self::TRIAL_EXPIRATION_DATE_SLUG, $expiration_date);
        }

        License::set_expiration_date($expiration_date);
    }

    public function is_lifetime(): bool
    {
        return self::LIFETIME_SLUG === $this->get();
    }

    public function is_exceeded(): bool
    {
        $date = $this->get();

        if(!$date){
            return false;
        }

        if(
            $this->subscription_type == Subscription_Const::TYPE_BOX &&
            $this->is_lifetime()
        ){
            return false;
        }

        return Date_Helper::is_date_in_the_past($this->get_as_datetime());
    }

    public function get_exceeded_days(): int
    {
        if(!$this->is_exceeded()){
            return 0;
        }
        return $this->get_difference_from_today();
    }

    public function get_days_left(): int
    {
        if($this->is_exceeded()){
            return 0;
        }
        return $this->get_difference_from_today();
    }

    private function get_difference_from_today(): int
    {
        if(empty($this->get())){
            return 0;
        }

        $today = new DateTime();
        $interval = $today->diff($this->get_as_datetime());

        return $interval->days;
    }

    public function get_as_datetime(): DateTime
    {
        if($this->subscription_type == Subscription_Const::TYPE_BOX){
            return new DateTime($this->get());
        }

        $expiration_date = new DateTime();
        $expiration_date->setTimestamp($this->get());

        return $expiration_date;
    }

    public function get_as_timestamp(): ?int
    {
        return strtotime($this->get());
    }

}
