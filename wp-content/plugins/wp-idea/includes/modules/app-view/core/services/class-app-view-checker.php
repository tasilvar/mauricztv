<?php

declare(strict_types=1);

namespace bpmj\wpidea\modules\app_view\core\services;

use bpmj\wpidea\Current_Request;
use bpmj\wpidea\modules\app_view\App_View_Module;

class App_View_Checker
{
    private const PATTERN = '/app.|com.appmysite.shop/i';
    private Current_Request $current_request;

    public function __construct(
        Current_Request $current_request
    ) {
        $this->current_request = $current_request;
    }

    public function is_active(): bool
    {
        return $this->is_app_mode() || (defined('PUBLIGO_FORCE_APP_VIEW') && PUBLIGO_FORCE_APP_VIEW);
    }

    private function is_app_mode(): bool
    {
        return $this->is_set_app_view_cookie() || $this->is_android_app_view() || $this->is_ios_app_view();
    }

    private function is_android_app_view(): bool
    {
        $x_requested_with = $this->current_request->get_x_requested_with();

        if (!$x_requested_with) {
            return false;
        }

        return preg_match(self::PATTERN, $this->current_request->get_x_requested_with()) ? true : false;
    }

    private function is_ios_app_view(): bool
    {
        $user_agent = $this->current_request->get_user_agent();

        return (strpos($user_agent, 'Mobile/') !== false) && (strpos($user_agent, 'Safari/') === false);
    }

    private function is_set_app_view_cookie(): bool
    {
        $cookie_arg = $this->current_request->get_cookie_arg(App_View_Module::APP_VIEW_COOKIE_NAME);

        return $cookie_arg === App_View_Module::APP_VIEW_COOKIE_VALUE;
    }
}