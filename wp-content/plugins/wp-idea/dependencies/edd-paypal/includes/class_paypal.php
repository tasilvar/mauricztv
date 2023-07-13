<?php

use bpmj\wpidea\integrations\Interface_External_Service_Integration;
use bpmj\wpidea\integrations\Trait_External_Service_Integration;


class PayPal  implements Interface_External_Service_Integration{
    use Trait_External_Service_Integration;

    const SERVICE_NAME = 'PayPal';

    public function check_connection(): bool
    {
        // fixed return, paypal is manually check connection
        return false;
    }

}

?>
