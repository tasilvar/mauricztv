<?php

use bpmj\wpidea\modules\gus_api\core\services\{Interface_Site_Info_Getter, Site_Info_Getter};
use bpmj\wpidea\modules\gus_api\core\providers\Interface_Gus_API_Config_Provider;
use bpmj\wpidea\modules\gus_api\infrastructure\providers\Gus_API_Config_Provider;

return [
    Interface_Site_Info_Getter::class => DI\autowire(Site_Info_Getter::class),
    Interface_Gus_API_Config_Provider::class => DI\autowire(Gus_API_Config_Provider::class)
];
