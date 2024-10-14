<?php
declare(strict_types=1);

namespace bpmj\wpidea\modules\webhooks\core\services;

interface Interface_Webhook_Registration_Service
{
    public function subscribe(string $name, string $url): bool;

    public function unsubscribe(string $name, string $url): bool;
}