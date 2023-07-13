<?php

namespace bpmj\wpidea\modules\gus_api\infrastructure\providers;

use bpmj\wpidea\modules\gus_api\core\providers\Interface_Gus_API_Config_Provider;
use bpmj\wpidea\settings\Interface_Settings;

class Gus_API_Config_Provider implements Interface_Gus_API_Config_Provider
{
    private const ENABLED_GUS_API = 'enable_gus_api';

    private Interface_Settings $settings;

    public function __construct(Interface_Settings $settings)
    {
        $this->settings = $settings;
    }

    public function is_enabled(): bool
    {
        return $this->settings->get(self::ENABLED_GUS_API) ?? false;
    }
}