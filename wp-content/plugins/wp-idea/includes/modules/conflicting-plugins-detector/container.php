<?php

use bpmj\wpidea\modules\conflicting_plugins_detector\infrastructure\io\Plugins_Info_Provider;
use bpmj\wpidea\modules\conflicting_plugins_detector\core\io\Interface_Plugins_Info_Provider;
use bpmj\wpidea\modules\conflicting_plugins_detector\web\Notice_Handler;
use bpmj\wpidea\modules\conflicting_plugins_detector\core\io\Interface_Notice_Handler;

return [
    Interface_Plugins_Info_Provider::class => DI\autowire(Plugins_Info_Provider::class),
    Interface_Notice_Handler::class => DI\autowire(Notice_Handler::class)
];
