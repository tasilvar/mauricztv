<?php

function bpmj_eddpayu_cron_schedule_10_mins( $schedules ) {
	$schedules[ 'bpmj_eddpayu_once_every_10_min' ] = array(
		'interval' => 60 * 10,
		'display'  => __( 'Once every 10 minutes', BPMJ_EDDPAYU_DOMAIN ),
	);

	return $schedules;
}

add_filter( 'cron_schedules', 'bpmj_eddpayu_cron_schedule_10_mins' );

/**
 * @param array $meta_queries
 *
 * @return array
 */
function bpmj_eddpayu_edd_recurring_payment_meta_queries( array $meta_queries ) {
	$meta_queries[] = array(
		'key'   => '_payu_payment_subtype',
		'value' => 'recurrent',
	);

	return $meta_queries;
}

add_filter( 'edd_recurring_payment_meta_queries', 'bpmj_eddpayu_edd_recurring_payment_meta_queries' );

/**
 * @param string $mode
 * @param int $payment_id
 *
 * @return string
 */
function bpmj_eddpayu_get_payment_charge_mode( $mode, $payment_id ) {
	if ( 'recurrent' === get_post_meta( $payment_id, '_payu_payment_subtype', true ) ) {
		if ( (bool) get_post_meta( $payment_id, '_payu_recurrent_payment_token', true ) ) {
			return 'automatic';
		}

		return 'manual';
	}

	return $mode;
}

add_filter( 'edd_get_payment_charge_mode', 'bpmj_eddpayu_get_payment_charge_mode', 10, 2 );