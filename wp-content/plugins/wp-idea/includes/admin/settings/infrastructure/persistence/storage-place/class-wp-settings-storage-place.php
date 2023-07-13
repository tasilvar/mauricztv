<?php

namespace bpmj\wpidea\admin\settings\infrastructure\persistence\storage_place;

class WP_Settings_Storage_Place implements Interface_Settings_Storage_Place
{
    private const ON = 'on';
    private const OFF = 'off';

    public function get_data(string $name)
    {
        $value = $this->get_option($name) ?? null;

        if ($value === '0') {
            return false;
        }

        return $value ?? null;
    }

    public function update_data(string $name, $value): void
    {

        if($value === self::ON){
            $value = 1;
        }

        if($value === self::OFF){
            $value = 0;
        }

        $this->update_option($name, $value);
    }

    private function get_option(string $name)
    {
        return get_option($name);
    }

    private function update_option(string $name, $value): void
    {
        update_option($name, $value);
    }
}