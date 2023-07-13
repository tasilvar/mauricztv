<?php

declare(strict_types=1);

namespace bpmj\wpidea\modules\affiliate_program\infrastructure\io;

use bpmj\wpidea\Current_Request;
use bpmj\wpidea\modules\affiliate_program\Affiliate_Program_Module;
use bpmj\wpidea\modules\affiliate_program\core\io\Interface_Cookie_Based_Data_Provider;

class Cookie_Based_Data_Provider implements Interface_Cookie_Based_Data_Provider
{

    private Current_Request $current_request;

    public function __construct(
        Current_Request $current_request
    ) {
        $this->current_request = $current_request;
    }

    public function get_affiliate_id(): ?string
    {
        if ($this->current_request->cookie_arg_exists(Affiliate_Program_Module::COOKIE_NAME)) {
            return $this->current_request->get_cookie_arg(Affiliate_Program_Module::COOKIE_NAME);
        }

        return null;
    }

    public function get_campaign_name(): ?string
    {
        if ($this->current_request->cookie_arg_exists(Affiliate_Program_Module::COOKIE_CAMPAIGN_NAME)) {
            return $this->current_request->get_cookie_arg(Affiliate_Program_Module::COOKIE_CAMPAIGN_NAME);
        }

        return null;
    }
}
