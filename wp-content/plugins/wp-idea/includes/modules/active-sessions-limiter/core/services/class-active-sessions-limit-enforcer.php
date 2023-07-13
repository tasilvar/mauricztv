<?php

declare(strict_types=1);

namespace bpmj\wpidea\modules\active_sessions_limiter\core\services;

use bpmj\wpidea\modules\active_sessions_limiter\core\io\Interface_Sessions_Manager;
use bpmj\wpidea\events\Interface_Event_Emitter;
use bpmj\wpidea\modules\active_sessions_limiter\core\events\Event_Name;
use bpmj\wpidea\modules\active_sessions_limiter\core\value_objects\Limiter_Settings;

class Active_Sessions_Limit_Enforcer
{
    private Limiter_Settings $limiter_settings;
    private Interface_Sessions_Manager $sessions_manager;
    private ?Interface_Event_Emitter $event_emitter;

    private function __construct(
        Limiter_Settings $limiter_settings,
        Interface_Sessions_Manager $sessions_manager,
        Interface_Event_Emitter $event_emitter = null
    )
    {
        $this->limiter_settings = $limiter_settings;
        $this->sessions_manager = $sessions_manager;
        $this->event_emitter = $event_emitter;
    }

    public static function create(
        Limiter_Settings $limiter_settings,
        Interface_Sessions_Manager $sessions_manager,
        Interface_Event_Emitter $event_emitter = null
    ): Active_Sessions_Limit_Enforcer {
        return new self($limiter_settings, $sessions_manager, $event_emitter);
    }

    public function enforce(): void
    {
        $active_sessions = $this->sessions_manager->get_active_sessions_count();
        $max_sessions = $this->limiter_settings->get_max_active_sessions_number();

        $is_limit_reached = $active_sessions >= $max_sessions;

        if(!$is_limit_reached) {
            return;
        }

        $this->sessions_manager->destroy_all_sessions();

        $this->emit_event(
            Event_Name::USER_LOGGED_IN_EXCEEDING_ACTIVE_SESSIONS_LIMIT,
            $this->sessions_manager->get_managed_user_id(),
            $active_sessions
        );
    }

    private function emit_event(string $event_name, ...$args): void
    {
        if (!isset($this->event_emitter)) {
            return;
        }

        $this->event_emitter->emit($event_name, ...$args);
    }
}