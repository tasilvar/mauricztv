<?php

namespace bpmj\wpidea\modules\captcha\api;

use bpmj\wpidea\modules\captcha\core\providers\Interface_Captcha_Config_Provider;
use bpmj\wpidea\modules\captcha\core\services\Captcha_Checker;

class Captcha_API
{
    private Interface_Captcha_Config_Provider $captcha_config_provider;
    private Captcha_Checker $captcha_checker;

    public function __construct(
        Interface_Captcha_Config_Provider $captcha_config_provider,
        Captcha_Checker $captcha_checker
    ) {
        $this->captcha_config_provider = $captcha_config_provider;
        $this->captcha_checker = $captcha_checker;
    }

    public function is_captcha_valid(string $captcha): bool
    {
        if (!$this->captcha_config_provider->is_captcha_enabled()) {
            return true;
        }

        return $this->captcha_checker->is_captcha_valid($captcha);
    }
}