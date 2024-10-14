<?php

namespace bpmj\wpidea\admin\settings\infrastructure\persistence\storage_place;

class Fakturownia_Storage_Place implements Interface_Settings_Storage_Place
{
    private const ON = 'on';
    private const OFF = 'off';

    public function get_data(string $name)
    {
        global $bpmj_wpfa_settings;

        $settings = $bpmj_wpfa_settings ?? get_option('bpmj_wpfa_settings');

        $value = $settings[$name] ?? null;

        if ($value === '0') {
            return false;
        }

        return $value ?? null;
    }

    public function update_data(string $name, $value): void
    {
        global $bpmj_wpfa_settings;

        $settings = $bpmj_wpfa_settings ?? get_option('bpmj_wpfa_settings');

        if($value === self::ON){
            $value = 1;
        }

        if($value === self::OFF){
            $value = 0;
        }

        $settings[$name] = $value;

        $bpmj_wpfa_settings = $settings;

        update_option('bpmj_wpfa_settings', $bpmj_wpfa_settings);
    }
}