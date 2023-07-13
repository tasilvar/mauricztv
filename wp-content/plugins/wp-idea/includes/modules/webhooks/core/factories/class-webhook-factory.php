<?php
declare(strict_types=1);

namespace bpmj\wpidea\modules\webhooks\core\factories;

use bpmj\wpidea\data_types\Url;
use bpmj\wpidea\modules\webhooks\core\entities\{Webhook};
use bpmj\wpidea\modules\webhooks\core\value_objects\Webhook_Status;
use bpmj\wpidea\modules\webhooks\core\value_objects\Webhook_Types_Of_Events;

class Webhook_Factory implements Interface_Webhook_Factory
{
    public function create($name, $url): Webhook
    {
        $type_of_event = new Webhook_Types_Of_Events($name);
        $url           = new Url($url);
        $status        = new Webhook_Status(Webhook_Status::ACTIVE);

        return new Webhook($type_of_event, $url, $status);
    }
}