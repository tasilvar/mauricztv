<?php

namespace bpmj\wpidea\events\handlers;

use bpmj\wpidea\admin\subscription\api\Interface_Subscription_API;
use bpmj\wpidea\events\actions\Action_Name;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\events\Interface_Event_Handler;
use bpmj\wpidea\Packages;
use bpmj\wpidea\settings\Interface_Settings;

class Check_Available_Features_Handler implements Interface_Event_Handler
{
    private const OFF = 'off';
    private const ENABLED_GUS_API = 'enable_gus_api';

    private Interface_Actions $actions;
    private Interface_Settings $settings;
    private Interface_Subscription_API $subscription_api;

    public function __construct(
        Interface_Actions $actions,
        Interface_Settings $settings,
        Interface_Subscription_API $subscription_api
    ) {
        $this->actions = $actions;
        $this->settings = $settings;
        $this->subscription_api = $subscription_api;
    }

    public function init(): void
    {
        $this->actions->add(Action_Name::ADMIN_INIT, [$this, 'check_available_features']);
    }

    public function check_available_features(): void
    {
        if (!$this->subscription_api->has_access_to_for_active_license(Packages::FEAT_GUS_API)) {
            $this->settings->set(self::ENABLED_GUS_API, self::OFF);
        }
    }
}
