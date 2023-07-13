<?php

namespace bpmj\wpidea;

class Software_Variant {

    public static function is_international()
    {
        return WPI()->container->get(Software_Variant_Core::class)->is_international();
    }

    public static function is_saas()
    {
        return WPI()->container->get(Software_Variant_Core::class)->is_saas();
    }

    public static function get_name()
    {
        return (new Software_Variant_Core())->get_name();
    }

    public static function get_variant_name()
    {
        if(self::is_saas()){
            return 'GO';
        } else {
            return 'BOX';
        }
    }

}
