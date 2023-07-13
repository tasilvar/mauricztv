<?php

namespace bpmj\wpidea\modules\affiliate_program\core\services;

use bpmj\wpidea\Current_Request;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\Interface_Redirector;
use bpmj\wpidea\modules\affiliate_program\Affiliate_Program_Module;

class Affiliate_Redirector
{
    private Interface_Actions $actions;

    private Current_Request $current_request;

    private Interface_Redirector $redirector;
    private Interface_External_Landing_Link_Service $external_landing_link_service;

    public function __construct(
        Interface_Actions $actions,
        Current_Request $current_request,
        Interface_Redirector $redirector,
        Interface_External_Landing_Link_Service $external_landing_link_service
    ) {
        $this->actions = $actions;
        $this->current_request = $current_request;
        $this->redirector = $redirector;
        $this->external_landing_link_service = $external_landing_link_service;
    }

    public function init(): void
    {
        $this->actions->add('init', [$this, 'mayby_redirect'], 100);
    }

    public function mayby_redirect(): void
    {
        if (!$this->current_request->query_arg_exists(Affiliate_Program_Module::AFFILIATE_PROGRAM_HTTP_GET_REDIRECT_NAME)) {
            return;
        }

        $redirect_url = $this->try_to_format_url(
            $this->current_request->get_query_arg(Affiliate_Program_Module::AFFILIATE_PROGRAM_HTTP_GET_REDIRECT_NAME)
        );

        if (!$this->is_url_valid($redirect_url)) {
            return;
        }

        if (!$this->is_url_defined($redirect_url)) {
            return;
        }

        $this->redirector->redirect($redirect_url);
        exit;
    }

    private function is_url_valid(string $url): bool
    {
        if (false !== filter_var($url, FILTER_VALIDATE_URL)) {
            return true;
        }

        return false;
    }

    private function try_to_format_url(string $url): string
    {
        $url = urldecode($url);
        return parse_url($url, PHP_URL_SCHEME) === null ? 'https://' . $url : $url;
    }

    private function is_url_defined(string $redirect_url): bool
    {
        $landing_link = $this->external_landing_link_service->find_first_with_matching_url($redirect_url);

        return $landing_link !== null;
    }
}