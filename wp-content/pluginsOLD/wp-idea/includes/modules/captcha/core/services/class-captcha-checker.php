<?php

namespace bpmj\wpidea\modules\captcha\core\services;

use bpmj\wpidea\modules\captcha\core\providers\Interface_Captcha_Provider;

class Captcha_Checker
{
    private const SCORE = 0.5;
    private Interface_Captcha_Provider $captcha_provider;

    public function __construct(
        Interface_Captcha_Provider $captcha_provider
    ) {
        $this->captcha_provider = $captcha_provider;
    }

    public function is_captcha_valid(string $captcha): bool
    {
        $response = $this->captcha_provider->site_verify($captcha);

        if (!$response['success']) {
            return false;
        }

        if ($response['score'] < self::SCORE) {
            return false;
        }

        return true;
    }
}