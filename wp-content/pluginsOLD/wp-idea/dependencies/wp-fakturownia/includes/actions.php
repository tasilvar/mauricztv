<?php

/*
 * Wszystkie akcje użyte we wtyczce
 */

// Zakoncz, jeżeli plik jest załadowany bezpośrednio
if ( !defined( 'ABSPATH' ) )
	exit;


// Wykonuje Cron Hook
add_action( 'bpmj_wpfa_fakturownia', 'bpmj_wpfa_fakturownia_call_cron' );

function bpmj_wpfa_fakturownia_call_cron() {

	require_once BPMJ_WPFA_DIR . 'includes/cron.php';
}

/*
 * Ukrywa z typu posta "bpmj_wp_fakturownia" przycisk dodaj nowy
 */

function bpmj_wpfa_custom_post_ui() {

	if ( isset( $_GET[ 'post_type' ] ) && $_GET[ 'post_type' ] === 'bpmj_wp_fakturownia' ) {

		echo '<style type="text/css">
    .add-new-h2, .view-switch {display:none;}
    </style>';
	}
}

add_action( 'admin_head', 'bpmj_wpfa_custom_post_ui' );


