<?php

namespace bpmj\wpidea\admin\video;

class Api_Credentials{    
    /**
     * Get WP Idea license key
     *
     * @return string
     */
    public static function get_wpi_key()
    {        
        $wpidea_settings = get_option( WPI()->settings->get_settings_slug() );
        $license_key     = ! empty( $wpidea_settings[ 'license_key' ] ) ? $wpidea_settings[ 'license_key' ] : null;

        return $license_key;
    }

    /**
     * Get current request host
     *
     * @return string
     */
    public static function get_host()
    {
        return $_SERVER['HTTP_HOST'] ?? '';
    }
}