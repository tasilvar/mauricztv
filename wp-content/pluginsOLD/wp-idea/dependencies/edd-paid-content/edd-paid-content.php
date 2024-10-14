<?php

/*
  Plugin Name: EDD Paid Content
  Plugin URI: http://upsell.pl/sklep/edd-paid-content/
  Description: Wtyczka umożliwia sprzedaż dostępu do ukrytej treści
  Version: 1.6.5
  Author: upSell.pl & Better Profits
  Author URI: http://upsell.pl
 */

if ( ! defined( 'BPMJ_UPSELL_STORE_URL' ) ) {
	define( 'BPMJ_UPSELL_STORE_URL', 'http://upsell.pl' );
}
define( 'BPMJ_EDD_PC_NAME', 'EDD Paid Content' );

// Definiowanie potrzebnych stałych do ścieżek we wtyczce.
define( 'BPMJ_EDD_PC_PATH', dirname( __FILE__ ) );
define( 'BPMJ_EDD_PC_FILE', __FILE__ );
define( 'BPMJ_EDD_PC_INCLUDES', dirname( __FILE__ ) . '/includes' );
define( 'BPMJ_EDD_PC_FOLDER', basename( BPMJ_EDD_PC_PATH ) );
define( 'BPMJ_EDD_PC_URL', plugin_dir_url( __FILE__ ) );

// Licencja / Autoaklualizacja
if ( ! defined( 'BPMJ_EDD_PC_VERSION' ) ) {
	define( 'BPMJ_EDD_PC_VERSION', '1.6.5' );
}

global $bpmj_eddpc_tnow;
$bpmj_eddpc_tnow = time();

define( 'BPMJ_EDD_PC_DEBUG_MODE', false );

class BPMJ_EDD_PC_Plugin_Base {

	// Wywołanie potrzebnych akcji w konstruktorze
	function __construct() {

		$this->load_textdomain();

		// Wczytuje niezbędne funckje
		include_once( BPMJ_EDD_PC_INCLUDES . '/functions.php' );

		// Wczytuje pliki z filtami i akcjami
		include_once( BPMJ_EDD_PC_INCLUDES . '/filters.php' );
		include_once( BPMJ_EDD_PC_INCLUDES . '/actions.php' );

		// Ładuje shordcody
		include_once( BPMJ_EDD_PC_INCLUDES . '/shortcode.php' );

		// Dodaje skrypty
		include_once( BPMJ_EDD_PC_INCLUDES . '/scripts.php' );

		// Biblioteki
		if ( ! function_exists( 'xxtea_encrypt' ) ) {
			include_once( BPMJ_EDD_PC_INCLUDES . '/lib/xxtea.php' );
		}

		// metabox
		if ( is_admin() ) {
			include_once( BPMJ_EDD_PC_INCLUDES . '/ajax-functions.php' );
			include_once( BPMJ_EDD_PC_INCLUDES . '/metabox.php' );
			include_once( BPMJ_EDD_PC_INCLUDES . '/admin/metabox.php' );
			include_once( BPMJ_EDD_PC_INCLUDES . '/admin/settings.php' );
			include_once( BPMJ_EDD_PC_INCLUDES . '/admin/customers.php' );
		}

		// Rejestruje zaczep deaktywacji wtyczki
		register_deactivation_hook( BPMJ_EDD_PC_FILE, 'bpmj_eddpc_on_deactivate_callback' );

		if ( ! wp_next_scheduled( 'bpmj_eddpc_http_request_cron_hook' ) ) {
			wp_schedule_event( time(), 'bpmj_eddpc_1min', 'bpmj_eddpc_http_request_cron_hook' );
		}

		// Instancja nie jest nam tu potrzebna, ale dzięki temu inicjujemy klasę
		include_once( BPMJ_EDD_PC_INCLUDES . '/class-user-access.php' );
		BPMJ_EDDPC_User_Access::instance();

		// CRON HOOK
		//add_action( 'admin_init', array( $this, 'bpmj_eddpc_http_request' ) );
		add_action( 'bpmj_eddpc_http_request_cron_hook', array( $this, 'bpmj_eddpc_http_request' ) );


		// AccessTime BUG
		//add_action( 'admin_init', array( $this, 'bpmj_eddpc_access_time_bug' ) );
		$this->auto_upgrade();
	}

	// Rejestracja domeny dla tłumaczeń
	private function load_textdomain() {
		$lang_dir = dirname( plugin_basename( BPMJ_EDD_PC_FILE ) ) . '/languages/';
		load_plugin_textdomain( 'edd-paid-content', false, $lang_dir );
	}

	// Rejestracja zaczepu crona
	public function bpmj_eddpc_http_request() {
		require_once BPMJ_EDD_PC_INCLUDES . '/cron-renewals.php';
	}

	// Rejestracja zaczepu pliku naprawczego
	public function bpmj_eddpc_access_time_bug() {
		require_once BPMJ_EDD_PC_INCLUDES . '/access-time-bug.php';
	}

	/**
	 * @param bool $mark_only
	 */
	public function auto_upgrade( $mark_only = false ) {
		include_once( BPMJ_EDD_PC_INCLUDES . '/admin/class-upgrades.php' );
		if ( $mark_only ) {
			BPMJ_EDDPC_Upgrades::instance()->auto_upgrade( true );
		} else {
			add_action( 'admin_init', array( BPMJ_EDDPC_Upgrades::instance(), 'auto_upgrade' ), 100 );
		}
	}
}

//Rejestracja aktywacyjnego zaczepu
function bpmj_eddpc_on_activate_callback() {

}

//Rejestracja deaktywacyjnego zaczepu
function bpmj_eddpc_on_deactivate_callback() {

}

// Inicjuje wszystko
$bpmj_eddpc_plugin_base = new BPMJ_EDD_PC_Plugin_Base;
