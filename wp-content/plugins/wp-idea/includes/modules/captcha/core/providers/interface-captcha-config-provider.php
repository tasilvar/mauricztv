<?php

namespace bpmj\wpidea\modules\captcha\core\providers;

interface Interface_Captcha_Config_Provider
{
    public function get_site_key(): ?string;

    public function get_secret_key(): ?string;

    public function is_captcha_enabled(): bool;

}