<?php

namespace bpmj\wpidea;

class Software_Variant_Core {

    const LOCAL_NAME = 'WP Idea';
    const INTRNATIONAL_NAME = 'Idea LMS';

    public function is_international()
    {
        $locale = $this->_get_locale_const();
        return !empty( $locale ) && 'pl_PL' !== $locale;
    }

    public function is_saas()
    {
        return null !== $this->_get_saas_const();
    }

    public function get_name()
    {
        return $this->is_international() ? self::INTRNATIONAL_NAME : self::LOCAL_NAME;
    }

    protected function _get_locale_const() {
        if( defined( 'BPMJ_EDDCM_LOCALE' ) ) {
            return BPMJ_EDDCM_LOCALE;
        }

        return null;
    }

    protected function _get_saas_const() {
        if( defined( 'BPMJ_EDDCM_SAAS' ) ) {
            return BPMJ_EDDCM_SAAS;
        }

        return null;
    }

}
