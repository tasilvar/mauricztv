<?php
namespace bpmj\wpidea\admin\support\diagnostics\items;

use bpmj\wpidea\admin\support\diagnostics\items\Abstract_Diagnostics_Item;

class PHP_Session_Use_Cookies extends Abstract_Diagnostics_Item {

    public function __construct() {
        $this->name = __( 'PHP Session - use cookies', BPMJ_EDDCM_DOMAIN );
        $this->fix_hint = __( 'Set "session.use_cookies" to "1" in your php.ini file.', BPMJ_EDDCM_DOMAIN );
    }

    public function get_current_value()
    {
        return ini_get("session.use_cookies");
    }

    public function check_status(){
        if (ini_get("session.use_cookies") != "1") {
            return self::STATUS_ERROR;
        }

        return self::STATUS_OK;
    }
}
