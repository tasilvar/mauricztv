<?php

namespace bpmj\wpidea\admin\subscription\handlers;

use bpmj\wpidea\admin\subscription\models\Subscription;

class Subscription_Notifier_Initiator
{
    public function __construct(Subscription $subscription)
    {
        $notifier = $subscription->is_go() ? new Subscription_Notifier_Go() : new Subscription_Notifier_Box();
        $notifier->init($subscription);
    }


}
