<?php

namespace bpmj\wpidea\admin\video\settings;

use bpmj\wpidea\Software_Variant;

class Videos_Settings {

    const VIMEO_UPLOAD_ENABLED_OPTION_NAME = 'bpmj_vimeo_upload_enabled';
    
    const VIMEO_UPLOAD_ENABLED_ON = 'on';
    const VIMEO_UPLOAD_ENABLED_OFF = 'off';

    const ADVANCED_SETTINGS_KEY = 'video_upload_to_remote_enabled';

    const BPMJ_HIDE_UPLOAD_TOGGLE = 'BPMJ_HIDE_UPLOAD_TOGGLE';

    public function __construct() {
        add_filter( 'bpmj_eddcm_advanced_settings', array( $this, 'add_field_in_advanced_wpi_settings' ) );
        
        add_action( 'admin_print_footer_scripts', array( $this, 'print_vimeo_upload_enabled_js_variable') );
    }

    public function print_vimeo_upload_enabled_js_variable()
    {
        $hide_toggle = self::should_toggle_be_hidden() ? 'true' : 'false';

        echo "<script>var " . self::BPMJ_HIDE_UPLOAD_TOGGLE . "=" . $hide_toggle . "</script>";
    }

    public function add_field_in_advanced_wpi_settings( $advanced_settings )
    {
        if( self::is_vimeo_integration_forced() ) return $advanced_settings;
        
        $fields = array(
            self::ADVANCED_SETTINGS_KEY => array(
                'name'    => self::ADVANCED_SETTINGS_KEY,
                'label'   => __( 'Disable upload to Vimeo option', BPMJ_EDDCM_DOMAIN ),
                'type'    => 'checkbox_one_empty',
                'desc'    => __( 'Check this box to disable Vimeo upload completely.', BPMJ_EDDCM_DOMAIN ),
                'default' => false,
            )
        );

        return array_merge( $advanced_settings, $fields );
    }

    /**
     * Enable upload to Vimeo after file uplaod
     *
     * @return bool
     */
    public static function enable_vimeo_upload()
    {
        return update_option( self::VIMEO_UPLOAD_ENABLED_OPTION_NAME, self::VIMEO_UPLOAD_ENABLED_ON );
    }

    /**
     * Disable upload to Vimeo after file uplaod
     *
     * @return bool
     */
    public static function disable_vimeo_upload()
    {
        return update_option( self::VIMEO_UPLOAD_ENABLED_OPTION_NAME, self::VIMEO_UPLOAD_ENABLED_OFF );
    }

    /**
     * Returns true if Vimeo upload option is enabled
     *
     * @return bool
     */
    public static function is_vimeo_upload_enabled()
    {
        if( self::is_vimeo_integration_forced() ) return true;

        if( self::is_vimeo_upload_globally_disabled() ) return false;
        
        return get_option( self::VIMEO_UPLOAD_ENABLED_OPTION_NAME, '' ) == self::VIMEO_UPLOAD_ENABLED_ON;
    }

    /**
     * Returns true if Vimeo upload option is disabled
     *
     * @return bool
     */
    public static function is_vimeo_upload_disabled()
    {
        return ! self::is_vimeo_upload_enabled();
    }

    /**
     * Returns true if Vimeo upload option is disabled in the advanced WP Idea settings
     *
     * @return bool
     */
    public static function is_vimeo_upload_globally_disabled()
    {
        global $wpidea_settings;

        if( ! array_key_exists( self::ADVANCED_SETTINGS_KEY, $wpidea_settings ) ) return false;

        return '1' === $wpidea_settings[ self::ADVANCED_SETTINGS_KEY ];
    }

    public static function is_vimeo_integration_forced()
    {
        return Software_Variant::is_saas();
    }

    public static function should_toggle_be_hidden()
    {
        return self::is_vimeo_upload_globally_disabled() || self::is_vimeo_integration_forced();
    }
}