<?php

use bpmj\wpidea\modules\webhooks\core\factories\Interface_Webhook_Factory;
use bpmj\wpidea\modules\webhooks\core\factories\Webhook_Factory;
use bpmj\wpidea\modules\webhooks\core\repositories\Interface_Webhook_Repository;
use bpmj\wpidea\modules\webhooks\core\services\Interface_Webhook_Registration_Service;
use bpmj\wpidea\modules\webhooks\core\services\Interface_Webhook_Sender;
use bpmj\wpidea\modules\webhooks\core\services\Webhook_Registration_Service;
use bpmj\wpidea\modules\webhooks\core\services\Webhook_Sender;
use bpmj\wpidea\modules\webhooks\infrastructure\persistence\Interface_Webhooks_Persistence;
use bpmj\wpidea\modules\webhooks\infrastructure\persistence\Webhooks_Persistence;
use bpmj\wpidea\modules\webhooks\infrastructure\repositories\Webhook_Repository;

return [
    Interface_Webhooks_Persistence::class => DI\autowire(Webhooks_Persistence::class),
    Interface_Webhook_Repository::class => DI\autowire(Webhook_Repository::class),
    Interface_Webhook_Registration_Service::class => DI\autowire(Webhook_Registration_Service::class),
    Interface_Webhook_Factory::class => DI\autowire(Webhook_Factory::class),
    Interface_Webhook_Sender::class => DI\autowire(Webhook_Sender::class)
];
