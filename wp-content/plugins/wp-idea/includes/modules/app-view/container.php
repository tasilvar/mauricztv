<?php

use bpmj\wpidea\modules\app_view\infrastructure\providers\App_Info_Provider;
use bpmj\wpidea\modules\app_view\core\providers\Interface_App_Info_Provider;

return [
    Interface_App_Info_Provider::class => DI\autowire(App_Info_Provider::class)
];
