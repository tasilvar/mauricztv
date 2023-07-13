<?php

// Zakoncz, jeżeli plik jest załadowany bezpośrednio
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Główna klasa wtyczki
 *
 */
class BPMJ_PLUGIN_WP_Taxe {

	private static $instance;

	/*
	 * Inicjalizacja struktury wtyczki
	 */

	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof BPMJ_PLUGIN_WP_Taxe ) ) {
			self::$instance = new BPMJ_PLUGIN_WP_Taxe;
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
		if ( ! defined( 'BPMJ_WPTAXE_VERSION' ) ) {
			define( 'BPMJ_WPTAXE_VERSION', '1.0.0' );
		}

		// Nazwa wtyczki
		if ( ! defined( 'BPMJ_WPTAXE_NAME' ) ) {
			define( 'BPMJ_WPTAXE_NAME', 'WP Taxe' );
		}

		/** @define "BPMJ_WPTAXE_DIR" "./" */
		// Ścieżka do wtyczki ( serwer )
		if ( ! defined( 'BPMJ_WPTAXE_DIR' ) ) {
			define( 'BPMJ_WPTAXE_DIR', plugin_dir_path( __FILE__ ) );
		}

		// Ścieżka do wtyczki ( URL )
		if ( ! defined( 'BPMJ_WPTAXE_URL' ) ) {
			define( 'BPMJ_WPTAXE_URL', plugin_dir_url( __FILE__ ) );
		}

		// Główny plik wtyczki
		if ( ! defined( 'BPMJ_WPTAXE_FILE' ) ) {
			define( 'BPMJ_WPTAXE_FILE', __FILE__ );
		}

		if ( ! defined( 'BPMJ_WPTAXE_PATH' ) ) {
			define( 'BPMJ_WPTAXE_PATH', dirname( __FILE__ ) );
		}
	}

	/**
	 * Dołącza niezbędne pliki i inicjuje opcje
	 */
	private function includes() {
		global $bpmj_wptaxe_settings;

		require_once BPMJ_WPTAXE_DIR . 'includes/admin/settings/register-settings.php';

		$bpmj_wptaxe_settings = get_option( 'bpmj_wptaxe_settings' );

		if ( empty( $bpmj_wptaxe_settings ) ) {
			$bpmj_wptaxe_settings = array();
		}


		require_once BPMJ_WPTAXE_DIR . 'includes/post-types.php';
		require_once BPMJ_WPTAXE_DIR . 'includes/scripts.php';
		require_once BPMJ_WPTAXE_DIR . 'includes/functions.php';
		require_once BPMJ_WPTAXE_DIR . 'includes/actions.php';
		require_once BPMJ_WPTAXE_DIR . 'includes/filters.php';
		require_once BPMJ_WPTAXE_DIR . '../invoices/common/class-base-invoice.php';
		require_once BPMJ_WPTAXE_DIR . '../invoices/common/class-base-invoice-item.php';
		require_once BPMJ_WPTAXE_DIR . '../invoices/common/class-invoice-factory.php';
		require_once BPMJ_WPTAXE_DIR . '../invoices/common/class-invoice-factory-edd.php';
		require_once BPMJ_WPTAXE_DIR . '../invoices/common/class-invoice-factory-betterpay.php';
		require_once BPMJ_WPTAXE_DIR . '../invoices/common/class-invoice-factory-woocommerce.php';
		require_once BPMJ_WPTAXE_DIR . 'includes/class-wp-taxe.php';
		require_once BPMJ_WPTAXE_DIR . 'includes/integration/edd/edd.php';

		if ( is_admin() ) {
			require_once BPMJ_WPTAXE_DIR . 'includes/admin/admin-menu.php';
			require_once BPMJ_WPTAXE_DIR . 'includes/admin/settings/settings-view.php';
			require_once BPMJ_WPTAXE_DIR . 'includes/integration/edd/metabox.php';
		}

		require_once BPMJ_WPTAXE_DIR . 'includes/install.php';
	}

	/*
	 * Ładuje pliki językowe
	 */

	public function load_textdomain() {

		$bpmj_wptaxe_lang_dir = dirname( plugin_basename( BPMJ_WPTAXE_FILE ) ) . '/languages/';
		load_plugin_textdomain( 'bpmj_wptaxe', false, $bpmj_wptaxe_lang_dir );
	}

}

// Inicjuje wtyczkę
BPMJ_PLUGIN_WP_Taxe::instance();

// CRON
if ( ! wp_next_scheduled( 'bpmj_wptaxe_cron' ) ) {
	wp_schedule_event( time(), 'bpmj_wptaxe_min', 'bpmj_wptaxe_cron' );
}