<?php

namespace bpmj\wpidea\modules\google_analytics\api;

use bpmj\wpidea\modules\google_analytics\core\providers\Interface_Google_Analytics_Config_Provider;
use bpmj\wpidea\modules\google_analytics\core\services\Hash_User_ID_Generator;

class Google_Analytics_API
{
    private Interface_Google_Analytics_Config_Provider $google_analytics_config_provider;
    private Hash_User_ID_Generator $hash_user_id_generator;

    public function __construct(
        Interface_Google_Analytics_Config_Provider $google_analytics_config_provider,
        Hash_User_ID_Generator $hash_user_id_generator
    ) {
        $this->google_analytics_config_provider = $google_analytics_config_provider;
        $this->hash_user_id_generator = $hash_user_id_generator;
    }

    public function is_ga4_enabled(): bool
    {
        return $this->google_analytics_config_provider->is_ga4_enabled();
    }

    public function get_ga4_id(): ?string
    {
        return $this->google_analytics_config_provider->get_ga4_id();
    }

    public function get_user_hashed_id_for_logged_in_user(): ?string
    {
        return $this->hash_user_id_generator->get_current_user_id_hash();
    }

    public function get_ga4_debug_view(): bool
    {
        return $this->google_analytics_config_provider->get_ga4_debug_view();
    }
}