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

function bpmj_wpfa_load_scripts() {

	$js_dir = BPMJ_WPFA_URL . 'assets/js/';

	wp_enqueue_script( 'bpmj_wpfa_scripts', $js_dir . 'scripts.js', array( 'jquery' ), BPMJ_WPFA_VERSION );
}

add_action( 'wp_enqueue_scripts', 'bpmj_wpfa_load_scripts' );


/*
 * Załącza arkusze styli CSS ( frontend )
 */

function bpmj_wpfa_register_styles() {

	$css_dir = BPMJ_WPFA_URL . 'assets/css/';

	wp_enqueue_style( 'bpmj_wpfa_style', $css_dir . 'style.css', array(), BPMJ_WPFA_VERSION );
}

add_action( 'wp_enqueue_scripts', 'bpmj_wpfa_register_styles' );

/**
 * Załącza skrypty js oraz arkusze styli CSS na zapleczu
 */
function bpmj_wpfa_admin_scripts() {


	$js_dir	 = BPMJ_WPFA_URL . 'assets/js/';
	$css_dir = BPMJ_WPFA_URL . 'assets/css/';


	wp_enqueue_script( 'bpmj_wpfa-admin-scripts', $js_dir . 'admin-scripts.js', array( 'jquery' ), BPMJ_WPFA_VERSION, false );

	wp_enqueue_style( 'bpmj_wpfa-admin-style', $css_dir . 'admin-style.css', BPMJ_WPFA_VERSION );
}

add_action( 'admin_enqueue_scripts', 'bpmj_wpfa_admin_scripts', 100 );
