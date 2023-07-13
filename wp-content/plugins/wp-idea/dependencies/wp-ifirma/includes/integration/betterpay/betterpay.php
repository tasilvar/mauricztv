<?php

/*
 * Tworzy JSON z danymi do wysyłki do ifirma.pl po złożeniu zamówienia w Better Pay
 */

add_action( 'bpmj_betterpay_complete_purchase', 'bpmj_wpifirma_on_betterpay_complete_purchase' );

function bpmj_wpifirma_on_betterpay_complete_purchase( $order_data ) {
	global $bpmj_wpifirma_settings;

	// Jeśli kwota do zapłaty wynosi 0 - koniec
	if ( $order_data[ 'amount' ] == '0.00' ) {
		return;
	}

	$invoice_factory = BPMJ_Invoice_Factory_Betterpay::factory( $order_data, $bpmj_wpifirma_settings, new BPMJ_WP_iFirma() );
	if ( false === $invoice_factory->create_invoice_object() ) {
		return;
	}

	$invoice_factory->store_invoice();
}
