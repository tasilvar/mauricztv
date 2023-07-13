<?php

/*
 * Wszystkie filtry użyte we wtyczce
 */

// Zakoncz, jeżeli plik jest załadowany bezpośrednio
if ( !defined( 'ABSPATH' ) )
	exit;


// Dodanie niestandardowych częstotliwości do harmonogramu CRON

add_filter( 'cron_schedules', 'bpmj_wpwf_custom_intervals' );

function bpmj_wpwf_custom_intervals( $schedules ) {

	$schedules[ 'bpmj_wpwf_min' ] = array(
		'interval'	 => 60,
		'display'	 => __( 'Co minutę' )
	);

	return $schedules;
}

/*
 * Dodanie kolumny ze statusem i informacją
 */

add_filter( 'manage_edit-bpmj_wp_wfirma_columns', 'bpmj_wpwf_set_columns' );
add_action( 'manage_bpmj_wp_wfirma_posts_custom_column', 'bpmj_wpwf_custom_column', 10, 2 );

function bpmj_wpwf_set_columns( $columns ) {
	unset( $columns[ 'author' ] );
	unset( $columns[ 'date' ] );
	$columns[ 'wfirma_status' ]	 = __( 'Status', 'bpmj_wpwf' );
	$columns[ 'wfirma_note' ]		 = __( 'Logi', 'bpmj_wpwf' );
	$columns[ 'wfirma_date' ]		 = __( 'Data', 'bpmj_wpwf' );

	return $columns;
}

function bpmj_wpwf_custom_column( $column, $post_id ) {
	switch ( $column ) {

		case 'wfirma_status' :

			$status = get_post_meta( $post_id, 'wfirma_status', true );

			echo $status;

			break;

		case 'wfirma_note' :

			$note = get_post_meta( $post_id, 'wfirma_note', true );

			echo $note;

			break;

		case 'wfirma_date' :
			echo get_the_date( 'G:i - d.m.Y', $post_id );
			break;
	}
}
