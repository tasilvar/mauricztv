<?php

/*
  Plugin Name: EDD Interspire
  Plugin URI: http://upsell.pl/sklep/edd-interspire/
  Description: Plugin rozszerzający funkcjonalności EDD o integrację z Interspire Email Marketer
  Author: upSell.pl & Better Profits
  Author URI: http://upsell.pl
  Version: 1.0
 */

//ini_set( 'display_errors', 1 );
//ini_set( 'display_startup_errors', 1 );
//error_reporting( E_ALL );

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) )
	exit;

class BPMJ_EDD_IN {

	private static $instance;

	public static function instance() {
		if ( !isset( self::$instance ) && !( self::$instance instanceof BPMJ_EDD_IN ) ) {
			self::$instance = new BPMJ_EDD_IN;
		}

		return self::$instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->constants();

		// Set up localisation
		$this->load_textdomain();
		$this->includes();

		$this->version = BPMJ_EDD_IN_VERSION;
	}

	/*
	 * Setup plugin constants
	 */
	private function constants() {
		$this->define( 'BPMJ_EDD_IN_VERSION', '1.0' );   // Current version
		$this->define( 'BPMJ_EDD_IN_NAME', 'EDD Interspire' );   // Plugin name
		$this->define( 'BPMJ_EDD_IN_DIR', plugin_dir_path( __FILE__ ) );  // Root plugin path
		$this->define( 'BPMJ_EDD_IN_URL', plugin_dir_url( __FILE__ ) );   // Root plugin URL
		$this->define( 'BPMJ_EDD_IN_FILE', __FILE__ );   // General plugin FILE
		$this->define( 'BPMJ_EDD_IN_DOMAIN', 'bpmj-eddin' );   // Text Domain
		$this->define( 'BPMJ_UPSELL_STORE_URL', 'http://upsell.pl' );
	}

	/*
	 * Define constant if not already set
	 * @param  string $name
	 * @param  string|bool $value
	 */
	private function define( $name, $value ) {
		if ( !defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Załadowanie plików
	 */
	private function includes() {

		if ( !class_exists( 'EDD_Newsletter' ) )
			require_once BPMJ_EDD_IN_DIR . 'includes/class-edd-newsletter.php';

		if ( !class_exists( 'EDD_Interspire_API' ) )
			require_once BPMJ_EDD_IN_DIR . 'includes/class-interspire-api.php';

		require_once BPMJ_EDD_IN_DIR . 'includes/class-interspire.php';
	}

	/**
	 * Register text domain
	 */
	private function load_textdomain() {
		$lang_dir = dirname( plugin_basename( BPMJ_EDD_IN_FILE ) ) . '/languages/';
		load_plugin_textdomain( BPMJ_EDD_IN_DOMAIN, false, $lang_dir );
	}

}

function BPMJ_EDD_IN() {
	return BPMJ_EDD_IN::instance();
}
BPMJ_EDD_IN();