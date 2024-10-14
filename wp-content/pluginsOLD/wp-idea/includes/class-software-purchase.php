<?php

namespace bpmj\wpidea;

class Software_Purchase {
    
    const IDEALMS_OFFER_URL = 'https://idealms.io/?utm_source=idelms&utm_medium=software&utm_campaign=trial';
    const WPIDEA_OFFER_URL = 'https://wpidea.pl/?utm_source=wpidea&utm_medium=software&utm_campaign=trial';

    const GO_PRICING_URL = 'https://wpidea.pl/f/pricing/go/';
    const BOX_PRICING_URL = 'https://wpidea.pl/f/pricing/box/';
    
    public static function get_purchase_link() {
        if( Software_Variant::is_international() ) {
            return self::IDEALMS_OFFER_URL;
        }
        
        return self::WPIDEA_OFFER_URL;
    }    
}