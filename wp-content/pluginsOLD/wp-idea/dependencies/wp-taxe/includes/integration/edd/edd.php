<?php

/*
 * Tworzy JSON z danymi do wysyłki do taxe.pl po złożeniu zamówienia w EDD
 */

// Zakoncz, jeżeli plik jest załadowany bezpośrednio
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'edd_complete_purchase', 'bpmj_wptaxe_on_complete_purchase' );

/**
 * @param int $payment_id
 */
function bpmj_wptaxe_on_complete_purchase( $payment_id ) {
	global $bpmj_wptaxe_settings;

	// Jeśli kwota do zapłaty wynosi 0 - koniec
	if ( edd_get_payment_amount( $payment_id ) == '0.00' ) {
		return;
	}

	$invoice_factory = BPMJ_Invoice_Factory_Edd::factory( $payment_id, $bpmj_wptaxe_settings, new BPMJ_WP_Taxe() );
	if ( false === $invoice_factory->create_invoice_object() ) {
		return;
	}

	$invoice_factory->store_invoice();
}

/**
 * integracja z Recurring Payments
 *
 * @param $payment
 * @param int $parent_id
 */
function bpmj_wptaxe_recurring_payment_received_notice( $payment, $parent_id ) {

	bpmj_wptaxe_on_complete_purchase( $parent_id );
}

add_action( 'edd_recurring_record_payment', 'bpmj_wptaxe_recurring_payment_received_notice', 10, 5 );

/**
 * @param array $src
 */
function bpmj_wptaxe_after_invoice_sent_to_customer( array $src ) {
	if ( isset( $src[ 'src' ] ) && 'edd' === $src[ 'src' ] && ! empty( $src[ 'id' ] ) ) {
		edd_insert_payment_note( $src[ 'id' ], sprintf( __( '%1$s: wysłano dokument do klienta', 'bpmj_wptaxe' ), BPMJ_WPTAXE_NAME ) );
	}
}

add_action( 'bpmj_wptaxe_after_invoice_sent_to_customer', 'bpmj_wptaxe_after_invoice_sent_to_customer' );

/**
 * @param array $src
 * @param string $remote_invoice_number
 */
function bpmj_wptaxe_after_invoice_created( array $src, $remote_invoice_number ) {
	if ( isset( $src[ 'src' ] ) && 'edd' === $src[ 'src' ] && ! empty( $src[ 'id' ] ) ) {
		edd_insert_payment_note( $src[ 'id' ], sprintf( __( '%1$s: wystawiono dokument o numerze %2$s', 'bpmj_wptaxe' ), BPMJ_WPTAXE_NAME, $remote_invoice_number ) );
	}
}

add_action( 'bpmj_wptaxe_after_invoice_created', 'bpmj_wptaxe_after_invoice_created', 10, 2 );
