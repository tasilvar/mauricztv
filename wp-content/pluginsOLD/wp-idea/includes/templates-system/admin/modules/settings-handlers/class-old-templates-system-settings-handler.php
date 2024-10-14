<?php

namespace bpmj\wpidea\templates_system\admin\modules\settings_handlers;

use bpmj\wpidea\admin\subscription\models\Subscription;
use bpmj\wpidea\settings\LMS_Settings;
use bpmj\wpidea\View;
use bpmj\wpidea\Templates;
use bpmj\wpidea\Caps;

class Old_Templates_System_Settings_Handler implements Interface_Templates_Settings_Handler
{
    private $lms_settings;

    private $subscription;
    public function __construct(
        LMS_Settings $lms_settings,
        Subscription $subscription
    ) {
        $this->lms_settings = $lms_settings;
        $this->subscription = $subscription;
    }

    public function add_menu_pages(): void
    {
        $this->create_temporary_template_changer();
    }

    private function create_temporary_template_changer(): void
    {
        add_submenu_page(
            null,
            __('Templates', BPMJ_EDDCM_DOMAIN),
            '',
            Caps::CAP_MANAGE_SETTINGS,
            'wp-idea-template-changer',
            [$this, 'temporary_template_changer']
        );
    }
    
    public function temporary_template_changer(): void
    {
        echo View::get_admin('/templates/guide/new-templates-available');
    }
    
    public function should_template_option_be_visible_on_the_settings_page(string $option_name): bool
    {
        if($option_name === 'override_all') {
            return !$this->subscription->is_go();
        }

        return true;
    }

    public function should_color_settings_be_displayed(): bool
    {
        return true;
    }

    public function should_download_section_position_field_be_displayed(): bool
    {
        return WPI()->templates->is_feature_supported(Templates::FEATURE_LESSON_FILES_POSITION);
    }

    public function get_current_template_options(): ?array
    {
        return null;
    }

    public function legacy_templates_settings_in_use(): bool
    {
        return true;
    }

    public function get_override_all_option_value(): ?string
    {
        return $this->lms_settings->get('override_all') === true ? 'on' : 'off';
    }

    public function get_custom_css_field_value(): ?string
    {
        $template_settings = $this->lms_settings->get('template');

        return WPI()->settings->get_custom_css_field_value($template_settings);
    }
}
