<?php

declare(strict_types=1);

namespace bpmj\wpidea\modules\active_sessions_limiter\core\io;

use bpmj\wpidea\modules\active_sessions_limiter\core\value_objects\Limiter_Settings;

interface Interface_Active_Sessions_Limit_Enforcer
{
    public function init(Limiter_Settings $limiter_settings): void;
}