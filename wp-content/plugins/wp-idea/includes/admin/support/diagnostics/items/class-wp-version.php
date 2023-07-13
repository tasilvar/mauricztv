<?php
namespace bpmj\wpidea\admin\support\diagnostics\items;

class WP_Version extends Abstract_Diagnostics_Item {

    private const MIN_VERSION = 5.5;

    public const MAX_RECOMMENDED_VERSION = 6.0;

    public function __construct() {
        $this->name = __( 'WP Version', BPMJ_EDDCM_DOMAIN );
        $this->fix_hint = __( 'Update your Wordpress installation.', BPMJ_EDDCM_DOMAIN );
    }

    public function get_current_value()
    {
        return get_bloginfo('version');
    }

    public function check_status() {
        if( $this->get_current_value() < self::MIN_VERSION ) return self::STATUS_ERROR;

        return self::STATUS_OK;
    }
}