<?php

/*
  Plugin Name: Easy Digital Downloads - Bramka Przelewy24.pl
  Plugin URI: http://upsell.pl/sklep/edd-bramka-platnosci-przelewy24-pl/
  Description: Bramka płatności Przelewy24.pl do Easy Digital Downloads
  Version: 1.0.4
  Author: upSell.pl & Better Profits
  Author URI: http://upsell.pl
 */

if ( !defined( 'BPMJ_UPSELL_STORE_URL' ) ) {
	define( 'BPMJ_UPSELL_STORE_URL', 'http://upsell.pl' );
}
define( 'BPMJ_P24_EDD_NAME', 'EDD Bramka płatności Przelewy24.pl' );

// Definiowanie stałych
define( 'BPMJ_P24_EDD_DIR', dirname( __FILE__ ) ); // Główny katalog wtyczki
define( 'BPMJ_P24_EDD_URL', plugins_url( '/', __FILE__ ) ); // URL do katalogu głównego
define( 'BPMJ_P24_EDD_INC', plugins_url( 'includes', __FILE__ ) ); // URL do katalogu incudes
//
// Licencja / Autoaklualizacja
if ( !defined( 'BPMJ_P24_EDD_VERSION' ) ) {
	define( 'BPMJ_P24_EDD_VERSION', '1.0.4' );
}

//  Dołącza wymagane pliki
if ( !class_exists( 'Przelewy24' ) ) {
	include_once('includes/class_przelewy24.php');
}
include_once('includes/gateways/przelewy24.php');

if ( is_admin() ) {
	include_once('includes/admin/settings.php');
}

/**
 * - Rejestracja domeny dla tłumaczeń
 */
function bpmj_p24_edd_add_textdomain() {
	load_plugin_textdomain( 'bpmj_p24_edd', false, BPMJ_P24_EDD_DIR . '/languages/' );
}

add_action( 'plugins_loaded', 'bpmj_p24_edd_add_textdomain' );

/**
 * - Rejestruje bramkę Przelewy24.pl dla EDD
 * - Zarejestrowaną bramkę można włączyć w EDD -> ustawienia -> Bramki płatności
 */
function bpmj_p24_edd_register_gateway( $gateways ) {
	$gateways[ 'przelewy24_gateway' ] = array(
		'admin_label'	 => 'Bramka Przelewy24.pl',
		'checkout_label' => __( 'przelewy24.pl', 'bpmj_p24_edd' ) );
	return $gateways;
}

add_filter( 'edd_payment_gateways', 'bpmj_p24_edd_register_gateway' );
