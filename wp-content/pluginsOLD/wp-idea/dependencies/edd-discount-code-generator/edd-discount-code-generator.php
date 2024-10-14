<?php
/*
Plugin Name: Easy Digital Downloads - Discount Code Generator
Plugin URL: http://easydigitaldownloads.com/extension/coupon-generator
Description: Create discount codes in bulk.
Version: 1.1
Author: polevaultweb
Author URI: http://polevaultweb.com

UWAGA Plugin zmodyfikowany, nie wolno aktualizować!
*/


if( !class_exists( 'eddDev7DiscountCodeGenerator' ) ){

	class eddDev7DiscountCodeGenerator {

	    private $plugin_name = 'Discount Code Generator';
	    private $plugin_version;
	    private $plugin_author = 'polevaultweb';

	    function __construct() {

	    	$this->plugin_version = '1.1';

	    	if ( ! defined( 'EDD_DCG_PLUGIN_DIR' ) ) {
				define( 'EDD_DCG_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}

			if ( ! defined( 'EDD_DCG_PLUGIN_URL' ) ) {
				define( 'EDD_DCG_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			}

			if ( ! defined( 'EDD_DCG_PLUGIN_FILE' ) ) {
				define( 'EDD_DCG_PLUGIN_FILE', __FILE__ );
			}

	        load_plugin_textdomain( 'edd_dcg', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );

	        add_filter('edd_load_scripts_for_these_pages', array($this, 'edd_load_scripts_for_these_pages'));
	        add_filter('edd_load_scripts_for_discounts', array($this, 'edd_load_scripts_for_these_pages'));

			if( is_admin() ) {

				include_once( EDD_DCG_PLUGIN_DIR .'/includes/admin-page.php' );
				include_once( EDD_DCG_PLUGIN_DIR .'/includes/discount-actions.php' );
			}

	    }

	    function edd_load_scripts_for_these_pages($pages) {
	    	$pages[] = 'download_page_edd-dc-generator';
			return $pages;
	    }
	}
	$eddDev7DiscountCodeGenerator = new eddDev7DiscountCodeGenerator();
}
