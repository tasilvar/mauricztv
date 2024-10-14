<?php

use bpmj\wpidea\integrations\Interface_External_Service_Integration;
use bpmj\wpidea\integrations\Trait_External_Service_Integration;

class Payu implements Interface_External_Service_Integration {
use Trait_External_Service_Integration;

    const SERVICE_NAME = 'Payu';

    public function check_connection(): bool
    {
        OpenPayU_Configuration::setEnvironment(edd_get_option( 'payu_api_environment' ));
        OpenPayU_Configuration::setMerchantPosId( edd_get_option( 'payu_pos_id' ) );
        OpenPayU_Configuration::setSignatureKey(  edd_get_option( 'payu_key2' ) );
        OpenPayU_Configuration::setOauthClientId( edd_get_option( 'payu_pos_id' ) );
        OpenPayU_Configuration::setOauthClientSecret(  edd_get_option( 'payu_key1' ) );

        try {
            $response = OpenPayU_Retrieve::payMethods();
            if($response->getStatus() == 'SUCCESS'){
                return true;
            }
        } catch (OpenPayU_Exception $exception) {
            return false;
        }
        return false;
    }

}

?>
