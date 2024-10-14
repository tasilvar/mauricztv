<?php

/**
 * Given a Payment ID, extract the transaction ID from Stripe
 *
 * @param  string $payment_id       Payment ID
 * @return string                   Transaction ID
 */
function edds_get_payment_transaction_id( $payment_id ) {

	$txn_id = '';
	$notes  = edd_get_payment_notes( $payment_id );

	foreach ( $notes as $note ) {
		if ( preg_match( '/^Stripe Charge ID: ([^\s]+)/', $note->comment_content, $match ) ) {
			$txn_id = $match[1];
			continue;
		}
	}

	return apply_filters( 'edds_set_payment_transaction_id', $txn_id, $payment_id );
}
add_filter( 'edd_get_payment_transaction_id-stripe', 'edds_get_payment_transaction_id', 10, 1 );

/**
 * Given a transaction ID, generate a link to the Stripe transaction ID details
 *
 * @since  1.9.1
 * @param  string $transaction_id The Transaction ID
 * @param  int    $payment_id     The payment ID for this transaction
 * @return string                 A link to the Stripe transaction details
 */
function edd_stripe_link_transaction_id( $transaction_id, $payment_id ) {

	$test = edd_get_payment_meta( $payment_id, '_edd_payment_mode' ) === 'test' ? 'test/' : '';
	$url  = '<a href="https://dashboard.stripe.com/' . $test . 'payments/' . $transaction_id . '" target="_blank">' . $transaction_id . '</a>';

	return apply_filters( 'edd_stripe_link_payment_details_transaction_id', $url );

}
add_filter( 'edd_payment_details_transaction_id-stripe', 'edd_stripe_link_transaction_id', 10, 2 );
