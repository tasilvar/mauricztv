<?php

namespace bpmj\wpidea\modules\affiliate_program\core\services;

use bpmj\wpidea\Current_Request;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\modules\affiliate_program\Affiliate_Program_Module;
use bpmj\wpidea\modules\affiliate_program\core\helpers\Interface_Encoder;
use bpmj\wpidea\modules\affiliate_program\core\repositories\Interface_Partner_Repository;
use bpmj\wpidea\modules\affiliate_program\core\value_objects\Affiliate_ID;

class Affiliate_Program_Cookie_Setter
{
    private const EMPTY_STRING = '';

    private Interface_Actions $actions;
    private Current_Request $current_request;
    private Interface_Partner_Repository $partner_repository;
    private Interface_Encoder $encoder;

    public function __construct(
        Interface_Actions $actions,
        Current_Request $current_request,
        Interface_Partner_Repository $partner_repository,
        Interface_Encoder $encoder
    ) {
        $this->actions = $actions;
        $this->current_request = $current_request;
        $this->partner_repository = $partner_repository;
        $this->encoder = $encoder;
    }

    public function init(): void
    {
        $this->actions->add('init', [$this, 'set_cookies']);
    }

    public function set_cookies(): void
    {
        if ($this->current_request->query_arg_exists(Affiliate_Program_Module::AFFILIATE_PROGRAM_HTTP_GET_PARAMETER_NAME)) {
            $this->set_affiliate_id_cookie();
            $this->set_affiliate_campaign_name_cookie();
        }
    }

    private function set_affiliate_id_cookie(): void
    {
        $id_string = $this->current_request->get_query_arg(
            Affiliate_Program_Module::AFFILIATE_PROGRAM_HTTP_GET_PARAMETER_NAME
        );

        $id = $this->partner_repository->find_by_affiliate_id(new Affiliate_ID($id_string));

        if ($id) {
            $this->set_cookie(Affiliate_Program_Module::COOKIE_NAME, $id->get_affiliate_id()->as_string());
        }
    }

    private function set_affiliate_campaign_name_cookie(): void
    {
        $campaign_name = $this->current_request->get_query_arg(
            Affiliate_Program_Module::AFFILIATE_PROGRAM_HTTP_GET_CAMPAIGN_NAME
        );

        $campaign_name = $campaign_name ?? self::EMPTY_STRING;

        $this->set_cookie(Affiliate_Program_Module::COOKIE_CAMPAIGN_NAME, $campaign_name);
    }

    private function set_cookie(string $name, string $value): void
    {
        setcookie(
            $name,
            $this->encoder->base64_encode($value),
            time() + Affiliate_Program_Module::COOKIE_LIFE_TIME,
            '/'
        );
    }
}