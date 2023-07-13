<?php
namespace bpmj\wpidea\admin\support\diagnostics\items;

class Curl_Enabled extends Abstract_Diagnostics_Item {

    public function __construct() {
        $this->name = __( 'curl enabled', BPMJ_EDDCM_DOMAIN );
        $this->fix_hint = __( 'Curl is not installed on this server', BPMJ_EDDCM_DOMAIN );
    }

    private function is_curl_installed() {
        if ( in_array( 'curl', get_loaded_extensions() ) ) return true;
        
        return false;
    }

    public function get_current_value()
    {
        if( $this->is_curl_installed() ) return 'yes';
        
        return false;
    }

    public function check_status(){
        if( $this->get_current_value() ) return self::STATUS_OK;

        return self::STATUS_ERROR;
    }
}