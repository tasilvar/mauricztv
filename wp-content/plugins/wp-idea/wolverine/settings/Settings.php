<?php
namespace bpmj\wpidea\wolverine\settings;

class Settings
{
    public static function setTemplate($template)
    {
        global $wpidea_settings;

        $settings = get_option('wp_idea');
        $settings['template'] = $template;
        $settings['_fucking_cache'] = time(); // @note: for acceptance tests needed only, but... WP cache system is a shit ;) alternatively use wp_cache_delete('alloptions', 'options'); before get_option

        update_option('wp_idea', $settings);
        $wpidea_settings = $settings;
    }
    
    public static function enableAutoupdate()
    {
        global $wpidea_settings;

        $settings = get_option('wp_idea');
        $settings['enable_auto_update'] = 'on';
        update_option('wp_idea', $settings);

        $wpidea_settings = $settings;
    }
}
