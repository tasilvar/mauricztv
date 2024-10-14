<?php

use bpmj\wpidea\modules\active_sessions_limiter\core\io\Interface_Settings_Provider;
use bpmj\wpidea\modules\active_sessions_limiter\infrastructure\io\Settings_Provider;

return [
    Interface_Settings_Provider::class => DI\autowire(Settings_Provider::class)
];
