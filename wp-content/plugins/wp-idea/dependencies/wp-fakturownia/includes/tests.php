<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Usage: https://your.wordpress.com/?bpmj_test=<function_name>
 */
class BPMJ_WPFA_Tests {
	/**
	 * @var BPMJ_WPFA_Tests
	 */
	private static $instance;

	/**
	 * @return BPMJ_WPFA_Tests
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new static();
		}

		return self::$instance;
	}

	/**
	 *
	 */
	public function bpmj_wpfa_test_send_invoice() {
		header( 'Content-type: text/plain; charset=UTF-8' );
		$invoice = get_post( 3815 );

		$fakturownia = new BPMJ_WP_Fakturownia();
		$fakturownia->set_from_invoice_post( $invoice->ID );
		$fakturownia->send_invoice();
	}
}

