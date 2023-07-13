<?php

use bpmj\wpidea\integrations\Interface_External_Service_Integration;
use bpmj\wpidea\integrations\Trait_External_Service_Integration;
use Paynow\Environment;
use Paynow\Client;
use Paynow\Service\ShopConfiguration;
use Paynow\Service\Payment;
use Paynow\Notification;
use Paynow\Model\Payment\Status;
use Paynow\Exception\SignatureVerificationException;
use Paynow\Exception\PaynowException;

class Paynow implements Interface_External_Service_Integration {
use Trait_External_Service_Integration;

    const SERVICE_NAME = 'Paynow';

    public function check_connection(): bool
    {
        global $edd_options;

        $environment = 'production' === $edd_options['paynow_environment'] ?
            Environment::PRODUCTION :
            Environment::SANDBOX;

        $client = new Client(
            $edd_options['paynow_access_key'] ?? '',
            $edd_options['paynow_signature_key'] ?? '',
            $environment,
            get_bloginfo('name')
        );
        $fake_data = [
            "amount" => "100"
        ];


        try {
            $payment = new Payment($client);
            $payment->authorize($fake_data, uniqid());
        } catch (PaynowException $exception) {
            return false;
        }

        return true;
    }

}

?>
