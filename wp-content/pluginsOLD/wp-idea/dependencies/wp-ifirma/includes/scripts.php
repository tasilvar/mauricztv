<?php
/**
 * Ładuje skrypty potrzebne do działania wtyczki ( front-end oraz back-end )
 */

// Zakoncz, jeżeli plik jest załadowany bezpośrednio
if ( !defined( 'ABSPATH' ) ) exit;


/*
 * Załącza skrypty js ( frontend )
 */
function bpmj_wpifirma_load_scripts() {

	$js_dir = BPMJ_WPIFIRMA_URL . 'assets/js/';

	wp_enqueue_script( 'bpmj_wpifirma_scripts', $js_dir . 'scripts.js', array( 'jquery' ), BPMJ_WPIFIRMA_VERSION );

}
add_action( 'wp_enqueue_scripts', 'bpmj_wpifirma_load_scripts' );


/*
 * Załącza arkusze styli CSS ( frontend )
 */
function bpmj_wpifirma_register_styles() {
        
        $css_dir = BPMJ_WPIFIRMA_URL . 'assets/css/';

	wp_enqueue_style( 'bpmj_wpifirma_style', $css_dir . 'style.css', array(), BPMJ_WPIFIRMA_VERSION );
}
add_action( 'wp_enqueue_scripts', 'bpmj_wpifirma_register_styles' );


/**
 * Załącza skrypty js oraz arkusze styli CSS na zapleczu
 */
function bpmj_wpifirma_admin_scripts() {


	$js_dir  = BPMJ_WPIFIRMA_URL . 'assets/js/';
	$css_dir = BPMJ_WPIFIRMA_URL . 'assets/css/';

        
	wp_enqueue_script( 'bpmj_wpifirma-admin-scripts', $js_dir . 'admin-scripts.js', array( 'jquery' ), BPMJ_WPIFIRMA_VERSION, false );
	
	wp_enqueue_style( 'bpmj_wpifirma-admin-style', $css_dir . 'admin-style.css', BPMJ_WPIFIRMA_VERSION );
        

        
}
add_action( 'admin_enqueue_scripts', 'bpmj_wpifirma_admin_scripts', 100 );
