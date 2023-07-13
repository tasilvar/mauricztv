<?php

namespace bpmj\wpidea\admin\settings\core\services;

use bpmj\wpidea\translator\Interface_Translator;

class Settings_Tab_Scripts implements Interface_Settings_Tab_Scripts
{
    private Interface_Translator $translator;

    public function __construct(
        Interface_Translator $translator
    ) {
        $this->translator = $translator;
    }

    public function register_script(string $save_single_field_url, string $save_configuration_group_fields_url, ?string $license_key_info_url = null): void
    {
        $licence_key = [];

        if ($license_key_info_url) {
            $licence_key = ['license_key_info_url' => $license_key_info_url];
        }

        wp_register_script('settings_tab_scripts', BPMJ_EDDCM_URL . 'assets/js/admin/settings/tab.js', [
            'jquery',
        ], BPMJ_EDDCM_VERSION);
        wp_localize_script(
            'settings_tab_scripts',
            'settings_tab',
            array_merge($licence_key, [
                'save_single_field_url' => $save_single_field_url,
                'save_configuration_group_fields_url' => $save_configuration_group_fields_url,
                'an_error_occurred' => $this->translator->translate('settings.messages.an_error_occurred'),
                'unsaved_data_error' => $this->translator->translate('settings.messages.unsaved_data_error'),
                'save_course_structure' => $this->translator->translate('settings.field.button.save'),
                'saving_course_structure' => $this->translator->translate('settings.field.button.saving'),
                'saving_quiz_structure' => $this->translator->translate('settings.field.button.saving'),
            ])
        );
        wp_enqueue_script('settings_tab_scripts');
    }
}