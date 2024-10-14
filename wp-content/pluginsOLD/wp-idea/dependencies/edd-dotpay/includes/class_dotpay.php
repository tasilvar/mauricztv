<?php

use bpmj\wpidea\integrations\Interface_External_Service_Integration;
use bpmj\wpidea\integrations\Trait_External_Service_Integration;
use bpmj\wpidea\http\Http_Client;

class Dotpay  implements Interface_External_Service_Integration {
use Trait_External_Service_Integration;

    const SERVICE_NAME = 'Dotpay';

    public function check_connection(): bool
    {
        global $edd_options;

        $dotpay_id = $edd_options[ 'dotpay_id' ] ?? null;
        if(!$dotpay_id){
            return false;
        }
        $fake_url = 'https://ssl.dotpay.pl/test_payment/channels/';

        $client = new Http_Client();
        $response = $client->create_request()
            ->set_url($fake_url)
            ->add_param('id', $dotpay_id)
            ->add_param('amount', '301.00')
            ->add_param('currency', 'PLN')
            ->add_param('lang', 'pl')
            ->add_param('format', 'json')
            ->send();

        if($response->is_error()){
            return false;
        }

        $body = $response->get_decoded_body();
        return isset($body->payment_details);
    }

}

?>
