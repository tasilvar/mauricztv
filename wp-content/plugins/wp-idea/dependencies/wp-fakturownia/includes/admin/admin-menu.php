<?php

/*
 * Rejestracja menu administratora
 */

// Zakoncz, jeżeli plik jest załadowany bezpośrednio
if ( !defined( 'ABSPATH' ) )
	exit;


/*
 * Tworzy menu
 */

function bpmj_wpfa_add_options_link() {

	add_submenu_page( 'edit.php?post_type=bpmj_wp_fakturownia', __( 'Ustawienia', 'bpmj_wpfa' ), __( 'Ustawienia', 'bpmj_wpfa' ), 'manage_options', 'bpmj_wpfa_options', 'bpmj_wpfa_options_page' );
}

add_action( 'admin_menu', 'bpmj_wpfa_add_options_link', 10 );
