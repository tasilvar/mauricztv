<?php

namespace bpmj\wpidea\admin\settings\infrastructure\persistence\storage_place;

class Edd_Settings_Storage_Place implements Interface_Settings_Storage_Place
{
    private const ON = 'on';
    private const OFF = 'off';

    public function get_data(string $name)
    {
        $edd_settings = get_option('edd_settings');

        if (strpos($name, 'gateway_') === 0) {
            $gateway_name = substr($name, 8);
            $value = $edd_settings['gateways'][$gateway_name] ?? false;
        } else {
            $value = $edd_settings[$name] ?? null;
        }

       if($value === '-1'){
           return false;
       }

       return $value ?? null;

    }

    public function update_data(string $name, $value): void
    {
        $edd_settings = get_option('edd_settings');

        if($value === self::ON){
            $value = '1';
        }

        if($value === self::OFF){
            $value = '-1';
        }

        if (strpos($name, 'gateway_') === 0) {
            $gateway_name = substr($name, 8);

            if($value !== '1') {
                unset($edd_settings['gateways'][$gateway_name]);
            } else {
                $edd_settings['gateways'][$gateway_name] = $value;
            }
        } else {
            $edd_settings[$name] = $value;
        }

        update_option('edd_settings', $edd_settings);
    }
}