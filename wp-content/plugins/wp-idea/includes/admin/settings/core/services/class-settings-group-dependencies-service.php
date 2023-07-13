<?php

namespace bpmj\wpidea\admin\settings\core\services;

use bpmj\wpidea\admin\helpers\html\Configuration_Settings_Popup;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\settings\Interface_Settings;

class Settings_Group_Dependencies_Service
{
    private Configuration_Settings_Popup $settings_popup;
    private Interface_Url_Generator $url_generator;
    private Current_Request $current_request;
    private Interface_Settings $app_settings;

    public function __construct(
        Configuration_Settings_Popup $settings_popup,
        Interface_Url_Generator $url_generator,
        Current_Request $current_request,
        Interface_Settings $app_settings
    ) {
        $this->settings_popup = $settings_popup;
        $this->url_generator = $url_generator;
        $this->current_request = $current_request;
        $this->app_settings = $app_settings;
    }

    public function get_settings_popup(): Configuration_Settings_Popup
    {
        return $this->settings_popup;
    }

    public function get_url_generator(): Interface_Url_Generator
    {
        return $this->url_generator;
    }

    public function get_current_request(): Current_Request
    {
        return $this->current_request;
    }

    public function get_app_settings(): Interface_Settings
    {
        return $this->app_settings;
    }
}