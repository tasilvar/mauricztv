<?php

declare(strict_types=1);

namespace bpmj\wpidea\modules\active_sessions_limiter\core\io;

interface Interface_Settings_Provider
{
    public function is_active_sessions_limiter_enabled(): bool;

    public function get_max_active_sessions_number(): int;
}