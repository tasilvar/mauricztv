<?php

namespace bpmj\wpidea\modules\captcha\infrastructure\providers;

use bpmj\wpidea\Current_Request;
use bpmj\wpidea\modules\captcha\core\providers\{Interface_Captcha_Config_Provider, Interface_Captcha_Provider};

class Captcha_Provider implements Interface_Captcha_Provider
{
    private Current_Request $current_request;
    private Interface_Captcha_Config_Provider $captcha_config_provider;

    private const RECAPTCHA_ENDPOINT = 'https://www.google.com/recaptcha/api/siteverify';

    public function __construct(
        Current_Request $current_request,
        Interface_Captcha_Config_Provider $captcha_config_provider
    ) {
        $this->current_request = $current_request;
        $this->captcha_config_provider = $captcha_config_provider;
    }

    public function site_verify(string $captcha): array
    {
        $data = [
            'secret' => $this->captcha_config_provider->get_secret_key(),
            'response' => $captcha,
            'remoteip' => $this->current_request->get_user_ip()
        ];

        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded",
                'method' => 'POST',
                'content' => http_build_query($data)
            ]
        ];

        $context = stream_context_create($options);

        $result_json = file_get_contents(self::RECAPTCHA_ENDPOINT, false, $context);

        return json_decode($result_json, true);
    }
}