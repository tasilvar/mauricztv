<?php
namespace bpmj\wpidea\admin\support\diagnostics\items;

use bpmj\wpidea\admin\support\diagnostics\items\Abstract_Diagnostics_Item;

class Max_Input_Vars extends Abstract_Diagnostics_Item {

    const MIN_VALUE = 2500;
    const MIN_OPTIMAL_VALUE = 5000;


    public function __construct() {
        $this->name = __( 'max_input_vars', BPMJ_EDDCM_DOMAIN );
        $this->fix_hint = __( 'Change your php.ini configuration', BPMJ_EDDCM_DOMAIN );
        $this->solve_hint   = __( 'Make sure max_input_vars value is set to at least 2500 and preferably 5000-6000.', BPMJ_EDDCM_DOMAIN );
    }

    public function get_current_value()
    {
        return ini_get('max_input_vars');
    }

    public function check_status(){
        if( $this->get_current_value() >= self::MIN_VALUE ) return self::STATUS_OK;

        return self::STATUS_ERROR;
    }
}
