<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

//  Dołącza wymagane pliki
include_once( BPMJ_TRA_EDD_DIR . '/includes/gateways/tpay.php' );
include_once( BPMJ_TRA_EDD_DIR . '/includes/filters.php' );
include_once( BPMJ_TRA_EDD_DIR . '/includes/functions.php' );
include_once( BPMJ_TRA_EDD_DIR . '/includes/metabox.php' );
include_once( BPMJ_TRA_EDD_DIR . '/includes/service/class-edd-extensions.php' );

if ( is_admin() ) {
	include_once( BPMJ_TRA_EDD_DIR . '/includes/admin/settings.php' );
}

/**
 * - Rejestracja domeny dla tłumaczeń
 */
function bpmjd_tra_edd_add_textdomain() {
	$lang_dir = dirname( plugin_basename( BPMJ_TRA_EDD_FILE ) ) . '/languages/';
	load_plugin_textdomain( 'edd-tpay', false, $lang_dir );
}

add_action( 'plugins_loaded', 'bpmjd_tra_edd_add_textdomain' );

/**
 * - Rejestruje bramkę tpay.com dla EDD
 * - Zarejestrowaną bramkę można włączyć w EDD -> ustawienia -> Bramki płatności
 *
 * @param array $gateways
 *
 * @return array
 */
function bpmjd_tra_edd_register_gateway( $gateways ) {
	global $edd_options;

	$gateways[ BPMJ_TRA_EDD_GATEWAY_ID ] = array(
		'admin_label'    => __( 'Bramka tpay.com', 'edd-tpay' ),
		'checkout_label' => __( 'tpay.com', 'edd-tpay' ),
	);

	if ( ! empty( $edd_options[ 'tpay_cards_api_key' ] ) && ! empty( $edd_options[ 'tpay_cards_api_password' ] ) ) {
		$gateways[ BPMJ_TRA_EDD_GATEWAY_ID ][ 'supports' ] = array( 'recurring_payments' );
	}

	return $gateways;
}

add_filter( 'edd_payment_gateways', 'bpmjd_tra_edd_register_gateway' );

function bpmjd_tra_auto_upgrade( $mark_only = false ) {
	require_once BPMJ_TRA_EDD_DIR . '/includes/admin/class-upgrades.php';
	if ( $mark_only ) {
		\bpmj\wp\eddtpay\admin\Upgrades::instance()->auto_upgrade( true );
	} else {
		add_action( 'admin_init', array( \bpmj\wp\eddtpay\admin\Upgrades::instance(), 'auto_upgrade' ), 100 );
	}
}

bpmjd_tra_auto_upgrade();