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

function bpmj_wpwf_add_options_link() {

	add_submenu_page( 'edit.php?post_type=bpmj_wp_wfirma', __( 'Ustawienia', 'bpmj_wpwf' ), __( 'Ustawienia', 'bpmj_wpwf' ), 'manage_options', 'bpmj_wpwf_options', 'bpmj_wpwf_options_page' );
}

add_action( 'admin_menu', 'bpmj_wpwf_add_options_link', 10 );
