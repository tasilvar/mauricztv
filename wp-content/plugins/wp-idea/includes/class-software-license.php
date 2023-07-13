<?php

namespace bpmj\wpidea;

class Software_License {
    public static function is_valid()
    {
		return 'valid' === self::get_status() ? true : false;
    }

    public static function get_status()
    {
        return get_option('bpmj_eddcm_license_status');
    }
}