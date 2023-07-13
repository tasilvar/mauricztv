<?php

namespace bpmj\wpidea\integrations;

use bpmj\wpidea\Current_Request;

if (!defined('ABSPATH'))
    exit;

class Integrations_Connect
{
    const CHECK_INTEGRATION_CONNECTION_NAME_REQUEST = 'check-integrations-connection-name';
    const CHECK_INTEGRATION_CONNECTION_TYPE_REQUEST = 'check-integrations-connection-type';

    const CRON_CHECK_CONNECTION_NAME = 'cron_check_integrations_connection';
    const CRON_CHECK_CONNECTION_RECURRENCE = 'weekly';

    const SETTINGS_UPDATED_REQUEST = 'settings-updated';

    const CONNECTION_ERROR_INTEGRATIONS_SLUG = 'connection-error-integrations';


    public function __construct()
    {
        $this->add_hooks();
    }

    private function add_hooks(): void
    {
        $this->check_connection_after_save_wpi_settings();
        $this->check_connection_action();
    }

    private function check_connection_after_save_wpi_settings(): void
    {
        $request = new Current_Request();
        if($request->query_arg_exists(self::SETTINGS_UPDATED_REQUEST) !== true){
            return;
        }
        $this->check_integration_connections();
    }

    private function check_connection_action(): void
    {
        $request = new Current_Request();
        if (
            !$request->query_arg_exists(self::CHECK_INTEGRATION_CONNECTION_NAME_REQUEST) ||
            !$request->query_arg_exists(self::CHECK_INTEGRATION_CONNECTION_TYPE_REQUEST)
        ) {
            return;
        }
        $name = $request->get_query_arg(self::CHECK_INTEGRATION_CONNECTION_NAME_REQUEST);
        $type = $request->get_query_arg(self::CHECK_INTEGRATION_CONNECTION_TYPE_REQUEST);

        $integrations_to_check = [];
        $integrations_to_check[$type][] = $name;

        $this->check_integration_connections($integrations_to_check);
    }


    public static function get_connection_error_integrations()
    {
        return get_option(self::CONNECTION_ERROR_INTEGRATIONS_SLUG);
    }

    public static function update_connection_error_integrations(?array $connection_error_integrations): void
    {
        update_option(self::CONNECTION_ERROR_INTEGRATIONS_SLUG, $connection_error_integrations);
    }

    private function remove_integration_from_connection_error_integrations(string $integration_name_to_remove): void
    {
        $integrations = self::get_connection_error_integrations();
        foreach ($integrations as $type => $names){
            foreach ($names as $key => $name) {
                if($integration_name_to_remove == $name){
                    unset($integrations[$type][$key]);
                }
            }
        }

        self::update_connection_error_integrations($integrations);
    }

    public static function add_connection_error(string $type, string $name): void
    {
        $connection_error_integrations = self::get_connection_error_integrations();
        $connection_error_integrations[$type][] = $name;
        self::update_connection_error_integrations($connection_error_integrations);
    }

    public function check_integration_connections(?array $integrations = null): void
    {
        if(!$integrations){
            $integrations = Integrations_Catch_Changed::get_changed_integrations();
            Integrations_Catch_Changed::reset_changed_integrations();
        }

        foreach ($integrations as $type => $names){
            foreach ($names as $name) {
                $this->remove_integration_from_connection_error_integrations_if_exists($name);
                $this->check_connection_and_add_to_connection_error($type, $name);
            }
        }
    }

    private function remove_integration_from_connection_error_integrations_if_exists(string $integration_name_to_remove): void
    {
        $integrations = self::get_connection_error_integrations();
        if(!$integrations){
            return;
        }
        foreach ($integrations as $type => $names){

            if(!$names){
                continue;
            }

            foreach ($names as $name) {
                if ($integration_name_to_remove == $name) {
                    $this->remove_integration_from_connection_error_integrations($name);
                }
            }
        }

    }

    public function check_connection_and_add_to_connection_error(string $type, string $name): void
    {
        if (!Integrations::check_is_enabled($type, $name) ||  $this->check_is_manual_getaway($name)) {
            return;
        }

        if(!Integrations::is_check_manually($type, $name)){
            Integrations_Notices::check_manually_notice($type, $name);
            return;
        }

        $integration = Integrations::get_integration_model($type, $name);
        if(!$integration->check_connection()){
            self::add_connection_error($type, $name);
        }
    }

    private function check_is_manual_getaway(string $name): bool
    {
        return $name == 'manual';
    }

}
