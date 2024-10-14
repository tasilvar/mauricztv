<?php

/**
 * Funkcje tworzące Custom Post Types
 */
// Zakoncz, jeżeli plik jest załadowany bezpośrednio
if ( !defined( 'ABSPATH' ) )
	exit;

/**
 * Rejestracja Custom Post Types
 */
function bpmj_wpwf_posts_type() {


	$args = array(
		'labels'		 => array(
			'name' => _x( 'WP wFirma', 'bpmj_wpwf' )
		),
		'public'		 => false,
		'show_ui'		 => false,
		'hierarchical'	 => false,
		'has_archive'	 => false,
		'supports'		 => apply_filters( 'bpmj_wpwf_download_supports', array( 'title' ) ),
	);

	register_post_type( 'bpmj_wp_wfirma', apply_filters( 'bpmj_wpwf_post_type_args', $args ) );
}

add_action( 'init', 'bpmj_wpwf_posts_type', 1 );

