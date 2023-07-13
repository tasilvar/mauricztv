<?php

namespace bpmj\wpidea\admin\support\diagnostics\items;

use \bpmj\wpidea\infrastructure\system\System;

class Decimal_Point_Comma extends Abstract_Diagnostics_Item
{
    public function __construct()
    {
        $this->name = __( 'Decimal point', BPMJ_EDDCM_DOMAIN );
        $this->fix_hint = __( 'Decimal point in PHP is "," (comma), this can cause problems.', BPMJ_EDDCM_DOMAIN );
    }

    public function check_status()
    {
        if ( System::is_decimal_point_comma() )
            return self::STATUS_ERROR;

        return self::STATUS_OK;
    }

    public function get_current_value()
    {
        $locale = localeconv();
        return '"' . $locale['decimal_point'] . '" ';
    }
}
