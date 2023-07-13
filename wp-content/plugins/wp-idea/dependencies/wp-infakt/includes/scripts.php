<?php
/**
 * Ładuje skrypty potrzebne do działania wtyczki ( front-end oraz back-end )
 */

// Zakoncz, jeżeli plik jest załadowany bezpośrednio
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Załącza skrypty js oraz arkusze styli CSS na zapleczu
 */
function bpmj_wpinfakt_admin_scripts() {
	$css_dir = BPMJ_WPINFAKT_URL . 'assets/css/';

	wp_enqueue_style( 'bpmj_wpinfakt-admin-style', $css_dir . 'admin-style.css', BPMJ_WPINFAKT_VERSION );
}

add_action( 'admin_enqueue_scripts', 'bpmj_wpinfakt_admin_scripts', 100 );
