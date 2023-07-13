<?php
namespace bpmj\wpidea\admin\support\diagnostics\items;

class BCMath extends Abstract_Diagnostics_Item {

    public function __construct() {
        $this->name         = __( 'BC Math', BPMJ_EDDCM_DOMAIN );
        $this->fix_hint     = __( 'Enable BC Math extension in server settings', BPMJ_EDDCM_DOMAIN );
    }

    public function get_current_value()
    {
        if( extension_loaded('bcmath') ) return 'on';
        
        return false;
    }

    public function check_status(){
        if( $this->get_current_value() ) return self::STATUS_OK;

        return self::STATUS_ERROR;
    }
}