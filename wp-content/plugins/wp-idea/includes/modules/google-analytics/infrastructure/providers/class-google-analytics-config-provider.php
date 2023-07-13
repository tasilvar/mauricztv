<?php

namespace bpmj\wpidea\modules\google_analytics\infrastructure\providers;

use bpmj\wpidea\modules\google_analytics\core\providers\Interface_Google_Analytics_Config_Provider;
use bpmj\wpidea\settings\Interface_Settings;

class Google_Analytics_Config_Provider implements Interface_Google_Analytics_Config_Provider
{
    private const GA4_ID_SETTING_NAME = 'ga4_id';
    private const GA4_DEBUG_VIEW_SETTING_NAME = 'ga4_debug_view';
    private const ON = 'on';
    private Interface_Settings $settings;

    public function __construct(Interface_Settings $settings)
    {
        $this->settings = $settings;
    }

    public function is_ga4_enabled(): bool
    {
        return !empty($this->get_ga4_id());
    }

    public function get_ga4_id(): ?string
    {
        return $this->settings->get(self::GA4_ID_SETTING_NAME);
    }

    public function get_ga4_debug_view(): bool
    {
        $debug_view = $this->settings->get(self::GA4_DEBUG_VIEW_SETTING_NAME);
        
        return $debug_view ?? false;
    }


}