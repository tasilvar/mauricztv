<?php

declare(strict_types=1);

namespace bpmj\wpidea\modules\active_sessions_limiter\core\io;

interface Interface_Sessions_Manager
{
    public function get_active_sessions_count(): int;

    public function destroy_all_sessions(): void;

    public function get_managed_user_id(): int;
}