<?php

/**
 * Funkcje tworzące Custom Post Types
 */
// Zakoncz, jeżeli plik jest załadowany bezpośrednio
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Rejestracja Custom Post Types
 */
function bpmj_wptaxe_posts_type() {


	$args = array(
		'labels'       => array(
			'name' => _x( 'WP Taxe', 'bpmj_wptaxe' )
		),
		'public'       => false,
		'show_ui'      => false,
		'hierarchical' => false,
		'has_archive'  => false,
		'supports'     => apply_filters( 'bpmj_wptaxe_download_supports', array( 'title' ) ),
		'capabilities' => array(
			'edit_post'          => 'manage_options',
			'read_post'          => 'manage_options',
			'delete_post'        => 'manage_options',
			'edit_posts'         => 'manage_options',
			'edit_others_posts'  => 'manage_options',
			'delete_posts'       => 'manage_options',
			'publish_posts'      => 'manage_options',
			'read_private_posts' => 'manage_options'
		),
	);

	register_post_type( 'bpmj_wp_taxe', apply_filters( 'bpmj_wptaxe_post_type_args', $args ) );
}

add_action( 'init', 'bpmj_wptaxe_posts_type', 1 );

