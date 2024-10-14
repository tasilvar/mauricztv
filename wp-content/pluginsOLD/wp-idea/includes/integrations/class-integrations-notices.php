<?php

namespace bpmj\wpidea\integrations;

// Exit if accessed directly


if (!defined('ABSPATH'))
    exit;

class Integrations_Notices
{
    const INTEGRATIONS_NOTICES_SLUG = 'wpi_integrations_notices';

    const TYPE_INVOICES = 'invoices';
    const TYPE_GATEWAYS = 'gates';
    const TYPE_MAILERS = 'mailers';


    public function __construct()
    {
        $this->add_hooks();
    }

    private function add_hooks(): void
    {
        $this->prepare_notices();
        $this->display_notices();
    }

    public static function update_integrations_notices_option(?array $integrations): ?bool
    {
        return update_option( self::INTEGRATIONS_NOTICES_SLUG, $integrations);
    }

    private static function get_integrations_notices_option(): ?array
    {
        return (get_option(self::INTEGRATIONS_NOTICES_SLUG) != false) ? get_option(self::INTEGRATIONS_NOTICES_SLUG) : [];
    }

    private function display_notices(): void
    {
       foreach (self::get_integrations_notices_option() as $integrations_notice){
            WPI()->notices->display_notice($integrations_notice['message'], $integrations_notice['type'] );
        }
    }

    private static function remove_all_notices(): void
    {
        self::update_integrations_notices_option([]);
    }

    private function prepare_notices(): void
    {
        $this->remove_all_notices();
        $integrations = Integrations_Connect::get_connection_error_integrations();
        $notices = [];

        if(!$integrations){
            return;
        }
        foreach ($integrations as $type => $names){

            if(!$names){
                continue;
            }

            foreach ($names as $name){

                if(!Integrations::check_is_enabled($type, $name)){
                    continue;
                }

                $integration = Integrations::get_integration_model($type, $name);
                $notice_message = sprintf( __( 'Connection error with %s. Check your details. ', BPMJ_EDDCM_DOMAIN ), $integration->get_service_name() );
                $notice_type = WPI()->notices::TYPE_ERROR;
                $notices[] =  ['message' => $notice_message, 'type' => $notice_type];
            }
        }

        self::update_integrations_notices_option($notices);
    }

    public static function check_manually_notice(string $type, string $name): void
    {
        $integration = Integrations::get_integration_model($type, $name);
        $notice_message = sprintf( __( 'Check connection manually for %s. ', BPMJ_EDDCM_DOMAIN ), $integration->get_service_name() );
        $notice_type = WPI()->notices::TYPE_WARNING;

        WPI()->notices->display_notice($notice_message, $notice_type );
    }
}
