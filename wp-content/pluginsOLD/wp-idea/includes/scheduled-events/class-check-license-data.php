<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\scheduled_events;

use bpmj\wpidea\admin\settings\Settings_Const;
use bpmj\wpidea\admin\subscription\models\Server_License_Data;
use bpmj\wpidea\options\Options_Const;
use bpmj\wpidea\Software_Variant_Core;
use DateInterval;
use DateTime;
use bpmj\wpidea\settings\Interface_Settings;
use bpmj\wpidea\options\Interface_Options;
use bpmj\wpidea\admin\subscription\services\License_Server_Connector;
use bpmj\wpidea\infrastructure\scheduler\Interface_Schedulable;

class Check_License_Data implements Interface_Schedulable
{
    private $license_server_connector;
    private $options;
    private $settings;
    private $software_variant;

    public function __construct(License_Server_Connector $license_server_connector,
        Interface_Options $options, Interface_Settings $settings, Software_Variant_Core $software_variant)
    {
        $this->license_server_connector = $license_server_connector;
        $this->options = $options;
        $this->settings = $settings;
        $this->software_variant = $software_variant;
    }

    public function get_method_to_run(): callable
    {
        return [$this, 'run'];
    }

    public function run(): void
    {
        if($this->software_variant->is_saas()) {
            return;
        }

        $license_key = empty( $this->settings->get(Settings_Const::LICENSE_KEY) ) ? '' : $this->settings->get(Settings_Const::LICENSE_KEY);

        $this->get_and_update_license_data($license_key);
    }

    public function get_first_run_time(): DateTime
    {
        return new DateTime();
    }

    public function get_interval(): DateInterval
    {
        return new DateInterval(Interface_Schedulable::INTERVAL_1DAY);
    }

    public function get_args(): array
    {
        return [];
    }

    public function get_and_update_license_data(string $license_key): void
    {
        if( empty( $license_key ) ) {
            return;
        }

        $license_data = $this->license_server_connector->check_and_get_license_data($license_key);

        if( $this->license_server_connection_failed($license_data) ) {
            $this->options->set(Options_Const::LICENSE_SERVER_CONNECTION_ERROR, true);
            return;
        }

        $this->options->delete( Options_Const::LICENSE_SERVER_CONNECTION_ERROR );

        if ( Server_License_Data::STATUS_INACTIVE === $license_data->get_status() ) {
            $this->options->delete( Options_Const::WPI_PACKAGE );
            $this->options->delete( Options_Const::WPI_VALIDATED_KEY );
        }

        if ( ! empty( $license_data->get_expires() ) ) {
            $this->options->set( Options_Const::LICENSE_EXPIRATION_DATA, $license_data->get_expires() );
        }
    }

    private function license_server_connection_failed(?Server_License_Data $license_data): bool
    {
        return is_null($license_data);
    }
}