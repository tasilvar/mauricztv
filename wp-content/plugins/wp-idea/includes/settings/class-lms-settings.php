<?php
namespace bpmj\wpidea\settings;

use bpmj\wpidea\admin\settings\Settings_API;

class LMS_Settings implements Interface_Settings
{
    public const VALUE_ENABLED = 'on';
    public const VALUE_DISABLED = 'off';

    public const TEMPLATE_SCARLET = 'scarlet';
    public const TEMPLATE_CLASSIC = 'default';

    public const TEMPLATE_OPTION_DISABLE_BANNERS = 'disable_banners';

    private const SETTINGS_SLUG = 'wp_idea';

    public function get(string $option_name, $default_value = null)
    {
        return self::get_option($option_name, $default_value);
    }

    public function set(string $option_name, $value): bool
    {
        self::update($option_name, $value);
        return true;
    }

    public static function get_all()
    {
        global $wpidea_settings;

        return $wpidea_settings;
    }
    public static function get_option($option_name, $default_value = null)
    {
        $wpidea_settings = self::get_all();

        if(empty($wpidea_settings[$option_name])) {
            $settings = new Settings_API();
            return $settings->get_option( $option_name, $default_value, 'edd_settings' );
        }

        //@todo: zamiana wartości checkboxów na boolean - być może lepiej to ograć osobną metodą, typu is_option_enabled()
        if($wpidea_settings[$option_name] === self::VALUE_DISABLED) return false;

        if($wpidea_settings[$option_name] === self::VALUE_ENABLED) return true;

        return $wpidea_settings[$option_name];
    }

    public static function update(string $option_name, $value): void
    {
        $wpidea_settings = self::get_all();
        $wpidea_settings[$option_name] = $value;
        update_option(self::SETTINGS_SLUG, $wpidea_settings);
    }

    /**
     * Non-static wrapper for ::add_layout_option method
     */
    public function add_template_option(array $option_args): void
    {
        self::add_layout_option($option_args);
    }

    //@todo: wybór do której sekcji dodać opcję powinien zostać rozwiązany w jakiś bardziej uniwersalny sposób
    public static function add_layout_option($option_args)
    {
        add_filter('bpmj_eddcm_layout', function($settings) use ($option_args){
            return array_merge($settings, [$option_args]);
        });
    }

    public static function get_template_settings(string $template_name = null): array
    {
        $template_settings = get_option(WPI()->settings->get_layout_template_settings_slug());
        if ($template_name !== null) {
            return $template_settings[$template_name] ?? [];
        }

        return $template_settings;
    }

    public static function is_template_option_enabled(string $template_name, string $option_name): bool
    {
        $option_value = self::get_template_settings($template_name)[$option_name] ?? null;

        return $option_value === self::VALUE_ENABLED;
    }
}
