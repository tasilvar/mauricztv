<?php

declare(strict_types=1);

namespace bpmj\wpidea\modules\affiliate_program\infrastructure\io;

use bpmj\wpidea\modules\affiliate_program\core\io\Interface_Commission_Rules_Provider;
use bpmj\wpidea\modules\affiliate_program\core\value_objects\Commission_Rate;
use bpmj\wpidea\settings\Interface_Settings;
use bpmj\wpidea\admin\settings\Settings_Const;
use bpmj\wpidea\modules\affiliate_program\core\exceptions\Invalid_Commission_Rate_Exception;

class WP_Settings_Commission_Rules_Provider implements Interface_Commission_Rules_Provider
{
    private Interface_Settings $settings;

    public function __construct(
        Interface_Settings $settings
    ) {
        $this->settings = $settings;
    }

    public function get_rate(): Commission_Rate
    {
        try {
            return new Commission_Rate(
                (int)$this->settings->get(Settings_Const::PARTNER_PROGRAM_COMMISSION)
            );
        } catch (Invalid_Commission_Rate_Exception $e) {
            return Commission_Rate::zero();
        }
    }
}