<?php

use bpmj\wpidea\modules\videos\core\persistence\Interface_Video_Persistence;
use bpmj\wpidea\modules\videos\core\providers\Interface_Video_Config_Provider;
use bpmj\wpidea\modules\videos\infrastructure\providers\Bunny_Net_Config_Provider;
use bpmj\wpidea\modules\videos\core\providers\Interface_Video_Provider;
use bpmj\wpidea\modules\videos\core\repositories\Interface_Video_Repository;
use bpmj\wpidea\modules\videos\infrastructure\persistence\Video_Wp_Persistence;
use bpmj\wpidea\modules\videos\infrastructure\providers\Bunny_Net_Video_Provider;
use bpmj\wpidea\modules\videos\infrastructure\repositories\Video_Wp_Repository;
use bpmj\wpidea\modules\videos\core\services\Interface_Video_Player_Renderer;
use bpmj\wpidea\modules\videos\core\services\Video_Player_Renderer;

return [
    Interface_Video_Persistence::class => DI\autowire(Video_Wp_Persistence::class),
    Interface_Video_Provider::class => DI\autowire(Bunny_Net_Video_Provider::class),
    Interface_Video_Repository::class => DI\autowire(Video_Wp_Repository::class),
    Interface_Video_Config_Provider::class => DI\autowire(Bunny_Net_Config_Provider::class),
    Interface_Video_Player_Renderer::class => DI\autowire(Video_Player_Renderer::class)
];