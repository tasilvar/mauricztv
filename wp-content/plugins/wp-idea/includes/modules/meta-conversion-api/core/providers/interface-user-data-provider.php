<?php

namespace bpmj\wpidea\modules\meta_conversion_api\core\providers;

use bpmj\wpidea\sales\order\client\Client;
use bpmj\wpidea\sales\order\invoice\Invoice;
use FacebookAds\Object\ServerSide\UserData;

interface Interface_User_Data_Provider
{
    public function get_user_data_for_logged_user(): UserData;

    public function get_user_data(?Client $client = null, ?Invoice $invoice = null): UserData;
}