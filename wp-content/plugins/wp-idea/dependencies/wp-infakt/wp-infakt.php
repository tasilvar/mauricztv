<?php


// Zakoncz, jeżeli plik jest załadowany bezpośrednio
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Główna klasa wtyczki
 *
 */
class BPMJ_PLUGIN_WP_Infakt {

	private static $instance;

	/*
	 * Inicjalizacja struktury wtyczki
	 */

	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof BPMJ_PLUGIN_WP_Infakt ) ) {
			self::$instance = new BPMJ_PLUGIN_WP_Infakt;
			self::$instance->constants();
			self::$instance->includes();
			self::$instance->load_textdomain();
		}

		return self::$instance;
	}

	/**
	 * Definicje stałych wtyczki
	 */
	private function constants() {

		if ( ! defined( 'BPMJ_UPSELL_STORE_URL' ) ) {
			define( 'BPMJ_UPSELL_STORE_URL', 'http://upsell.pl' );
		}

		// Wersja wtyczki
		if ( ! defined( 'BPMJ_WPINFAKT_VERSION' ) ) {
			define( 'BPMJ_WPINFAKT_VERSION', '1.0.0' );
		}

		// Nazwa wtyczki
		if ( ! defined( 'BPMJ_WPINFAKT_NAME' ) ) {
			define( 'BPMJ_WPINFAKT_NAME', 'WP Infakt' );
		}

		/** @define "BPMJ_WPINFAKT_DIR" "./" */
		// Ścieżka do wtyczki ( serwer )
		if ( ! defined( 'BPMJ_WPINFAKT_DIR' ) ) {
			define( 'BPMJ_WPINFAKT_DIR', plugin_dir_path( __FILE__ ) );
		}

		// Ścieżka do wtyczki ( URL )
		if ( ! defined( 'BPMJ_WPINFAKT_URL' ) ) {
			define( 'BPMJ_WPINFAKT_URL', plugin_dir_url( __FILE__ ) );
		}

		// Główny plik wtyczki
		if ( ! defined( 'BPMJ_WPINFAKT_FILE' ) ) {
			define( 'BPMJ_WPINFAKT_FILE', __FILE__ );
		}

		if ( ! defined( 'BPMJ_WPINFAKT_PATH' ) ) {
			define( 'BPMJ_WPINFAKT_PATH', dirname( __FILE__ ) );
		}
	}

	/**
	 * Dołącza niezbędne pliki i inicjuje opcje
	 */
	private function includes() {
		global $bpmj_wpinfakt_settings;

		require_once BPMJ_WPINFAKT_DIR . 'includes/admin/settings/register-settings.php';

		$bpmj_wpinfakt_settings = get_option( 'bpmj_wpinfakt_settings' );

		if ( empty( $bpmj_wpinfakt_settings ) ) {
			$bpmj_wpinfakt_settings = array();
		}


		require_once BPMJ_WPINFAKT_DIR . 'includes/post-types.php';
		require_once BPMJ_WPINFAKT_DIR . 'includes/scripts.php';
		require_once BPMJ_WPINFAKT_DIR . 'includes/functions.php';
		require_once BPMJ_WPINFAKT_DIR . 'includes/actions.php';
		require_once BPMJ_WPINFAKT_DIR . 'includes/filters.php';
		require_once BPMJ_WPINFAKT_DIR . '../invoices/common/class-base-invoice.php';
		require_once BPMJ_WPINFAKT_DIR . '../invoices/common/class-base-invoice-item.php';
		require_once BPMJ_WPINFAKT_DIR . '../invoices/common/class-invoice-factory.php';
		require_once BPMJ_WPINFAKT_DIR . '../invoices/common/class-invoice-factory-edd.php';
		require_once BPMJ_WPINFAKT_DIR . '../invoices/common/class-invoice-factory-betterpay.php';
		require_once BPMJ_WPINFAKT_DIR . '../invoices/common/class-invoice-factory-woocommerce.php';
		require_once BPMJ_WPINFAKT_DIR . 'includes/class-wp-infakt.php';
		require_once BPMJ_WPINFAKT_DIR . 'includes/integration/edd/edd.php';

		if ( is_admin() ) {
			require_once BPMJ_WPINFAKT_DIR . 'includes/admin/admin-menu.php';
			require_once BPMJ_WPINFAKT_DIR . 'includes/admin/settings/settings-view.php';
			require_once BPMJ_WPINFAKT_DIR . 'includes/integration/edd/metabox.php';
		}

		require_once BPMJ_WPINFAKT_DIR . 'includes/install.php';
	}

	/*
	 * Ładuje pliki językowe
	 */

	public function load_textdomain() {

		$bpmj_wpinfakt_lang_dir = dirname( plugin_basename( BPMJ_WPINFAKT_FILE ) ) . '/languages/';
		load_plugin_textdomain( 'bpmj_wpinfakt', false, $bpmj_wpinfakt_lang_dir );
	}

}

// Inicjuje wtyczkę
BPMJ_PLUGIN_WP_Infakt::instance();

// CRON
if ( ! wp_next_scheduled( 'bpmj_wpinfakt_cron' ) ) {
	wp_schedule_event( time(), 'bpmj_wpinfakt_min', 'bpmj_wpinfakt_cron' );
}