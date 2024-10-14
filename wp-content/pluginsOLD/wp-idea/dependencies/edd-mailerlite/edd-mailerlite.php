<?php

/*
  Plugin Name: EDD MailerLite
  Plugin URI: http://upsell.pl/sklep/edd-mailerlite/
  Description: Plugin rozszerzający funkcjonalności EDD o integrację z MailerLite
  Author: upSell.pl & Better Profits
  Author URI: http://upsell.pl
  Version: 1.0.2
 */

//ini_set( 'display_errors', 1 );
//ini_set( 'display_startup_errors', 1 );
//error_reporting( E_ALL );
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) )
	exit;

class BPMJ_EDD_ML {

	private static $instance;

	public static function instance() {
		if ( !isset( self::$instance ) && !( self::$instance instanceof BPMJ_EDD_ML ) ) {
			self::$instance = new BPMJ_EDD_ML;
		}

		return self::$instance;
	}

	/**
	 * EDD MailerLite Constructor.
	 */
	public function __construct() {
		$this->constants();

		// Set up localisation
		$this->load_textdomain();
		$this->includes();

		$this->version = BPMJ_EDD_ML_VERSION;
	}

	/*
	 * Setup plugin constants
	 */

	private function constants() {

		$this->define( 'BPMJ_EDD_ML_VERSION', '1.0.2' );   // Current version
		$this->define( 'BPMJ_EDD_ML_NAME', 'EDD MailerLite' );   // Plugin name
		$this->define( 'BPMJ_EDD_ML_DIR', plugin_dir_path( __FILE__ ) );  // Root plugin path
		$this->define( 'BPMJ_EDD_ML_URL', plugin_dir_url( __FILE__ ) );   // Root plugin URL
		$this->define( 'BPMJ_EDD_ML_FILE', __FILE__ );   // General plugin FILE
		$this->define( 'BPMJ_EDD_ML_DOMAIN', 'bpmj-eddml' );   // Text Domain
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

	/*
	 * Załadowanie plików
	 */

	private function includes() {

		if ( ! class_exists( 'EDD_Newsletter' ) ) {
			require_once BPMJ_EDD_ML_DIR . 'includes/class-edd-newsletter.php';
		}
		
		if ( ! class_exists( 'EDD_MailerLite_API' ) ) {
			require_once BPMJ_EDD_ML_DIR . 'includes/class-mailerlite-api.php';
		}

		require_once BPMJ_EDD_ML_DIR . 'includes/class-mailerlite.php';

		if ( is_admin() ) {
			//require_once BPMJ_EDD_ML_DIR . 'includes/settings.php';
		}
	}


	/**
	 * Register text domain
	 */
	private function load_textdomain() {
		$lang_dir = dirname( plugin_basename( BPMJ_EDD_ML_FILE ) ) . '/languages/';
		load_plugin_textdomain( BPMJ_EDD_ML_DOMAIN, false, $lang_dir );
	}

}

function BPMJ_EDD_ML() {
	return BPMJ_EDD_ML::instance();
}

// Get EDD Running
BPMJ_EDD_ML();
