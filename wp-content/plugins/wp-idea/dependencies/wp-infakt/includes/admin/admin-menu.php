<?php

/*
 * Rejestracja menu administratora
 */

// Zakoncz, jeżeli plik jest załadowany bezpośrednio
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/*
 * Tworzy menu
 */

function bpmj_wpinfakt_add_options_link() {

	add_submenu_page( 'edit.php?post_type=bpmj_wp_infakt', __( 'Ustawienia', 'bpmj_wpinfakt' ), __( 'Ustawienia', 'bpmj_wpinfakt' ), 'manage_options', 'bpmj_wpinfakt_options', 'bpmj_wpinfakt_options_page' );
}

add_action( 'admin_menu', 'bpmj_wpinfakt_add_options_link', 10 );
