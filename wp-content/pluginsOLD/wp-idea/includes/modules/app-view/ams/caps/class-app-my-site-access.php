<?php

namespace bpmj\wpidea\modules\app_view\ams\caps;

use bpmj\wpidea\Caps;
use bpmj\wpidea\caps\Access;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\modules\app_view\ams\api\controllers\AMS_Rest_Routes_Controller;

class App_My_Site_Access extends Access
{
    private const SETTINGS_REST_ENDPOINT = '/wp-json/wp/v2/settings';
    private Current_Request $current_request;

    public function __construct(
        array $all_caps,
        Current_Request $current_request
    )
    {
        parent::__construct($all_caps);

        $this->current_request = $current_request;
    }

    public function verifyPage($post = null)
    {
        if (!is_null($post)) {
            return parent::verifyPage($post);
        }

        if (!$this->verify_request_uri()) {
            return parent::verifyPage($post);
        }

        $user_name = $this->current_request->get_php_auth_user();
        $password = $this->current_request->get_php_auth_pw();

        if (empty($user_name) || empty($password)) {
            return parent::verifyPage($post);
        }

        $authenticated = wp_authenticate_application_password(null, $user_name, $password);

        if (!$authenticated instanceof \WP_User) {
            return parent::verifyPage($post);
        }

        if(!$this->has_lms_admin_role($authenticated)) {
            return parent::verifyPage($post);
        }

        return $this->grant_access();
    }

    public function grant_access()
    {
        $this->all_caps[Caps::CAP_MANAGE_OPTIONS] = true;

        return $this->all_caps;
    }

    private function verify_request_uri(): bool
    {
        $request_uri = $this->current_request->get_request_uri();

        if (empty($request_uri)) {
            return false;
        }

        return str_contains($request_uri, self::SETTINGS_REST_ENDPOINT)
            || str_contains($request_uri, AMS_Rest_Routes_Controller::URL_NAMESPACE . AMS_Rest_Routes_Controller::URL_ROUTE_AMS_VERIFY_APPLICATION_PASSWORD)
            || str_contains($request_uri, AMS_Rest_Routes_Controller::URL_NAMESPACE . AMS_Rest_Routes_Controller::URL_ROUTE_AMS_WP_GET_USER_AUTH_COOKIES);
    }

    private function has_lms_admin_role(\WP_User $authenticated): bool
    {
        return in_array(Caps::ROLE_LMS_ADMIN, (array)$authenticated->roles, true);
    }
}