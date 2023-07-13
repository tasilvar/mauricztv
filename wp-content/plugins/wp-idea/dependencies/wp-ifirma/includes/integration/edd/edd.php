<?php

/*
 * Tworzy JSON z danymi do wysyłki do ifirma.pl po złożeniu zamówienia w EDD
 */

// Zakoncz, jeżeli plik jest załadowany bezpośrednio
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'edd_complete_purchase', 'bpmj_wpifirma_on_complete_purchase' );

//add_action( 'edd_view_order_details_resend_receipt_after', 'bpmj_wpifirma_add_edd_metabox' );

/**
 * @param int $payment_id
 */
function bpmj_wpifirma_on_complete_purchase( $payment_id ) {
	global $bpmj_wpifirma_settings;

	// Jeśli kwota do zapłaty wynosi 0 - koniec
	if ( edd_get_payment_amount( $payment_id ) == '0.00' ) {
		return;
	}

	$invoice_factory = BPMJ_Invoice_Factory_Edd::factory( $payment_id, $bpmj_wpifirma_settings, new BPMJ_WP_iFirma() );
	if ( false === $invoice_factory->create_invoice_object() ) {
		return;
	}

	$invoice_factory->store_invoice();
}

/**
 * Register the metabox on the 'payment' post type
 */
function bpmj_wpifirma_add_edd_metabox() {
	?>
	<br>
	<form method="post">
		<input type="submit"
		name="bpmj_send_payment_invoice" id="bpmj_send_payment_invoice" 
		class="button-secondary" 
		value="<?php _e('Wystaw fakturę', 'bpmj_wp_ifirma' ); ?> (WP iFirma)" /><br/>
	</form>
	
	<?php
	if( !empty($_GET['id']) && !empty($_GET['bpmj_send_payment_invoice']) ) $payment_id = absint( $_GET['id'] );

	if( $payment_id ){
		$payment      = new EDD_Payment( $payment_id );

		if( $payment->status == 'publish' ){

			if( array_key_exists('bpmj_send_payment_invoice',$_POST )){
				bpmj_wpifirma_on_complete_purchase( $payment_id );
				_e( 'Wystawiono fakturę.', 'bpmj_wp_ifirma' );
			}

		} else {
			_e( 'Możesz wystawić fakturę tylko dla zakończonych płatności', 'bpmj_wp_ifirma' );
		}
		
	}

}



/**
 * integracja z Recurring Payments
 *
 * @param $payment
 * @param int $parent_id
 */
function bpmj_wpifirma_recurring_payment_received_notice( $payment, $parent_id ) {

	bpmj_wpifirma_on_complete_purchase( $parent_id );
}

add_action( 'edd_recurring_record_payment', 'bpmj_wpifirma_recurring_payment_received_notice', 10, 5 );

/**
 * @param array $src
 */
function bpmj_wpifirma_after_invoice_sent_to_customer( array $src ) {
	if ( isset( $src[ 'src' ] ) && 'edd' === $src[ 'src' ] && ! empty( $src[ 'id' ] ) ) {
		edd_insert_payment_note( $src[ 'id' ], sprintf( __( '%1$s: wysłano dokument do klienta', 'bpmj_wpifirma' ), BPMJ_WPIFIRMA_NAME ) );
	}
}

add_action( 'bpmj_wpifirma_after_invoice_sent_to_customer', 'bpmj_wpifirma_after_invoice_sent_to_customer' );

/**
 * @param array $src
 * @param string $remote_invoice_number
 */
function bpmj_wpifirma_after_invoice_created( array $src, $remote_invoice_number ) {
	if ( isset( $src[ 'src' ] ) && 'edd' === $src[ 'src' ] && ! empty( $src[ 'id' ] ) ) {
		edd_insert_payment_note( $src[ 'id' ], sprintf( __( '%1$s: wystawiono dokument o numerze %2$s', 'bpmj_wpifirma' ), BPMJ_WPIFIRMA_NAME, $remote_invoice_number ) );
	}
}

add_action( 'bpmj_wpifirma_after_invoice_created', 'bpmj_wpifirma_after_invoice_created', 10, 2 );
