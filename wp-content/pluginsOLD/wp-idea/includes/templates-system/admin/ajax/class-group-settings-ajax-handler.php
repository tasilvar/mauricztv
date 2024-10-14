<?php

namespace bpmj\wpidea\templates_system\admin\ajax;

use bpmj\wpidea\admin\helpers\fonts\Fonts_Helper;
use bpmj\wpidea\Caps;
use bpmj\wpidea\templates_system\groups\Template_Group;
use bpmj\wpidea\templates_system\groups\Template_Group_Id;
use bpmj\wpidea\wolverine\user\User;

class Group_Settings_Ajax_Handler
{
    public const AJAX_GET_SETTINGS_ACTION_NAME = 'bpmj_template_group_get_settings';

    public const AJAX_ACTION_GET_GOOGLE_FONTS_LIST = 'get_google_fonts_list';
    public const AJAX_GET_FIELD_OPTIONS_NONCE = 'bpmj_nonce_get_fonts';

    public const AJAX_ACTION_SAVE_SETTINGS = 'bpmj_save_template_group_settings';
    public const AJAX_SAVE_GROUP_SETTINGS_NONCE_PREFIX = 'bpmj_nonce_save_group_settings_';

    public const GROUP_ID_PARAM_NAME = 'bpmj_template_group_id';

    public static function ajax_store_settings(): void
    {
        self::validate_store_settings_request_or_die();

        self::validate_permissions_or_die();

        self::update_group_settings_or_die();

        self::send_json_success_and_die('Settings updated!');
    }

    private static function validate_store_settings_request_or_die(): void
    {
        $request_data = self::get_post_request_data();
        $group_id = $request_data[self::GROUP_ID_PARAM_NAME] ?? null;

        check_ajax_referer(self::AJAX_SAVE_GROUP_SETTINGS_NONCE_PREFIX . $group_id);
    }

    private static function get_post_request_data(): ?array
    {
        return $_POST['data'] ?? [];
    }

    private static function validate_permissions_or_die(): void
    {
        if (!User::currentUserHasAnyOfTheCapabilities([Caps::CAP_MANAGE_SETTINGS])) {
            self::send_json_error_and_die(__('You are not authorized to perform this operation!', BPMJ_EDDCM_DOMAIN));
        }
    }

    private static function update_group_settings_or_die(): void
    {
        $request_data = self::get_post_request_data();

        $group_id =  Template_Group_Id::from_string(
            $request_data[self::GROUP_ID_PARAM_NAME]
        );

        /** @var Template_Group $group */
        $group = Template_Group::find($group_id);

        if($group === null) {
            self::send_json_error_and_die(__('No group with the given id!', BPMJ_EDDCM_DOMAIN));
        }

        $group->update_settings($request_data);
    }

    private static function send_json_error_and_die(string $message): void
    {
        wp_send_json_error($message);
    }

    private static function send_json_success_and_die($payload): void
    {
        wp_send_json_success($payload);
    }

    public static function ajax_get_google_fonts_list(): void
    {
        self::validate_get_field_options_request_or_die();

        self::validate_permissions_or_die();

        self::send_json_success_and_die(Fonts_Helper::get_google_fonts_list());
    }

    public static function echo_nonce_field_for_group(string $group_id): void
    {
        wp_nonce_field(self::AJAX_SAVE_GROUP_SETTINGS_NONCE_PREFIX . $group_id);
    }

    public static function get_nonce_for_field_options(): string
    {
        return wp_create_nonce(self::AJAX_GET_FIELD_OPTIONS_NONCE);
    }

    private static function validate_get_field_options_request_or_die(): void
    {
        check_ajax_referer(self::AJAX_GET_FIELD_OPTIONS_NONCE);
    }
}
