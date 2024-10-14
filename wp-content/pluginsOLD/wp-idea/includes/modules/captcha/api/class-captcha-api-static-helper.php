<?php

namespace bpmj\wpidea\modules\captcha\api;

class Captcha_API_Static_Helper
{
    private static Captcha_API $captcha_api;

    public static function init(Captcha_API $captcha_api): void
    {
        self::$captcha_api = $captcha_api;
    }

    public static function is_captcha_valid(string $captcha): bool
    {
        return self::$captcha_api->is_captcha_valid($captcha);
    }
}