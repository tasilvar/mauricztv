<?php

namespace bpmj\wpidea\admin\settings\core\services;

use bpmj\wpidea\admin\settings\Settings_API;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\instantiator\Interface_Initiable;

class Settings_Saved_Event_Handler implements Interface_Initiable
{
    private Interface_Actions $actions;
    private string $setting_slug = BPMJ_EDDCM_SETTINGS_SLUG;
    private string $layout_template_settings_slug = '';

    public function __construct(Interface_Actions $actions)
    {
        $this->actions = $actions;
    }

    public function init(): void
    {
        $this->actions->add('update_option_wp_idea', [$this, 'settings_after_save']);
    }

    public function settings_after_save()
    {
        $this->reload_mailers();

        $template = !empty($_POST[$this->setting_slug]['template']) ? $_POST[$this->setting_slug]['template'] : '';
        $template_settings_key = $this->layout_template_settings_slug . '-' . $template;
        $template_settings = $_POST[$template_settings_key] ?? null;
        if (!empty($template_settings)) {
            $template_settings = $this->remove_slashes_from_css($template_settings);
            $settings_api = $this->get_layout_template_settings_api($template);
            $sane_data = $settings_api->sanitize_options($template_settings);
            $options = get_option($this->layout_template_settings_slug);
            if (false === $options) {
                $options = array();
                add_option($this->layout_template_settings_slug, array());
            }
            $options[$template] = apply_filters('bpmj_eddcm_layout_filter_settings', $sane_data);
            update_option($this->layout_template_settings_slug, $options);
            do_action('bpmj_eddcm_layout_template_settings_save', $sane_data);
            bpmj_eddcm_reload_layout_template_settings();
        }

        $this->actions->do('wpi_after_save_settings');
    }

    private function reload_mailers()
    {
        WPI()->load_mailers();
        if (function_exists('bpmj_eddact_on_activate_callback')) {
            bpmj_eddact_on_activate_callback();
        }

        if (function_exists('bpmj_eddres_on_activate_callback')) {
            bpmj_eddres_on_activate_callback();
        }
    }

    private function remove_slashes_from_css(array $template_settings): array
    {
        $template_settings['css'] = stripslashes($template_settings['css'] ?? '');

        return $template_settings;
    }

    private function get_layout_template_settings_api($template)
    {
        $slug = $this->setting_slug . '-layout-template-settings';
        $template_options = $this->get_layout_template_settings_array($template);
        $template_settings_api = new Settings_API($slug . '-' . $template, true);
        $template_settings_api->set_detached_options($template_options);
        $template_root_dir = WPI()->templates->get_template_root_dir($template);
        if (file_exists($template_root_dir . '/template-config.php')) {
            $template_config = include $template_root_dir . '/template-config.php';
            if (!empty($template_config['settings'])) {
                $template_settings_api->set_fields($template_config['settings']);
            }
        }
        $template_settings_api->settings_init();
        return $template_settings_api;
    }

    private function get_layout_template_settings_array($template)
    {
        $slug = $this->layout_template_settings_slug;
        $options = get_option($slug);
        if (false === $options) {
            $options = array();
            add_option($slug, array());
        }
        return isset($options[$template]) ? $options[$template] : array();
    }

}