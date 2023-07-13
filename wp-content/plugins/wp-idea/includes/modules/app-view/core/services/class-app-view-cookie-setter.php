<?php

namespace bpmj\wpidea\modules\app_view\core\services;

use bpmj\wpidea\Current_Request;
use bpmj\wpidea\modules\app_view\App_View_Module;

class App_View_Cookie_Setter
{
    private Current_Request $current_request;

    public function __construct(
        Current_Request $current_request
    )
    {
        $this->current_request = $current_request;
    }

    public function init(): void
    {
        if ($this->current_request->get_query_arg(App_View_Module::APP_VIEW_MODE_URL_PARAM_NAME) === App_View_Module::APP_VIEW_MODE_URL_PARAM_VALUE) {
            $this->set_cookies();
        }
    }

    public function set_cookies(): void
    {
        $this->set_app_view_cookie();
    }

    private function set_app_view_cookie(): void
    {
        $this->current_request->set_cookie_arg(
            App_View_Module::APP_VIEW_COOKIE_NAME,
            App_View_Module::APP_VIEW_COOKIE_VALUE,
            time() + App_View_Module::APP_VIEW_COOKIE_LIFE_TIME
        );
    }
}