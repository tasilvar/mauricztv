<?php

/*
 * W harmonogramie wykonują się następujące czynności:
 */


// Zakoncz, jeżeli plik jest załadowany bezpośrednio
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// prevent firing several times
if ( get_transient( 'bpmj_wptaxe_doing_cron' ) ) {
    return;
}

set_transient( 'bpmj_wptaxe_doing_cron', true, 55 ); // 55 sekund

$args = array(
	'posts_per_page' => 1,
	'post_type'      => 'bpmj_wp_taxe',
	'meta_key'       => 'taxe_status',
	'meta_value'     => 'pending',
    'orderby'        => 'ID',
    'order'          => 'ASC'
);

$invoices = get_posts( $args );


if ( ! empty( $invoices ) && is_array( $invoices ) ) {

	/** @var WP_Post $invoice */
	foreach ( $invoices as $invoice ) {

		// Utworzenie obiektu
		$invoice_object = new BPMJ_WP_Taxe();
		$invoice_object->set_from_invoice_post( $invoice->ID );

		// Próba wysyłki danych do taxe.pl
		$invoice_object->send_invoice();
	}
}

delete_transient( 'bpmj_wptaxe_doing_cron' );
