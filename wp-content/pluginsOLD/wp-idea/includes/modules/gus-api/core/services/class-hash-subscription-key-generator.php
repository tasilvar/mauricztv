<?php

namespace bpmj\wpidea\modules\gus_api\core\services;

use bpmj\wpidea\admin\subscription\api\Interface_Subscription_API;

class Hash_Subscription_Key_Generator
{
    private Interface_Subscription_API $subscription_api;

    public function __construct(
        Interface_Subscription_API $subscription_api
    ) {
        $this->subscription_api = $subscription_api;
    }

    public function get_hash_subscription_key_by_type(): string
    {
        $value = $this->subscription_api->get_license_key();

        if ($this->subscription_api->is_go()) {
            $value = (defined('BPMJ_EDDCM_MAILER_SMTP_USERNAME') && BPMJ_EDDCM_MAILER_SMTP_USERNAME) ? BPMJ_EDDCM_MAILER_SMTP_USERNAME : '';
        }

        return $this->generate_hash_key($value);
    }

    private function generate_hash_key(string $value): string
    {
        return md5($value);
    }
}
