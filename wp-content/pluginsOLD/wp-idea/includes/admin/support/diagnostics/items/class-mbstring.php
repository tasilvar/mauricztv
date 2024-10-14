<?php
namespace bpmj\wpidea\admin\support\diagnostics\items;

use bpmj\wpidea\admin\support\diagnostics\items\Abstract_Diagnostics_Item;

class Mbstring extends Abstract_Diagnostics_Item {

    public function __construct() {
        $this->name         = __( 'mbstring', BPMJ_EDDCM_DOMAIN );
        $this->fix_hint     = __( 'Enable mbstring extension in server settings', BPMJ_EDDCM_DOMAIN );
    }

    public function get_current_value()
    {
        if( extension_loaded('mbstring') ) return 'on';
        
        return false;
    }

    public function check_status(){
        if( $this->get_current_value() ) return self::STATUS_OK;

        return self::STATUS_ERROR;
    }
}