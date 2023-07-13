<?php

use bpmj\wpidea\modules\captcha\core\providers\{Interface_Captcha_Config_Provider, Interface_Captcha_Provider};
use bpmj\wpidea\modules\captcha\infrastructure\providers\{Captcha_Config_Provider, Captcha_Provider};

return [
    Interface_Captcha_Provider::class => DI\autowire(Captcha_Provider::class),
    Interface_Captcha_Config_Provider::class => DI\autowire(Captcha_Config_Provider::class)
];