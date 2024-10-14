<?php

declare(strict_types=1);

namespace bpmj\wpidea\modules\active_sessions_limiter\infrastructure\io;

use bpmj\wpidea\events\filters\Interface_Filters;
use bpmj\wpidea\modules\active_sessions_limiter\core\value_objects\Limiter_Settings;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\modules\active_sessions_limiter\core\services\Active_Sessions_Limit_Enforcer;

class Active_Sessions_Limit_Enforcer_Initiator
{
    public const AUTH_CHECK_HOOK = 'check_password';

    private Interface_Filters $filters;

    private Limiter_Settings $limiter_settings;
    private Interface_Events $events;

    public function __construct(
        Interface_Filters $filters,
        Interface_Events $events
    )
    {
        $this->filters = $filters;
        $this->events = $events;
    }

    public function init(Limiter_Settings $limiter_settings): void
    {
        $this->limiter_settings = $limiter_settings;

        $this->filters->add(self::AUTH_CHECK_HOOK, [$this, 'init_enforcer_on_login'], 10, 4);
    }

    public function init_enforcer_on_login($check, $password, $hash, $user_id)
    {
        if (!$check) {
            return false;
        }

        if(empty($user_id) || !is_numeric($user_id)) {
            return $check;
        }

        $this->initiate_enforcer($user_id);

        return true;
    }

    private function initiate_enforcer($user_id): void
    {
        $sessions_manager = Sessions_Manager::create_for_user((int)$user_id);

        $enforcer = Active_Sessions_Limit_Enforcer::create($this->limiter_settings, $sessions_manager, $this->events);

        $enforcer->enforce();
    }
}