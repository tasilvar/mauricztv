<?php

/*
 * Wszystkie filtry użyte we wtyczce
 */

// Zakoncz, jeżeli plik jest załadowany bezpośrednio
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


// Dodanie niestandardowych częstotliwości do harmonogramu CRON

add_filter( 'cron_schedules', 'bpmj_wpinfakt_custom_intervals' );

function bpmj_wpinfakt_custom_intervals( $schedules ) {

	$schedules[ 'bpmj_wpinfakt_min' ] = array(
		'interval' => 60,
		'display'  => __( 'Co minutę' )
	);

	return $schedules;
}


/*
 * Dodanie kolumny ze statusem i informacją
 */

add_filter( 'manage_edit-bpmj_wp_infakt_columns', 'bpmj_wpinfakt_set_columns' );
add_action( 'manage_bpmj_wp_infakt_posts_custom_column', 'bpmj_wpinfakt_custom_column', 10, 2 );

function bpmj_wpinfakt_set_columns( $columns ) {
	unset( $columns[ 'author' ] );
	unset( $columns[ 'date' ] );
	$columns[ 'infakt_status' ] = __( 'Status', 'bpmj_wpinfakt' );
	$columns[ 'infakt_note' ]   = __( 'Logi', 'bpmj_wpinfakt' );
	$columns[ 'infakt_date' ]   = __( 'Data', 'bpmj_wpinfakt' );

	return $columns;
}

function bpmj_wpinfakt_custom_column( $column, $post_id ) {
	switch ( $column ) {

		case 'infakt_status' :

			$status = get_post_meta( $post_id, 'infakt_status', true );

			echo $status;

			break;

		case 'infakt_note' :

			$note = get_post_meta( $post_id, 'infakt_note', true );

			echo $note;

			break;

		case 'infakt_date' :
			echo get_the_date( 'G:i - d.m.Y', $post_id );
			break;

	}
}

