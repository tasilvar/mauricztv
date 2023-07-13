<?php

namespace bpmj\wpidea\admin\settings\infrastructure\persistence\storage_place;

use bpmj\wpidea\settings\Interface_Settings;

class Publigo_Settings_Storage_Place implements Interface_Settings_Storage_Place
{
    private const OFF = 'off';
    private const NO = 'no';
    private const INTEGRATIONS = 'integrations';

    private Interface_Settings $publigo_settings;

    public function __construct(
        Interface_Settings $publigo_settings
    )
    {
        $this->publigo_settings = $publigo_settings;
    }

    public function get_data(string $name)
    {
        $integrations = $this->publigo_settings->get(self::INTEGRATIONS);

        if (strpos($name, 'integration_') === 0) {
            $integration_name = substr($name, 12);
            $value = $integrations[$integration_name] ?? false;
        } else {
            $value = $this->publigo_settings->get($name);
        }


        if($value === self::OFF || $value === self::NO) {
            return false;
        }

        return $value;
    }

    public function update_data(string $name, $value): void
    {
        $integrations = $this->publigo_settings->get(self::INTEGRATIONS);

        if (strpos($name, 'integration_') === 0) {
            $integration_name = substr($name, 12);

            if($value !== 'on') {
                unset($integrations[$integration_name]);
            } else {
                $integrations[$integration_name] = '1';
            }

            $this->publigo_settings->set(self::INTEGRATIONS, $integrations);
        } else {
            $this->publigo_settings->set($name, $value);
        }

    }
}