<?php

use bpmj\wpidea\modules\opinions\core\client\Interface_Product_Api_Client;
use bpmj\wpidea\modules\opinions\core\providers\Interface_Opinions_Config_Provider;
use bpmj\wpidea\modules\opinions\core\repositories\Interface_Opinion_Repository;
use bpmj\wpidea\modules\opinions\infrastructure\client\Product_Api_Client;
use bpmj\wpidea\modules\opinions\infrastructure\persistence\Interface_Opinions_Persistence;
use bpmj\wpidea\modules\opinions\infrastructure\persistence\Opinions_Persistence;
use bpmj\wpidea\modules\opinions\infrastructure\providers\Opinions_Config_Provider;
use bpmj\wpidea\modules\opinions\infrastructure\repositories\Opinion_Repository;

return [
    Interface_Opinions_Persistence::class => DI\autowire(Opinions_Persistence::class),
    Interface_Opinion_Repository::class => DI\autowire(Opinion_Repository::class),
    Interface_Opinions_Config_Provider::class => DI\autowire(Opinions_Config_Provider::class),
	Interface_Product_Api_Client::class => DI\autowire( Product_Api_Client::class)
];