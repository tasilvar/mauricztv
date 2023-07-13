<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\modules\webhooks\core\services;

use bpmj\wpidea\modules\webhooks\core\entities\Webhook;

interface Interface_Webhook_Sender
{
    public function send_data(Webhook $webhook, array $data): ?object;
}