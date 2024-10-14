<?php
namespace bpmj\wpidea\infrastructure\system;

use bpmj\wpidea\settings\LMS_Settings;

class System
{
    public function get_system_currency(): string
    {
        return self::get_currency();
    }

    public static function get_current_timestamp(): int
    {
        global $bpmj_eddpc_tnow;
        return $bpmj_eddpc_tnow;
    }

    public static function get_current_timestamp_with_timezone_offset(): int
    {
        global $bpmj_eddpc_tnow;
        return bpmj_eddpc_adjust_timestamp($bpmj_eddpc_tnow);
    }

    public static function get_currency(): string
    {
        return edd_get_currency();
    }

    public static function is_ajax_purchase_enabled(): bool
    {
        return ! edd_is_ajax_disabled();
    }

    public static function is_decimal_point_comma(): bool
    {
        $locale = localeconv();

        if ( isset( $locale['decimal_point'] ) && ',' === $locale['decimal_point'] )
            return true;

        return false;
    }
    
    // @todo: this method should probably be moved to a more apriopriate place
    public static function is_logo_set(): bool
    {
        return !empty(LMS_Settings::get_option('logo', null));
    }
}
