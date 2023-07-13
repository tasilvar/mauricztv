<?php

namespace bpmj\wpidea\admin\settings\infrastructure\persistence\storage_place;

class Taxe_Storage_Place implements Interface_Settings_Storage_Place
{
    private const ON = 'on';
    private const OFF = 'off';
    private const PREFIX = 'taxe_';

    public function get_data(string $name)
    {
        global $bpmj_wptaxe_settings;

        $settings = $bpmj_wptaxe_settings ?? get_option('bpmj_wptaxe_settings');

        if (strpos($name, self::PREFIX) === 0) {
            $name = substr($name, 5);
        }

        $value = $settings[$name] ?? null;

        if ($value === '0') {
            return false;
        }

        return $value ?? null;
    }

    public function update_data(string $name, $value): void
    {
        global $bpmj_wptaxe_settings;

        $settings = $bpmj_wptaxe_settings ?? get_option('bpmj_wptaxe_settings');

        if (strpos($name, self::PREFIX) === 0) {
            $name = substr($name, 5);
        }

        if($value === self::ON){
            $value = 1;
        }

        if($value === self::OFF){
            $value = 0;
        }

        $settings[$name] = $value;

        $bpmj_wptaxe_settings = $settings;

        update_option('bpmj_wptaxe_settings', $settings);
    }
}