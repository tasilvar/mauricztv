<?php

namespace bpmj\wpidea\modules\captcha\infrastructure\providers;

use bpmj\wpidea\modules\captcha\core\providers\Interface_Captcha_Config_Provider;
use bpmj\wpidea\settings\Interface_Settings;

class Captcha_Config_Provider implements Interface_Captcha_Config_Provider
{
    public const RECAPTCHA_SITE_KEY = 'recaptcha_site_key';
    public const RECAPTCHA_SECRET_KEY = 'recaptcha_secret_key';

    private Interface_Settings $settings;

    public function __construct(Interface_Settings $settings)
    {
        $this->settings = $settings;
    }

    public function is_captcha_enabled(): bool
    {
        return $this->get_site_key() && $this->get_secret_key();
    }

    public function get_site_key(): ?string
    {
        return $this->settings->get(self::RECAPTCHA_SITE_KEY);
    }

    public function get_secret_key(): ?string
    {
        return $this->settings->get(self::RECAPTCHA_SECRET_KEY);
    }
}