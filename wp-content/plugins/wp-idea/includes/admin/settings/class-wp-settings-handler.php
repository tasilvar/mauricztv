<?php

namespace bpmj\wpidea\admin\settings;

class WP_Settings_Handler
{
    public const WP_SETTING_BLOGNAME = 'blogname';
    public const WP_SETTING_BLOGDESC = 'blogdescription';
    public const WP_SETTING_SITE_ICON = 'site_icon';
    public const WP_SETTING_PAGE_ON_FRONT = 'page_on_front';
    public const WP_SETTING_SHOW_ON_FRONT = 'show_on_front';
    public const WP_SETTING_COMMENTS_NOTIFY = 'comments_notify';
    public const WP_SETTING_MODERATION_NOTIFY = 'moderation_notify';
    public const WP_SETTING_COMMENT_MODERATION = 'comment_moderation';
    public const WP_SETTING_COMMENT_PREVIOUSLY_APPROVED = 'comment_previously_approved';

    public const WP_SAVABLE_SETTINGS = [
        self::WP_SETTING_BLOGNAME,
        self::WP_SETTING_BLOGDESC,
        self::WP_SETTING_SITE_ICON,
        self::WP_SETTING_PAGE_ON_FRONT,
        self::WP_SETTING_COMMENTS_NOTIFY,
        self::WP_SETTING_MODERATION_NOTIFY,
        self::WP_SETTING_COMMENT_MODERATION,
        self::WP_SETTING_COMMENT_PREVIOUSLY_APPROVED
    ];

    public function maybe_update_wordpress_settings(array $new_settings, array $old_settings): void
    {
        foreach (self::WP_SAVABLE_SETTINGS as $index => $option_name)
        {
            if (!isset($new_settings[$option_name])) {
                continue;
            }

            $new_value = $new_settings[$option_name];

            $setting_unchanged = isset($old_settings[$option_name]) && $old_settings[$option_name] === $new_value;
            if ($setting_unchanged) {
                continue;
            }

            if ($new_value === 'on') {
                $new_value = 1;
            }

            if ($new_value === 'off') {
                $new_value = 0;
            }

            $this->set_wp_option_value($option_name, $new_value);
        }
    }

    public function get_default_value_for(string $field_name)
    {
        if(!in_array($field_name, self::WP_SAVABLE_SETTINGS, true)) {
            return null;
        }

        return $this->get_wp_option_value($field_name);
    }

    public function wp_option_value_is(string $field_name, $value): bool
    {
        return $this->get_wp_option_value($field_name) === $value;
    }

    private function get_wp_option_value(string $field_name)
    {
        return get_option($field_name);
    }

    private function set_wp_option_value(string $field_name, $value): bool
    {
        return update_option($field_name, $value);
    }
}