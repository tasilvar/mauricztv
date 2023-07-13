<?php

/*
  Plugin Name: EDD SALESmanago
  Plugin URI: http://upsell.pl/sklep/edd-salesmanago/
  Description: Plugin rozszerzający funkcjonalności EDD o integrację z SALESmanago
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

class BPMJ_EDD_SM {

	private static $instance;

	public static function instance() {
		if ( !isset( self::$instance ) && !( self::$instance instanceof BPMJ_EDD_SM ) ) {
			self::$instance = new BPMJ_EDD_SM;
		}

		add_action( 'admin_enqueue_scripts', array( self::$instance, 'admin_scripts_styles' ) );
		add_action( 'wp_enqueue_scripts', array( self::$instance, 'salesmanago_scripts' ) );

		return self::$instance;
	}

	/**
	 * WP Seller Constructor.
	 */
	public function __construct() {
		$this->constants();

		// Set up localisation
		$this->load_textdomain();
		$this->includes();

		$this->version = BPMJ_EDD_SM_VERSION;
	}

	/*
	 * Setup plugin constants
	 */

	private function constants() {

		$this->define( 'BPMJ_EDD_SM_VERSION', '1.0' );   // Current version
		$this->define( 'BPMJ_EDD_SM_NAME', 'EDD SALESmanago' );   // Plugin name
		$this->define( 'BPMJ_EDD_SM_DIR', plugin_dir_path( __FILE__ ) );  // Root plugin path
		$this->define( 'BPMJ_EDD_SM_URL', plugin_dir_url( __FILE__ ) );   // Root plugin URL
		$this->define( 'BPMJ_EDD_SM_FILE', __FILE__ );   // General plugin FILE
		$this->define( 'BPMJ_EDD_SM_DOMAIN', 'bpmj-eddsm' );   // Text Domain
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
			require_once BPMJ_EDD_SM_DIR . 'includes/class-edd-newsletter.php';
		}

		require_once BPMJ_EDD_SM_DIR . 'includes/class-salesmanago.php';

		if ( is_admin() ) {
			require_once BPMJ_EDD_SM_DIR . 'includes/settings.php';
		}
	}

	/*
	 * Ładowanie i uruchomienie styli i skryptów dla admina
	 */

	public function admin_scripts_styles() {

		// Scripts
		wp_register_script( 'bpmj_edd_sm_select2', BPMJ_EDD_SM_URL . 'assets/js/jquery.tagsinput.min.js', array( 'jquery' ) );
		wp_register_script( 'bpmj_edd_sm_admin_script', BPMJ_EDD_SM_URL . 'assets/js/scripts.js', array( 'jquery' ) );

		wp_enqueue_script( 'bpmj_edd_sm_select2' );
		wp_enqueue_script( 'bpmj_edd_sm_admin_script' );

		// Styles
		wp_register_style( 'bpmj_edd_sm_select2', BPMJ_EDD_SM_URL . 'assets/css/jquery.tagsinput.min.css' );
		wp_enqueue_style( 'bpmj_edd_sm_select2' );
	}


	/**
	 * Ładowanie skryptów dla usera
	 */
	public function salesmanago_scripts() {
		
		global $edd_options;

		// SALESmanago Tracking Code
		$tracking_code = (isset( $edd_options[ 'salesmanago_tracking_code' ] ) && $edd_options[ 'salesmanago_tracking_code' ] == '1');
		$client_id = isset( $edd_options[ 'salesmanago_client_id' ] ) ? $edd_options[ 'salesmanago_client_id' ] : false;
		if ( $tracking_code && $client_id ) {
			
			wp_enqueue_script( 'bpmj_edd_sm_monitor',  BPMJ_EDD_SM_URL . 'assets/js/sm-monitor.js', array(), false, true );		
				
			wp_localize_script( 'bpmj_edd_sm_monitor', '_smid', $client_id );

			if ( isset( $_COOKIE['smclient'] ) )
				wp_localize_script( 'bpmj_edd_sm_monitor', '_smclientid', strip_tags( $_COOKIE['smclient'] ) );
		}
	}


	/**
	 * Register text domain
	 */
	private function load_textdomain() {
		$lang_dir = dirname( plugin_basename( BPMJ_EDD_SM_FILE ) ) . '/languages/';
		load_plugin_textdomain( BPMJ_EDD_SM_DOMAIN, false, $lang_dir );
	}

}

function BPMJ_EDD_SM() {
	return BPMJ_EDD_SM::instance();
}

// Get EDD Running
BPMJ_EDD_SM();
