<?php

namespace bpmj\wpidea\admin\support\diagnostics\items;

class Hash_Hmac_Enabled extends Abstract_Diagnostics_Item
{
    public function __construct()
    {
        $this->name = __( 'hash_hmac', BPMJ_EDDCM_DOMAIN );
        $this->fix_hint = __( 'Enable hash extension', BPMJ_EDDCM_DOMAIN );
    }

    public function check_status()
    {
        if( $this->is_hash_extension_loaded() ) return self::STATUS_OK;

        return self::STATUS_ERROR;
    }

    public function get_current_value()
    {
        if( $this->is_hash_extension_loaded() ) return 'on';

        return false;
    }

    private function is_hash_extension_loaded()
    {
        if( extension_loaded('hash') ) return true;

        return false;
    }
}
