<?php

namespace bpmj\wpidea\modules\affiliate_program\core\services;

use bpmj\wpidea\data_types\exceptions\Invalid_Url_Exception;
use bpmj\wpidea\data_types\Url;
use bpmj\wpidea\environment\Interface_Site;
use bpmj\wpidea\modules\affiliate_program\Affiliate_Program_Module;
use bpmj\wpidea\modules\affiliate_program\core\entities\Partner;
use bpmj\wpidea\modules\affiliate_program\core\entities\External_Landing_Link;

class Affiliate_Link_Generator
{
    private Interface_Site $site;

    public function __construct(Interface_Site $site)
    {
        $this->site = $site;
    }

    /**
     * @throws Invalid_Url_Exception
     */
    public function get_partner_affiliate_link(Partner $partner): Url
    {
        $url_string = $this->get_affiliate_link_base() .
            $partner->get_affiliate_id()->as_string();
        return new Url($url_string);
    }

    /**
     * @throws Invalid_Url_Exception
     */
    public function get_partner_affiliate_link_to_external_landing(Partner $partner, External_Landing_Link $link): Url
    {
        $url_string = $this->get_affiliate_link_base() .
            $partner->get_affiliate_id()->as_string();

        $url_string .= '&' . Affiliate_Program_Module::AFFILIATE_PROGRAM_HTTP_GET_REDIRECT_NAME . '=' .
            $link->get_url()->get_value();

        return new Url($url_string);
    }

    public function get_affiliate_link_base(): string
    {
        return $this->site->get_base_url() . '?' .
            Affiliate_Program_Module::AFFILIATE_PROGRAM_HTTP_GET_PARAMETER_NAME . '=';
    }
}