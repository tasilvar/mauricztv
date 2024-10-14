<?php
use bpmj\wpidea\modules\learning\notes\infrastructure\repositories\Note_Repository;
use bpmj\wpidea\modules\learning\notes\core\repositories\Interface_Note_Repository;
use bpmj\wpidea\modules\learning\notes\core\services\Note_Service;
use bpmj\wpidea\modules\learning\notes\core\services\Interface_Note_Service;
use bpmj\wpidea\modules\learning\notes\infrastructure\persistence\{ Note_Persistence, Interface_Note_Persistence };

return [
    Interface_Note_Repository::class => DI\autowire(Note_Repository::class),
    Interface_Note_Service::class => DI\autowire(Note_Service::class),
    Interface_Note_Persistence::class => DI\autowire(Note_Persistence::class)
];
