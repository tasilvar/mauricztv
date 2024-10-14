<?php

use bpmj\wpidea\modules\search_engine\core\clients\access\Interface_Access_Module_Client;
use bpmj\wpidea\modules\search_engine\core\clients\sales\Interface_Sales_Module_Client;
use bpmj\wpidea\modules\search_engine\core\services\Interface_Search_Engine;
use bpmj\wpidea\modules\search_engine\core\services\Search_Engine;
use bpmj\wpidea\modules\search_engine\infrastructure\clients\access\Access_Module_Client;
use bpmj\wpidea\modules\search_engine\infrastructure\clients\sales\Sales_Module_Client;

return [
    Interface_Search_Engine::class => DI\autowire(Search_Engine::class),
    Interface_Sales_Module_Client::class => DI\autowire(Sales_Module_Client::class),
    Interface_Access_Module_Client::class => DI\autowire(Access_Module_Client::class)
];