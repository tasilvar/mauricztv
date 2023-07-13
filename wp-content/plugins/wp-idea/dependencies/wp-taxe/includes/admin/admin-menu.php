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

function bpmj_wptaxe_add_options_link() {

	add_submenu_page( 'edit.php?post_type=bpmj_wp_taxe', __( 'Ustawienia', 'bpmj_wptaxe' ), __( 'Ustawienia', 'bpmj_wptaxe' ), 'manage_options', 'bpmj_wptaxe_options', 'bpmj_wptaxe_options_page' );
}

add_action( 'admin_menu', 'bpmj_wptaxe_add_options_link', 10 );
