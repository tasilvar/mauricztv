<?php

namespace bpmj\wpidea\modules\increasing_sales\api;

use bpmj\wpidea\admin\settings\Settings_Const;
use bpmj\wpidea\admin\subscription\models\Subscription;
use bpmj\wpidea\admin\subscription\models\Subscription_Const;
use bpmj\wpidea\modules\cart\api\Cart_API;
use bpmj\wpidea\modules\increasing_sales\core\services\Increasing_Sales;
use bpmj\wpidea\settings\Interface_Settings;
use bpmj\wpidea\sales\product\api\Interface_Product_API;
use bpmj\wpidea\user\api\Interface_User_API;

class Increasing_Sales_API
{
    private Increasing_Sales $increasing_sales;
    private Subscription $subscription;
    private Interface_Settings $settings;

    public function __construct(
        Increasing_Sales $increasing_sales,
        Subscription $subscription,
        Interface_Settings $settings
    ) {
        $this->increasing_sales = $increasing_sales;
        $this->subscription = $subscription;
        $this->settings = $settings;
    }

    public function render_offers(): string
    {
        if(!$this->is_active_increasing_sales_module()){
            return '';
        }

        return $this->increasing_sales->render_offers();
    }

    private function is_active_increasing_sales_module(): bool
    {
        return $this->subscription->get_plan() === Subscription_Const::PLAN_PRO &&
            $this->settings->get(Settings_Const::INCREASING_SALES_ENABLED);
    }
}