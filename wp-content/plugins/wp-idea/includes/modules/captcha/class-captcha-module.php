<?php

namespace bpmj\wpidea\modules\captcha;

use bpmj\wpidea\modules\captcha\api\{Captcha_API, Captcha_API_Static_Helper};
use bpmj\wpidea\modules\captcha\core\events\external\handlers\Page_Handler;
use bpmj\wpidea\modules\captcha\core\providers\Interface_Captcha_Config_Provider;
use bpmj\wpidea\shared\abstractions\modules\Interface_Module;

class Captcha_Module implements Interface_Module
{
    private Captcha_API $captcha_api;
    private Interface_Captcha_Config_Provider $captcha_config_provider;
    private Page_Handler $page_handler;

    public function __construct(
        Captcha_API $captcha_api,
        Interface_Captcha_Config_Provider $captcha_config_provider,
        Page_Handler $page_handler
    ) {
        $this->captcha_api = $captcha_api;
        $this->captcha_config_provider = $captcha_config_provider;
        $this->page_handler = $page_handler;
    }

    public function init(): void
    {
        Captcha_API_Static_Helper::init($this->captcha_api);

        if ($this->captcha_config_provider->is_captcha_enabled()) {
            $this->page_handler->init();
        }
    }

    public function get_routes(): array
    {
        return [];
    }

    public function get_translations(): array
    {
        return [];
    }
}