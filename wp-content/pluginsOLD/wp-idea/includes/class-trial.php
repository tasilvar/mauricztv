<?php

namespace bpmj\wpidea;

class Trial
{

    const SETTINGS_SLUG = 'wp_idea';
    const SETTINGS_KEY_SLUG = 'license_key';
    const BMJ_KEY_SLUG = 'bmpj_wpidea_vkey';
    const PACKAGE_SLUG = 'wpidea_package';
    const TRIAL_EXPIRATION_DATE_SLUG = 'trial_version_expiration_date';

    private $key;
    private $trial_version_expiration_date;


    public function set_key($key)
    {
        $this->key = $key;
    }

    public function set_trial_version_expiration_date($trial_version_expiration_date)
    {
        $this->trial_version_expiration_date = $trial_version_expiration_date;
    }

    public function save()
    {
        $wpidea_settings = get_option(self::SETTINGS_SLUG);

        if(!is_null($this->trial_version_expiration_date)){
            $wpidea_settings[self::TRIAL_EXPIRATION_DATE_SLUG] = $this->trial_version_expiration_date;
        }

        if($this->key){
            $license_info = WPI()->packages->get_license_info( $this->key );
            if(!$license_info->price_id){
                return false;
            }

            $wpidea_settings[self::SETTINGS_KEY_SLUG] = $this->key;
            update_option(self::BMJ_KEY_SLUG, $this->key);

            $packageString = WPI()->packages->convert_price_id_to_package_name( $license_info->price_id );
            update_option( self::PACKAGE_SLUG, $packageString );
        }


        update_option(self::SETTINGS_SLUG, $wpidea_settings);
        return true;
    }

    public function update_key_and_disable_trial($key)
    {
       $this->set_key($key);
       $this->set_trial_version_expiration_date('');
       return $this->save();
    }

    public function change_trial_version_expiration_date($date)
    {
        $this->set_trial_version_expiration_date($date);
        return $this->save();
    }

    public static function get_key()
    {
        return get_option('bmpj_wpidea_vkey');
    }
}
