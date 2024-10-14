<?php

namespace bpmj\wpidea\modules\captcha\core\providers;

interface Interface_Captcha_Provider
{
    public function site_verify(string $captcha): array;
}