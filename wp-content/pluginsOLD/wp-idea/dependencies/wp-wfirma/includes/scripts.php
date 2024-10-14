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

function bpmj_wpwf_load_scripts() {

	$js_dir = BPMJ_WPWF_URL . 'assets/js/';

	wp_enqueue_script( 'bpmj_wpwf_scripts', $js_dir . 'scripts.js', array( 'jquery' ), BPMJ_WPWF_VERSION );
}

add_action( 'wp_enqueue_scripts', 'bpmj_wpwf_load_scripts' );


/*
 * Załącza arkusze styli CSS ( frontend )
 */

function bpmj_wpwf_register_styles() {

	$css_dir = BPMJ_WPWF_URL . 'assets/css/';

	wp_enqueue_style( 'bpmj_wpwf_style', $css_dir . 'style.css', array(), BPMJ_WPWF_VERSION );
}

add_action( 'wp_enqueue_scripts', 'bpmj_wpwf_register_styles' );

/**
 * Załącza skrypty js oraz arkusze styli CSS na zapleczu
 */
function bpmj_wpwf_admin_scripts() {


	$js_dir	 = BPMJ_WPWF_URL . 'assets/js/';
	$css_dir = BPMJ_WPWF_URL . 'assets/css/';


	wp_enqueue_script( 'bpmj_wpwf-admin-scripts', $js_dir . 'admin-scripts.js', array( 'jquery' ), BPMJ_WPWF_VERSION, false );

	wp_enqueue_style( 'bpmj_wpwf-admin-style', $css_dir . 'admin-style.css', BPMJ_WPWF_VERSION );
}

add_action( 'admin_enqueue_scripts', 'bpmj_wpwf_admin_scripts', 100 );
