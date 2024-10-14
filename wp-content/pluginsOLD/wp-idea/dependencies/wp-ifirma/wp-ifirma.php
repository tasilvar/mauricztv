<?php

// Zakoncz, jeżeli plik jest załadowany bezpośrednio
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Główna klasa wtyczki
 *
 */
class BPMJ_PLUGIN_WP_iFirma {

	private static $instance;

	/*
	 * Inicjalizacja struktury wtyczki
	 */

	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof BPMJ_PLUGIN_WP_iFirma ) ) {
			self::$instance = new BPMJ_PLUGIN_WP_iFirma;
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
			define( 'BPMJ_UPSELL_STORE_URL', 'https://upsell.pl' );
		}

		// Wersja wtyczki
		if ( ! defined( 'BPMJ_WPIFIRMA_VERSION' ) ) {
			define( 'BPMJ_WPIFIRMA_VERSION', '1.4.1' );
		}

		// Nazwa wtyczki
		if ( ! defined( 'BPMJ_WPIFIRMA_NAME' ) ) {
			define( 'BPMJ_WPIFIRMA_NAME', 'WP iFirma' );
		}

		/** @define "BPMJ_WPIFIRMA_DIR" "./" */
		// Ścieżka do wtyczki ( serwer )
		if ( ! defined( 'BPMJ_WPIFIRMA_DIR' ) ) {
			define( 'BPMJ_WPIFIRMA_DIR', plugin_dir_path( __FILE__ ) );
		}

		// Ścieżka do wtyczki ( URL )
		if ( ! defined( 'BPMJ_WPIFIRMA_URL' ) ) {
			define( 'BPMJ_WPIFIRMA_URL', plugin_dir_url( __FILE__ ) );
		}

		// Główny plik wtyczki
		if ( ! defined( 'BPMJ_WPIFIRMA_FILE' ) ) {
			define( 'BPMJ_WPIFIRMA_FILE', __FILE__ );
		}

		if ( ! defined( 'BPMJ_WPIFIRMA_PATH' ) ) {
			define( 'BPMJ_WPIFIRMA_PATH', dirname( __FILE__ ) );
		}
	}

	/**
	 * Dołącza niezbędne pliki i inicjuje opcje
	 */
	private function includes() {
		global $bpmj_wpifirma_settings;

		require_once BPMJ_WPIFIRMA_DIR . 'includes/admin/settings/register-settings.php';

		$bpmj_wpifirma_settings = get_option( 'bpmj_wpifirma_settings' );

		if ( empty( $bpmj_wpifirma_settings ) ) {
			$bpmj_wpifirma_settings = array();
		}

		require_once BPMJ_WPIFIRMA_DIR . 'includes/post-types.php';
		require_once BPMJ_WPIFIRMA_DIR . 'includes/scripts.php';
		require_once BPMJ_WPIFIRMA_DIR . 'includes/functions.php';
		require_once BPMJ_WPIFIRMA_DIR . 'includes/actions.php';
		require_once BPMJ_WPIFIRMA_DIR . 'includes/filters.php';
		require_once BPMJ_WPIFIRMA_DIR . '../invoices/common/class-base-invoice.php';
		require_once BPMJ_WPIFIRMA_DIR . '../invoices/common/class-base-invoice-item.php';
		require_once BPMJ_WPIFIRMA_DIR . '../invoices/common/class-invoice-factory.php';
		require_once BPMJ_WPIFIRMA_DIR . '../invoices/common/class-invoice-factory-edd.php';
		require_once BPMJ_WPIFIRMA_DIR . '../invoices/common/class-invoice-factory-betterpay.php';
		require_once BPMJ_WPIFIRMA_DIR . '../invoices/common/class-invoice-factory-woocommerce.php';
		require_once BPMJ_WPIFIRMA_DIR . 'includes/class-wp-ifirma.php';
		require_once BPMJ_WPIFIRMA_DIR . 'includes/integration/edd/edd.php';


		if ( is_admin() ) {
			require_once BPMJ_WPIFIRMA_DIR . 'includes/admin/admin-menu.php';
			require_once BPMJ_WPIFIRMA_DIR . 'includes/admin/settings/settings-view.php';
			require_once BPMJ_WPIFIRMA_DIR . 'includes/integration/edd/metabox.php';
		}

		require_once BPMJ_WPIFIRMA_DIR . 'includes/install.php';
	}

	/*
	 * Ładuje pliki językowe
	 */

	public function load_textdomain() {

		$bpmj_wpifirma_lang_dir = dirname( plugin_basename( BPMJ_WPIFIRMA_FILE ) ) . '/languages/';
		load_plugin_textdomain( 'bpmj_wpifirma', false, $bpmj_wpifirma_lang_dir );
	}

}

// Inicjuje wtyczkę
BPMJ_PLUGIN_WP_iFirma::instance();

// CRON
if ( ! wp_next_scheduled( 'bpmj_wpifirma_cron' ) ) {
	wp_schedule_event( time(), 'bpmj_wpifirma_min', 'bpmj_wpifirma_cron' );
}