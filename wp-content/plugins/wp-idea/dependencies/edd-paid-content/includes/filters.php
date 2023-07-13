<?php

/**
 * @param array|bool|string $interval
 * @param int $download_id
 * @param int $price_id
 * @param bool $raw
 *
 * @return array|bool|string
 */
function bpmj_eddpc_recurring_interval( $interval, $download_id, $price_id = 0, $raw = false ) {
	$payment_interval_number = (int) ( $price_id ? bpmj_eddpc_get_access_time_variable( $download_id, $price_id ) : bpmj_eddpc_get_access_time_single( $download_id ) );
	$payment_interval_unit   = $price_id ? bpmj_eddpc_get_access_time_unit_variable( $download_id, $price_id ) : bpmj_eddpc_get_access_time_unit_single( $download_id );

	$result = false;

	if ( $payment_interval_number > 0 ) {
		if ( key_exists( $payment_interval_unit, edd_recurring_get_interval_units() ) ) {
			$result = true;
		} else if ( in_array( $payment_interval_unit, array( 'hours', 'minutes' ) ) ) {
			// For minutes and hours intervals we assume the interval is 1 day
			$result                  = true;
			$payment_interval_number = 1;
			$payment_interval_unit   = 'days';
		}
	}

	if ( $result ) {
		if ( $raw ) {
			$result = $payment_interval_number . ' ' . $payment_interval_unit;
		} else {
			$result = array( 'number' => $payment_interval_number, 'unit' => $payment_interval_unit );
		}
	} else {
		return $interval;
	}

	return $result;
}

add_filter( 'edd_recurring_get_interval', 'bpmj_eddpc_recurring_interval', 10, 4 );

function bpmj_eddpc_get_next_payment_date( $next_payment_date, $download_id ) {
	$bpmj_eddpc_access_start_enabled = get_post_meta( $download_id, '_bpmj_eddpc_access_start_enabled', true ) ? true : false;
	if ( $bpmj_eddpc_access_start_enabled ) {
		$bpmj_eddpc_access_start    = esc_attr( get_post_meta( $download_id, '_bpmj_eddpc_access_start', true ) );
		$bpmj_eddpc_access_start_ts = strtotime( $bpmj_eddpc_access_start );
		if ( false !== $bpmj_eddpc_access_start_ts ) {
			/*
			 * If access time is enabled and is in the future, we add the difference between now and access time to
			 * next payment date
			 */
			$diff = date_diff( new DateTime( date( 'Y-m-d' ) ), new DateTime( date( 'Y-m-d', $bpmj_eddpc_access_start_ts ) ) );
			if ( $diff->days > 0 && 0 === $diff->invert ) {
				$next_payment_date = date( 'Y-m-d', strtotime( $diff->format( '%R%d days' ), strtotime( $next_payment_date ) ) );
			}
		}
	}

	return $next_payment_date;
}

add_filter( 'edd_recurring_get_next_payment_date', 'bpmj_eddpc_get_next_payment_date', 10, 2 );