<?php

declare(strict_types=1);

namespace bpmj\wpidea\modules\active_sessions_limiter\core\events;

final class Event_Name
{
    public const USER_LOGGED_IN_EXCEEDING_ACTIVE_SESSIONS_LIMIT = 'user_logged_in_exceeding_active_sessions_limit';
}