<?php

/*
  Plugin Name: Easy Digital Downloads - Bramka Dotpay
  Plugin URI: http://upsell.pl/sklep/bramka-dotpay-do-easy-digital-downloads/
  Description: Bramka płatności Dotpay do Easy Digital Downloads
  Version: 1.2.3
  Author: upSell.pl & Better Profits
  Author URI: http://upsell.pl
 */

if ( !defined( 'BPMJ_UPSELL_STORE_URL' ) ) {
	define( 'BPMJ_UPSELL_STORE_URL', 'https://upsell.pl' );
}
define( 'BPMJ_DOT_EDD_NAME', 'EDD Bramka płatności Dotpay' );

// Definiowanie stałych
define( 'BPMJ_DOT_EDD_DIR', dirname( __FILE__ ) ); // Główny katalog wtyczki __FILE__);
define( 'BPMJ_DOT_EDD_URL', plugins_url( '/', __FILE__ ) ); // URL do katalogu głównego
define( 'BPMJ_DOT_EDD_INC', plugins_url( 'includes', __FILE__ ) ); // URL do katalogu incudes
// Licencja / Autoaklualizacja
if ( !defined( 'BPMJ_DOT_EDD_VERSION' ) ) {
	define( 'BPMJ_DOT_EDD_VERSION', '1.2.3' );
}


if ( !class_exists( 'Dotpay' ) ) {
    include_once('includes/class_dotpay.php');
}

// Wczytanie niezbędnych plików
include_once('includes/gateways/dotpay.php'); // Plik z obsługą bramki Dotpay


if ( is_admin() ) {
	include_once('includes/admin/settings.php');
}

/**
 * - Rejestracja domeny dla tłumaczeń
 */
function bpmj_dot_edd_add_textdomain() {
	load_plugin_textdomain( 'bpmj_dot_edd', false, BPMJ_DOT_EDD_DIR . '/languages/' );
}

add_action( 'plugins_loaded', 'bpmj_dot_edd_add_textdomain' );

/**
 * - Rejestruje bramkę Dotpay dla EDD
 * - Zarejestrowaną bramkę można włączyć w EDD -> ustawienia -> Bramki płatnośc
 */
function bpmj_dot_edd_register_gateway( $gateways ) {
	$gateways[ 'dotpay_gateway' ] = array(
		'admin_label'	 => 'Bramka Dotpay',
		'checkout_label' => __( 'Dotpay', 'bpmj_dot_edd' ) );
	return $gateways;
}

add_filter( 'edd_payment_gateways', 'bpmj_dot_edd_register_gateway' );
