<?php
namespace bpmj\wpidea\admin\support\diagnostics\items;

class Allow_Url_Fopen extends Abstract_Diagnostics_Item {

    public function __construct() {
        $this->name = __( 'allow_url_fopen', BPMJ_EDDCM_DOMAIN );
        $this->fix_hint = __( 'Change your php.ini configuration', BPMJ_EDDCM_DOMAIN );
    }

    public function get_current_value()
    {
        if( ini_get('allow_url_fopen') ) return 'on';
        
        return false;
    }

    public function check_status(){
        if( $this->get_current_value() ) return self::STATUS_OK;

        return self::STATUS_ERROR;
    }
}
