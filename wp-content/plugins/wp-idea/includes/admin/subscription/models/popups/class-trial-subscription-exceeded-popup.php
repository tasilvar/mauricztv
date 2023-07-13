<?php

namespace bpmj\wpidea\admin\subscription\models\popups;

use bpmj\wpidea\admin\helpers\html\Popup;
use bpmj\wpidea\admin\subscription\models\Subscription;
use bpmj\wpidea\View;

class Trial_Subscription_Exceeded_Popup
{
    private const PATH_TO_TRIAL_SUBSCRIPTION_EXCEEDED_POPUP = '/subscription/go/trial_subscription_exceeded_popup';

    private $subscription;

    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
    }

    public function get_popup(): string
    {
        $uid = $this->subscription->get_id();
        $popup = Popup::create(
            'paid_subscription_exceeded_popup',
            View::get_admin(self::PATH_TO_TRIAL_SUBSCRIPTION_EXCEEDED_POPUP, [ 'uid' => $uid ] )
        )
            ->auto_open()
            ->show_close_button();

        return $popup->get_html();
    }

}
