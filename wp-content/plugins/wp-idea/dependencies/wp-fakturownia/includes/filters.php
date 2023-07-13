<?php

/*
 * Wszystkie filtry użyte we wtyczce
 */

// Zakoncz, jeżeli plik jest załadowany bezpośrednio
if ( !defined( 'ABSPATH' ) )
	exit;


// Dodanie niestandardowych częstotliwości do harmonogramu CRON

add_filter( 'cron_schedules', 'bpmj_wpfa_custom_intervals' );

function bpmj_wpfa_custom_intervals( $schedules ) {

	$schedules[ 'bpmj_wpfa_min' ] = array(
		'interval'	 => 60,
		'display'	 => __( 'Co minutę' )
	);

	return $schedules;
}

/*
 * Dodanie kolumny ze statusem i informacją
 */

add_filter( 'manage_edit-bpmj_wp_fakturownia_columns', 'bpmj_wpfa_set_columns' );
add_action( 'manage_bpmj_wp_fakturownia_posts_custom_column', 'bpmj_wpfa_custom_column', 10, 2 );

function bpmj_wpfa_set_columns( $columns ) {
	unset( $columns[ 'author' ] );
	unset( $columns[ 'date' ] );
	$columns[ 'fakturownia_status' ]	 = __( 'Status', 'bpmj_wpfa' );
	$columns[ 'fakturownia_note' ]	 = __( 'Logi', 'bpmj_wpfa' );
	$columns[ 'fakturownia_date' ]	 = __( 'Data', 'bpmj_wpfa' );

	return $columns;
}

function bpmj_wpfa_custom_column( $column, $post_id ) {
	switch ( $column ) {

		case 'fakturownia_status' :

			$status = get_post_meta( $post_id, 'fakturownia_status', true );

			echo $status;

			break;

		case 'fakturownia_note' :

			$note = get_post_meta( $post_id, 'fakturownia_note', true );

			echo $note;

			break;

		case 'fakturownia_date' :
			echo get_the_date( 'G:i - d.m.Y', $post_id );
			break;
	}
}
