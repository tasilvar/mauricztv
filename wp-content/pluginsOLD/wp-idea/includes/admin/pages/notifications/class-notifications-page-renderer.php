<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\notifications;

use bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug;
use bpmj\wpidea\admin\renderers\Abstract_Page_Renderer;
use bpmj\wpidea\controllers\admin\Admin_Notifications_Controller;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\notices\User_Notice_Service;
use bpmj\wpidea\Packages;
use bpmj\wpidea\packages\Interface_Packages_API;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\settings\Interface_Settings;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\view\Interface_View_Provider;

class Notifications_Page_Renderer extends Abstract_Page_Renderer
{
    public const INPUT_GROUP_NAME = 'wpi_notifications';

    private Interface_Translator $translator;
    private Interface_View_Provider $view_provider;
    private Interface_Url_Generator $url_generator;
    private Interface_Settings $settings;
    private Interface_Packages_API $packages_api;

    public function __construct(
        Interface_Url_Generator $url_generator,
        Interface_Translator $translator,
        Interface_View_Provider $view_provider,
        Interface_Settings $settings,
        Interface_Packages_API $packages_api
    )
    {
        $this->url_generator = $url_generator;
        $this->translator = $translator;
        $this->view_provider = $view_provider;
        $this->settings = $settings;
        $this->packages_api = $packages_api;
    }

    public function get_rendered_page(): string
    {
        if (!$this->packages_api->has_access_to_feature(Packages::FEAT_USER_NOTICES)) {
            return $this->render_wrong_license_page();
        }

        return $this->view_provider->get_admin('/pages/notifications/index', [
            'page_title' => $this->translator->translate('notifications.page_title'),
            'action'    => $this->get_notifications_action_url(),
            'url_notifications_page' => $this->get_notifications_page_url(),
            'input_group_name'    => self::INPUT_GROUP_NAME,
            'fields'    => $this->get_data_from_settings_to_fields(),
            'editor_settings' => $this->get_editor_settings(),
            'translator' => $this->translator
        ]);
    }

    private function get_data_from_settings_to_fields(): array
    {
        return [
            User_Notice_Service::ALLOW_USER_NOTICE_OPTION_KEY => $this->settings->get(User_Notice_Service::ALLOW_USER_NOTICE_OPTION_KEY),
            User_Notice_Service::USER_NOTICE_CONTENT_OPTION_KEY => $this->settings->get(User_Notice_Service::USER_NOTICE_CONTENT_OPTION_KEY),
            User_Notice_Service::USER_NOTICE_CLOSE_BUTTON_OPTION_KEY => $this->settings->get(User_Notice_Service::USER_NOTICE_CLOSE_BUTTON_OPTION_KEY)
        ];
    }

    private function get_editor_settings(): array
    {
        return [
            'teeny' => true,
            'textarea_name' => self::INPUT_GROUP_NAME.'['.User_Notice_Service::USER_NOTICE_CONTENT_OPTION_KEY.']',
            'textarea_rows' => 20
        ];
    }

    private function get_notifications_page_url(): string
    {
        return $this->url_generator->generate_admin_page_url('admin.php', [
            'page' => Admin_Menu_Item_Slug::NOTIFICATIONS
        ]);
    }

    private function get_notifications_action_url(): string
    {
        return $this->url_generator->generate(Admin_Notifications_Controller::class, 'save_notifications', [
            Nonce_Handler::DEFAULT_ACTION_NAME => Nonce_Handler::create()
        ]);
    }

    private function render_wrong_license_page(): string
    {
        return $this->view_provider->get_admin('/pages/notifications/error', [
            'title' => $this->translator->translate('notifications.page_title'),
            'message' => $this->packages_api->render_no_access_to_feature_info(Packages::FEAT_USER_NOTICES),
        ]);
    }
}