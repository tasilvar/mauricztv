<?php

namespace bpmj\wpidea;

use WP_Error;
use WP_REST_Server;

class API_V2
{

    private const API_KEY_SLUG = 'api_key';
    private const API_KEY_OPTIONS_SLUG = 'wpi_api_key';
    public const URL_NAMESPACE = 'wp-idea/v2';

    public static function get_api_key()
    {
        return get_option(self::API_KEY_OPTIONS_SLUG) ?? '';
    }

    public static function generate_and_save_api_key()
    {
        $api_key = md5(uniqid());
        update_option(self::API_KEY_OPTIONS_SLUG, $api_key);
        return $api_key;
    }

    public static function filter_wp_rest_api_access($access)
    {
        $api_key = $_GET[self::API_KEY_SLUG] ?? $_POST[self::API_KEY_SLUG] ?? null;

        if (!WPI()->packages->has_access_to_feature(Packages::FEAT_API_V2)) {
            return new WP_Error(
                'rest_token_error',
                __('You are not authorized.', BPMJ_EDDCM_DOMAIN),
                ['status' => rest_authorization_required_code()]);
        }

        if (!$api_key || $api_key != self::get_api_key()) {
            return new WP_Error(
                'rest_token_error',
                __('REST API wrong key.', BPMJ_EDDCM_DOMAIN),
                ['status' => rest_authorization_required_code()]);
        }

        return $access;
    }
}
