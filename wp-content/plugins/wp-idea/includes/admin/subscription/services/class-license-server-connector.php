<?php
/**
 * This file is licenses under proprietary license
 */

namespace bpmj\wpidea\admin\subscription\services;

use bpmj\wpidea\admin\subscription\models\Server_License_Data;
use Exception;
use GuzzleHttp\ClientInterface;

class License_Server_Connector
{
    private const ACTION_ACTIVATE_LICENSE = 'activate_license';
    private const ACTION_CHECK_LICENSE = 'check_license';

    private $http_client;

    public function __construct(ClientInterface $http_client)
    {
        $this->http_client = $http_client;
    }

    public function activate_and_get_license_data(string $key): ?Server_License_Data
    {
        return $this->get_license_data(
            $key,
            BPMJ_EDDCM_NAME,
            self::ACTION_ACTIVATE_LICENSE
        );
    }

    public function check_and_get_license_data(string $key): ?Server_License_Data
    {
        return $this->get_license_data(
            $key,
            BPMJ_EDDCM_NAME,
            self::ACTION_CHECK_LICENSE
        );
    }

    private function get_license_data( string $license_key, string $item_name, string $action ): ?Server_License_Data
    {
        if ( empty( $license_key ) ) {
            return null;
        }

        $api_params = [
            'edd_action' => $action,
            'license'    => $license_key,
            'item_name'  => urlencode( $item_name ),
        ];

        try {
            $response = $this->http_client->get(
                BPMJ_UPSELL_STORE_URL,
                [
                    'query'   => $api_params,
                    'timeout' => 15,
                    'verify'  => false
                ]
            );
        }
        catch(Exception $e) {
            return null;
        }

        if ( 200 !== $response->getStatusCode() ) {
            return null;
        }

        $response_as_array = json_decode( $response->getBody()->getContents(), true );

        return new Server_License_Data(
            $response_as_array['license'],
            $response_as_array['expires'] ?? null,
            $response_as_array['payment_id'] ? (int)$response_as_array['payment_id'] : null,
            $response_as_array['price_id'] ? (int)$response_as_array['price_id'] : null,
            $response_as_array['customer_email'] ?? null,
            $response_as_array['customer_name'] ?? null
        );
    }
}
