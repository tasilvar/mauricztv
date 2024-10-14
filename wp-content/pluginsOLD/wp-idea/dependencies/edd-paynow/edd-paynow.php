<?php

/*
  Plugin Name: Easy Digital Downloads - Bramka Paynow
  Plugin URI:
  Description: Bramka płatności Paynow do Easy Digital Downloads
  Version: 1.0.0
  Author: upSell.pl & Better Profits
  Author URI: http://upsell.pl
 */

define( 'BPMJ_PAYNOW_EDD_DIR', dirname( __FILE__ ) );

function bpmj_paynow_edd_add_textdomain() {
    load_plugin_textdomain( 'bpmj_paynow_edd', false, BPMJ_PAYNOW_EDD_DIR . '/languages/' );
}

add_action( 'plugins_loaded', 'bpmj_paynow_edd_add_textdomain' );

include_once('includes/paynow.php');


if ( !class_exists( 'Paynow' ) ) {
    include_once('includes/class_paynow.php');
}

if ( is_admin() ) {
    include_once('includes/admin/settings.php');
}

function bpmj_paynow_edd_register_gateway( $gateways ) {
    $gateways[ 'paynow_gateway' ] = array(
        'admin_label'	 => 'Bramka Paynow',
        'checkout_label' => __( 'Paynow', 'bpmj_paynow_edd' ) );
    return $gateways;
}

add_filter( 'edd_payment_gateways', 'bpmj_paynow_edd_register_gateway' );
