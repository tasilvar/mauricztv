<?php
namespace bpmj\wpidea\admin\support\diagnostics\items;

use bpmj\wpidea\admin\support\diagnostics\items\Abstract_Diagnostics_Item;

class PHP_Version extends Abstract_Diagnostics_Item {

    public function __construct() {
        $this->name = __( 'PHP Version', BPMJ_EDDCM_DOMAIN );
        $this->fix_hint = __( 'Update your PHP', BPMJ_EDDCM_DOMAIN );
    }

    public function get_current_value()
    {
        return PHP_VERSION;
    }

    public function check_status(){
        if (version_compare( $this->get_current_value(), \BPMJ_WPI::MINIMUM_PHP_VERSION, '<' ) ) {
            return self::STATUS_ERROR;
        }

        return self::STATUS_OK;
    }
}
