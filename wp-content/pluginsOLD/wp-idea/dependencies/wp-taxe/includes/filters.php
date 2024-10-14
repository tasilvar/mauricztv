<?php

/*
 * Wszystkie filtry użyte we wtyczce
 */

// Zakoncz, jeżeli plik jest załadowany bezpośrednio
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


// Dodanie niestandardowych częstotliwości do harmonogramu CRON

add_filter( 'cron_schedules', 'bpmj_wptaxe_custom_intervals' );

function bpmj_wptaxe_custom_intervals( $schedules ) {

	$schedules[ 'bpmj_wptaxe_min' ] = array(
		'interval' => 60,
		'display'  => __( 'Co minutę' )
	);

	return $schedules;
}


/*
 * Dodanie kolumny ze statusem i informacją
 */

add_filter( 'manage_edit-bpmj_wp_taxe_columns', 'bpmj_wptaxe_set_columns' );
add_action( 'manage_bpmj_wp_taxe_posts_custom_column', 'bpmj_wptaxe_custom_column', 10, 2 );

function bpmj_wptaxe_set_columns( $columns ) {
	unset( $columns[ 'author' ] );
	unset( $columns[ 'date' ] );
	$columns[ 'taxe_status' ] = __( 'Status', 'bpmj_wptaxe' );
	$columns[ 'taxe_note' ]   = __( 'Logi', 'bpmj_wptaxe' );
	$columns[ 'taxe_date' ]   = __( 'Data', 'bpmj_wptaxe' );

	return $columns;
}

function bpmj_wptaxe_custom_column( $column, $post_id ) {
	switch ( $column ) {

		case 'taxe_status' :

			$status = get_post_meta( $post_id, 'taxe_status', true );

			echo $status;

			break;

		case 'taxe_note' :

			$note = get_post_meta( $post_id, 'taxe_note', true );

			echo $note;

			break;

		case 'taxe_date' :
			echo get_the_date( 'G:i - d.m.Y', $post_id );
			break;

	}
}

