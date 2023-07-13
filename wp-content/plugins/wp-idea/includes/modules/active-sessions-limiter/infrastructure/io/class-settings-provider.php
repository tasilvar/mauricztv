<?php
declare(strict_types=1);

namespace bpmj\wpidea\modules\active_sessions_limiter\infrastructure\io;

use bpmj\wpidea\admin\settings\Settings_Const;
use bpmj\wpidea\admin\subscription\models\Subscription;
use bpmj\wpidea\admin\subscription\models\Subscription_Const;
use bpmj\wpidea\modules\active_sessions_limiter\core\io\Interface_Settings_Provider;
use bpmj\wpidea\settings\Interface_Settings;
use bpmj\wpidea\modules\active_sessions_limiter\core\value_objects\Limiter_Settings;

class Settings_Provider implements Interface_Settings_Provider
{
    private Interface_Settings $settings;
    private Subscription $subscription;

    public function __construct(
        Interface_Settings $settings,
        Subscription $subscription
    ) {
        $this->settings = $settings;
        $this->subscription = $subscription;
    }

    public function is_active_sessions_limiter_enabled(): bool
    {
        return $this->subscription->get_plan() !== Subscription_Const::PLAN_START && $this->settings->get(Settings_Const::ACTIVE_SESSIONS_LIMITER_ENABLED);
    }

    public function get_max_active_sessions_number(): int
    {
        $max_active_sessions_number = (int) $this->settings->get(Settings_Const::MAX_ACTIVE_SESSIONS_NUMBER);

        if(!$this->is_number_greater_than_zero($max_active_sessions_number)){
            return Limiter_Settings::MINIMUM_MAX_ACTIVE_SESSIONS;
        }

        return $max_active_sessions_number;
    }

    private function is_number_greater_than_zero(int $number): bool
    {
        if(!is_numeric($number) || $number <= 0){
            return false;
        }

        return true;
    }

}