<?php

use bpmj\wpidea\modules\google_analytics\core\services\{Interface_Data_Session_Setter,
    Data_Session_Setter
};
use bpmj\wpidea\modules\google_analytics\core\providers\Interface_Google_Analytics_Config_Provider;
use bpmj\wpidea\modules\google_analytics\infrastructure\providers\Google_Analytics_Config_Provider;

return [
    Interface_Data_Session_Setter::class => DI\autowire(Data_Session_Setter::class),
    Interface_Google_Analytics_Config_Provider::class => DI\autowire(Google_Analytics_Config_Provider::class)
];
