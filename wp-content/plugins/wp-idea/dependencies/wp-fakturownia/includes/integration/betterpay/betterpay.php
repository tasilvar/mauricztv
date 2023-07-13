<?php

/*
 * Tworzy JSON z danymi do wysyłki do fakturownia.pl po złożeniu zamówienia w Better Pay
 */

add_action( 'bpmj_betterpay_complete_purchase', 'bpmj_wpfa_on_betterpay_complete_purchase' );

function bpmj_wpfa_on_betterpay_complete_purchase( $order_data ) {
	global $bpmj_wpfa_settings;

	// Jeśli kwota do zapłaty wynosi 0 - koniec
	if ( $order_data[ 'amount' ] == '0.00' ) {
		return;
	}

	$invoice_factory = BPMJ_Invoice_Factory_Betterpay::factory( $order_data, $bpmj_wpfa_settings, new BPMJ_WP_Fakturownia() );
	if ( false === $invoice_factory->create_invoice_object() ) {
		return;
	}

	$invoice_factory->store_invoice( array(
		'oid'         => str_replace( array(
				"http://",
				"https://"
			), "", site_url() ) . " #" . $order_data[ 'control' ],
		'description' => str_replace( array(
				"http://",
				"https://"
			), "", site_url() ) . " #" . $order_data[ 'control' ],
	) );
}
