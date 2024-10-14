<?php
namespace bpmj\wpidea\admin\support\diagnostics\items;

use bpmj\wpidea\admin\support\diagnostics\items\Abstract_Diagnostics_Item;

class PHP_Session_Cookie_Lifetime extends Abstract_Diagnostics_Item {

    public function __construct() {
        $this->name = __( 'PHP Session - cookie lifetime', BPMJ_EDDCM_DOMAIN );
        $this->fix_hint = __( 'Set "session.cookie_lifetime" to "0" in your php.ini file.', BPMJ_EDDCM_DOMAIN );
    }

    public function get_current_value()
    {
        return ini_get("session.cookie_lifetime");
    }

    public function check_status(){
        if (ini_get("session.cookie_lifetime") != "0") {
            return self::STATUS_ERROR;
        }

        return self::STATUS_OK;
    }
}
