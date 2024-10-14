<?php

namespace bpmj\wpidea\admin\subscription\handlers;

use bpmj\wpidea\admin\subscription\models\notices\Trial_Expiration_Timer_Notice;
use bpmj\wpidea\admin\subscription\models\popups\Paid_Subscription_Exceeded_Popup;
use bpmj\wpidea\admin\subscription\models\popups\Trial_Subscription_Exceeded_Popup;
use bpmj\wpidea\admin\subscription\models\Software_Instance_Type;
use bpmj\wpidea\admin\subscription\models\Subscription;

class Subscription_Notifier_Go implements Interface_Subscription_Notifier
{
    private $software_instance_type;
    private $subscription;
    private $expiration_date;

    public function init(Subscription $subscription): void
    {
        $this->software_instance_type = new Software_Instance_Type();
        $this->subscription = $subscription;
        $this->expiration_date = $subscription->get_expiration_date();
        $this->hooks();
    }

    // function intended for testing
    public function set_software_instance_type($software_instance_type): void
    {
        $this->software_instance_type = $software_instance_type;
    }

    private function hooks(): void
    {
        add_action('admin_notices', [$this, 'maybe_display_trial_expiration_timer_notice']);
        add_action('admin_footer', [$this, 'maybe_display_paid_subscription_exceeded_popup']);
        add_action('admin_footer', [$this, 'maybe_display_trial_subscription_exceeded_popup']);
    }

    public function maybe_display_trial_expiration_timer_notice(): void
    {
        if($this->software_instance_type->is_paid()){
            return;
        }

        $trial_expiration_timer_notice = new Trial_Expiration_Timer_Notice($this->subscription);
        echo $trial_expiration_timer_notice->get_notice();
    }

    public function maybe_display_trial_subscription_exceeded_popup(): void
    {
        if(!$this->expiration_date->is_exceeded()){
            return;
        }

        if($this->software_instance_type->is_paid()){
            return;
        }

        $trial_subscription_exceeded_popup = new Trial_Subscription_Exceeded_Popup($this->subscription);
        echo $trial_subscription_exceeded_popup->get_popup();
    }

    public function maybe_display_paid_subscription_exceeded_popup(): void
    {
        if(!$this->expiration_date->is_exceeded()){
            return;
        }

        if(!$this->software_instance_type->is_paid()){
            return;
        }

        $paid_subscription_exceeded_popup = new Paid_Subscription_Exceeded_Popup($this->subscription);
        echo $paid_subscription_exceeded_popup->get_popup();
    }
}
