<?php

declare(strict_types=1);

namespace bpmj\wpidea\modules\meta_conversion_api\infrastructure\io;

use bpmj\wpidea\Current_Request;
use bpmj\wpidea\modules\meta_conversion_api\core\io\Interface_Cookie_Based_Data_Provider;
use bpmj\wpidea\modules\meta_conversion_api\Meta_Conversion_API_Module;

class Cookie_Based_Data_Provider implements Interface_Cookie_Based_Data_Provider
{
    private Current_Request $current_request;

    public function __construct(
        Current_Request $current_request
    ) {
        $this->current_request = $current_request;
    }

    public function get_fbp(): ?string
    {
        if ($this->current_request->cookie_arg_exists(Meta_Conversion_API_Module::COOKIE_FBP_NAME)) {
            return $this->current_request->get_cookie_arg(Meta_Conversion_API_Module::COOKIE_FBP_NAME);
        }

        return null;
    }

    public function get_fbc(): ?string
    {
        if ($this->current_request->cookie_arg_exists(Meta_Conversion_API_Module::COOKIE_FBC_NAME)) {
            return $this->current_request->get_cookie_arg(Meta_Conversion_API_Module::COOKIE_FBC_NAME);
        }

        return null;
    }
}
