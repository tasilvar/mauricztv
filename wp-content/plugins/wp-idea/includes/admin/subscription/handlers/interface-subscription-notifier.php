<?php

namespace bpmj\wpidea\admin\subscription\handlers;

use bpmj\wpidea\admin\subscription\models\Subscription;

interface Interface_Subscription_Notifier
{
    public function init(Subscription $subscription): void;
}
