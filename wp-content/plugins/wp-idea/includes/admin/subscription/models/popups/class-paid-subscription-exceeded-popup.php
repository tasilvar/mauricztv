<?php

namespace bpmj\wpidea\admin\subscription\models\popups;

use bpmj\wpidea\admin\helpers\html\Popup;
use bpmj\wpidea\View;
use bpmj\wpidea\admin\subscription\models\Subscription;

class Paid_Subscription_Exceeded_Popup
{
    private Subscription $subscription;

    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
    }

    public function get_popup(): string
    {
        $domain = get_home_url();

        $popup = Popup::create(
            'paid_subscription_exceeded_popup',
            View::get_admin('/subscription/go/paid_subscription_exceeded_popup', [
                'renew_url' => $this->subscription->get_renew_url($domain)
            ])
        )
            ->auto_open()
            ->show_close_button();

        return $popup->get_html();
    }

}
