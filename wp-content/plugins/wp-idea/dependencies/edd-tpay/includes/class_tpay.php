<?php

use bpmj\wpidea\integrations\Interface_External_Service_Integration;
use bpmj\wpidea\integrations\Trait_External_Service_Integration;

class Tpay implements Interface_External_Service_Integration {
use Trait_External_Service_Integration;

    const SERVICE_NAME = 'Tpay';

    public function check_connection(): bool
    {
        // fixed return, tpay is manually check connection
       return false;
    }

}

?>
