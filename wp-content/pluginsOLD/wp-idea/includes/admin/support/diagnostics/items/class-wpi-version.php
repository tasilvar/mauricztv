<?php

namespace bpmj\wpidea\admin\support\diagnostics\items;

use bpmj\wpidea\admin\Wpi_Version as Bpmj_Wpi_Version;

class WPI_Version extends Abstract_Diagnostics_Item {

    public function __construct() {
        $this->name         = __( 'LMS Idea Version', BPMJ_EDDCM_DOMAIN );
        $this->fix_hint     = __( 'Update your LMS Idea.', BPMJ_EDDCM_DOMAIN );
        
        $this->solve_hint   = __( 'Make sure you have the latest <strong>LMS Idea</strong> version installed', BPMJ_EDDCM_DOMAIN );
    }

    public function get_current_value()
    {
        return BPMJ_EDDCM_VERSION;
    }

    public function check_status(){
        if( Bpmj_Wpi_Version::needs_update() ) return self::STATUS_ERROR;

        return self::STATUS_OK;
    }
}