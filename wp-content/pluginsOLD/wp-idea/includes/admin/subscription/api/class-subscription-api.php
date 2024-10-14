<?php

namespace bpmj\wpidea\admin\subscription\api;

use bpmj\wpidea\admin\subscription\models\Subscription;
use bpmj\wpidea\data_types\exceptions\Object_Uninitialized_Exception;

class Subscription_API implements Interface_Subscription_API
{
    private static ?Subscription_API $instance = null;
    private Subscription $subscription;

    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;

        self::$instance = $this;
    }

    /**
     * @throws Object_Uninitialized_Exception
     */

    public static function get_instance(): Subscription_API
    {
        if (!isset(self::$instance)) {
            throw new Object_Uninitialized_Exception();
        }

        return self::$instance;
    }

    public function get_plan_for_current_user(): string
    {
        return $this->subscription->get_plan();
    }

    public function is_go(): bool
    {
        return $this->subscription->is_go();
    }

    public function get_license_key(): string
    {
        return $this->subscription->get_license_key();
    }

    public function has_access_to_for_active_license(string $future): bool
    {
        return $this->subscription->has_access_to_for_active_license($future);
    }
}
