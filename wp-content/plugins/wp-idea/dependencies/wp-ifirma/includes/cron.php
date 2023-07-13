<?php

/*
 * W harmonogramie wykonują się następujące czynności:
 */


// Zakoncz, jeżeli plik jest załadowany bezpośrednio
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// prevent firing several times
if ( get_transient( 'bpmj_wpifirma_doing_cron' ) ) {
    return;
}

set_transient( 'bpmj_wpifirma_doing_cron', true, 55 ); // 55 sekund

$args = array(
	'posts_per_page' => 1,
	'post_type'      => 'bpmj_wp_ifirma',
	'meta_key'       => 'ifirma_status',
	'meta_value'     => 'pending',
    'orderby'        => 'ID',
    'order'          => 'ASC'
);

$invoices = get_posts( $args );

$processing_disabled_until = get_option( 'bpmj_wpifirma_processing_disabled_until' );
if ( $processing_disabled_until ) {
	if ( $processing_disabled_until > current_time( 'mysql' ) ) {
		return;
	} else {
		delete_option( 'bpmj_wpifirma_processing_disabled_until' );
	}
}

if ( ! empty( $invoices ) && is_array( $invoices ) ) {

	/** @var WP_Post $invoice */
	foreach ( $invoices as $invoice ) {

		// Utworzenie obiektu
		$invoice_object = new BPMJ_WP_iFirma();
		$invoice_object->set_from_invoice_post( $invoice->ID );

		// Próba wysyłki danych do iFirma.pl
		$invoice_object->send_invoice();

		if ( get_option( 'bpmj_wpifirma_processing_disabled_until' ) ) {
			// Jeśli ta wartość została ustawiona to przerywamy wysyłkę faktur - coś jest mocno nie tak z konfiguracją
			break;
		}
	}
}

delete_transient( 'bpmj_wpifirma_doing_cron' );
