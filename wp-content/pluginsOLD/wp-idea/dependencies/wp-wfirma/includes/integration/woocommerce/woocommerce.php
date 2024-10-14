<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//add_action('woocommerce_payment_complete', 'bpmj_wpwf_on_woo_complete_purchase'); // nie działa z bramką transferuj.pl (nie wywyołują payment_complete)
add_action( 'woocommerce_order_status_completed', 'bpmj_wpwf_on_woo_complete_purchase' ); // alternatywa dla powyższego - do ustawień?

function bpmj_wpwf_on_woo_complete_purchase( $order_id ) {
	global $bpmj_wpwf_settings;

	$order = new WC_Order( $order_id );

    $shipping = is_callable( array(
        $order,
        'get_shipping_total'
    ) ) ? $order->get_shipping_total() : $order->get_total_shipping();
	$total    = round( $order->get_total() - $shipping, 2 );

	// Jeśli kwota do zapłaty wynosi 0 - koniec
	if ( 0 == $total ) {
		return;
	}

	$invoice_factory = BPMJ_Invoice_Factory_Woocommerce::factory( $order, $bpmj_wpwf_settings, new BPMJ_WP_wFirma() );
	if ( false === $invoice_factory->create_invoice_object() ) {
		return;
	}

	$invoice_factory->store_invoice();
}
