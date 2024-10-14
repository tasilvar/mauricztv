<?php

namespace bpmj\wpidea\admin\subscription\handlers;

use bpmj\wpidea\admin\subscription\models\notices\Box_Before_Expiration_Timer_Notice;
use bpmj\wpidea\admin\subscription\models\notices\Box_Expiration_Timer_Notice;
use bpmj\wpidea\admin\subscription\models\Software_Instance_Type;
use bpmj\wpidea\admin\subscription\models\Subscription;
use bpmj\wpidea\admin\subscription\models\Subscription_Expiration_Date;

class Subscription_Notifier_Box  implements Interface_Subscription_Notifier
{
    private $subscription;
    private $expiration_date;

    const DAYS_BEFORE_DISPLAY_NOTICE = 14;

    public function init(Subscription $subscription): void
    {
        $this->subscription = $subscription;
        /** @var Subscription_Expiration_Date expiration_date */
        $this->expiration_date = $subscription->get_expiration_date();
        $this->hooks();
    }

    private function hooks(): void
    {
        add_action('admin_notices', [$this, 'maybe_display_exceeded_licence_notice']);
        add_action('admin_notices', [$this, 'maybe_display_before_exceeded_licence_notice']);
    }


    public function maybe_display_exceeded_licence_notice(): void
    {
        if(!$this->expiration_date->is_exceeded()){
            return;
        }

        if (empty(apply_filters('bpmj_show_license_expired_notice', true))){
            return;
        }

        $expiration_timer_notice = new Box_Expiration_Timer_Notice($this->subscription);
        echo $expiration_timer_notice->get_notice();
    }

    public function maybe_display_before_exceeded_licence_notice(): void
    {

        if($this->is_license_not_activated_yet()){
            return;
        }

        if($this->expiration_date->is_lifetime()){
            return;
        }

        if($this->expiration_date->is_exceeded()){
            return;
        }

       if($this->expiration_date->get_days_left() > self::DAYS_BEFORE_DISPLAY_NOTICE){
           return;
       }

        $expiration_timer_notice = new Box_Before_Expiration_Timer_Notice($this->subscription);
        echo $expiration_timer_notice->get_notice();
    }

    private function is_license_not_activated_yet(): bool
    {
        return empty($this->expiration_date->get());
    }


}
