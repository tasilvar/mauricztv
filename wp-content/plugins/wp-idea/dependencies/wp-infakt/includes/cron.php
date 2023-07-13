<?php

/*
 * W harmonogramie wykonują się następujące czynności:
 */


// Zakoncz, jeżeli plik jest załadowany bezpośrednio
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// prevent firing several times
if ( get_transient( 'bpmj_wpinfakt_doing_cron' ) ) {
    return;
}

set_transient( 'bpmj_wpinfakt_doing_cron', true, 55 ); // 55 sekund

$args = array(
	'posts_per_page' => 1,
	'post_type'      => 'bpmj_wp_infakt',
	'meta_key'       => 'infakt_status',
	'meta_value'     => 'pending',
    'orderby'        => 'ID',
    'order'          => 'ASC'
);

$invoices = get_posts( $args );


if ( ! empty( $invoices ) && is_array( $invoices ) ) {

	/** @var WP_Post $invoice */
	foreach ( $invoices as $invoice ) {

		// Utworzenie obiektu
		$invoice_object = new BPMJ_WP_Infakt();
		$invoice_object->set_from_invoice_post( $invoice->ID );

		// Próba wysyłki danych do infakt.pl
		$invoice_object->send_invoice();
	}
}

delete_transient( 'bpmj_wpinfakt_doing_cron' );
