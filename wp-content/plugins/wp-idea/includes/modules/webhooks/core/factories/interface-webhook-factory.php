<?php
declare(strict_types=1);

namespace bpmj\wpidea\modules\webhooks\core\factories;

use bpmj\wpidea\modules\webhooks\core\entities\Webhook;

interface Interface_Webhook_Factory
{
    public function create(string $name, string $url): Webhook;
}