<?php
/*
Plugin Name: Easy Digital Downloads - ConvertKit
Plugin URL: https://easydigitaldownloads.com/downloads/convertkit
Description: Subscribe your customers to ConvertKit forms during purchase
Version: 9.0.0-wpidea
Author: EDD Team
Author URI: https://easydigitaldownloads.com
*/


define( 'EDD_CONVERTKIT_PATH', dirname( __FILE__ ) );

if( ! class_exists( 'EDD_Newsletter_V2' ) ) {
	include( EDD_CONVERTKIT_PATH . '/includes/class-edd-newsletter-v2.php' );
}

if( ! class_exists( 'EDD_ConvertKit' ) ) {
	include( EDD_CONVERTKIT_PATH . '/includes/class-edd-convertkit.php' );
}

global $edd_convert_kit;
$edd_convert_kit = new EDD_ConvertKit( 'convertkit', 'ConvertKit' );
