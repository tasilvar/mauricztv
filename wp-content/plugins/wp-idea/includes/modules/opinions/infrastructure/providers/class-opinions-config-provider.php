<?php

namespace bpmj\wpidea\modules\opinions\infrastructure\providers;

use bpmj\wpidea\admin\settings\Settings_Const;
use bpmj\wpidea\modules\opinions\core\providers\Interface_Opinions_Config_Provider;
use bpmj\wpidea\Packages;
use bpmj\wpidea\settings\Interface_Settings;

class Opinions_Config_Provider implements Interface_Opinions_Config_Provider
{
    private Interface_Settings $settings;
    private Packages $packages;

    public function __construct(
        Interface_Settings $settings,
        Packages $packages
    ) {
        $this->settings = $settings;
        $this->packages = $packages;
    }

    public function is_enabled(): bool
    {
        return ($this->packages->has_access_to_feature(Packages::FEAT_OPINIONS)
            && $this->settings->get(Settings_Const::ENABLE_OPINIONS)
            && $this->settings->get(Settings_Const::OPINIONS_RULES)) ?? false;
    }
}