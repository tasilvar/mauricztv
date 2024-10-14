<?php

use bpmj\wpidea\wolverine\event\Events;
use bpmj\wpidea\wolverine\order\Order;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function trigger_event_on_payment_complete($download_id = 0, $payment_id = 0)
{
    Events::trigger(Order::EVENT_ON_ORDER_COMPLETE, [
        'payment_id' => $payment_id,
        'download_id' => $download_id
    ]);

}
add_action('edd_complete_download_purchase', 'trigger_event_on_payment_complete', 10, 2);
