<?php

/*
 * Wszystkie akcje użyte we wtyczce
 */

// Zakoncz, jeżeli plik jest załadowany bezpośrednio
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


// Wykonuje Cron Hook
add_action( 'bpmj_wptaxe_cron', 'bpmj_wptaxe_call_cron' );

function bpmj_wptaxe_call_cron() {

	require_once BPMJ_WPTAXE_DIR . 'includes/cron.php';
}

/*
 * Ukrywa z typu posta "bpmj_wp_taxe" przycisk dodaj nowy
 */

function bpmj_wptaxe_custom_post_ui() {

	if ( isset( $_GET[ 'post_type' ] ) && $_GET[ 'post_type' ] === 'bpmj_wp_taxe' ) {

		echo '<style type="text/css">
    .add-new-h2, .view-switch {display:none;}
    </style>';
	}
}

add_action( 'admin_head', 'bpmj_wptaxe_custom_post_ui' );