<?php

/**
 * Ładuje skrypty potrzebne do działania wtyczki ( front-end oraz back-end )
 */
// Zakoncz, jeżeli plik jest załadowany bezpośrednio
if ( !defined( 'ABSPATH' ) )
	exit;


/*
 * Załącza skrypty js ( frontend )
 */

function bpmj_edd_invoice_data_load_scripts() {

	wp_enqueue_script( 'bpmj_edd_invoice_data_scripts', BPMJ_EDD_ID_PLUGINS_URL . 'assets/js/scripts.js', array( 'jquery' ), BPMJ_EDD_ID_VERSION );
}

add_action( 'wp_enqueue_scripts', 'bpmj_edd_invoice_data_load_scripts' );


/*
 * Załącza arkusze styli CSS ( frontend )
 */

function bpmj_edd_invoice_data_load_styles() {

	wp_register_style( 'bpmj_edd_invoice_data_form', BPMJ_EDD_ID_PLUGINS_URL . 'assets/css/style.css' );
	wp_enqueue_style( 'bpmj_edd_invoice_data_form' );

	$hide_fname = bpmj_edd_invoice_data_get_cb_setting( 'edd_id_hide_fname' );
	if ( $hide_fname ) {
		wp_register_style( 'bpmj_edd_invoice_data_hide_fname', BPMJ_EDD_ID_PLUGINS_URL . 'assets/css/hide_fname.css' );
		wp_enqueue_style( 'bpmj_edd_invoice_data_hide_fname' );
	}

	$hide_lname = bpmj_edd_invoice_data_get_cb_setting( 'edd_id_hide_lname' );
	if ( $hide_lname ) {
		wp_register_style( 'bpmj_edd_invoice_data_hide_lname', BPMJ_EDD_ID_PLUGINS_URL . 'assets/css/hide_lname.css' );
		wp_enqueue_style( 'bpmj_edd_invoice_data_hide_lname' );
	}
}

add_action( 'wp_enqueue_scripts', 'bpmj_edd_invoice_data_load_styles' );
