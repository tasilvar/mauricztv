<?php

namespace bpmj\wp\eddpayu;

use bpmj\wp\eddpayu\service\EddExtensions;
use bpmj\wpidea\Current_Request;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Plugin {

	/**
	 * Plugin's instance
	 * @var Plugin
	 */
	private static $instance;

	/**
	 * @var EddExtensions
	 */
	protected $edd_extensions;

	/**
	 * @var PayuGatewayConnector
	 */
	private $connector;

	private Current_Request $current_request;

	/**
	 * Private constructor to prevent direct instantiation
	 */
	private function __construct() {
	    $this->current_request = new Current_Request();
	}

	/**
	 * Get Plugin's instance
	 *
	 * @return Plugin
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new static();
		}

		return self::$instance;
	}

	/**
	 * Bootstrap plugin
	 */
	public function bootstrap() {
		$this->bootstrap_autoloader();
		$this->bootstrap_includes();
		$this->bootstrap_textdomain();
		$this->bootstrap_hooks();
		$this->bootstrap_connector();
		$this->bootstrap_edd_extensions();
		$this->bootstrap_activation_hooks();
	}

	/**
	 * Prepare plugin and composer autoloader
	 */
	protected function bootstrap_autoloader() {
		// This plugin's autoloader
		spl_autoload_register( array( $this, 'autoload' ) );

		if ( defined( 'BPMJ_EDDPAYU_INCLUDES_VENDOR_DIR' ) ) {
			// Composer autoloader
			require BPMJ_EDDPAYU_INCLUDES_VENDOR_DIR . '/autoload.php';
		}
	}

	/**
	 * Autoload class
	 *
	 * @param string $class_fqn
	 *
	 * @return boolean
	 */
	public function autoload( $class_fqn ) {
		if ( strpos( $class_fqn, BPMJ_EDDPAYU_NAMESPACE ) !== 0 ) {
			return false;
		}

		/*
		 * if $class_fqn = \BPMJ\EDDFM\Some\Dir\ClassName
		 * then $class becomes 'Some\Dir\ClassName'
		 */
		$class = substr( $class_fqn, strrpos( $class_fqn, BPMJ_EDDPAYU_NAMESPACE ) + BPMJ_EDDPAYU_NAMESPACE_LENGTH + 1 );

		// $class_path becomes 'some/dir' or 'some\dir' on Windows
		$class_path = strtolower( dirname( str_replace( '\\', DIRECTORY_SEPARATOR, $class ) ) );

		foreach ( array( 'class', 'interface', 'abstract' ) as $prefix ) {
			/*
			 * if $class = 'Some\Dir\ClassName'
			 * then $class_base_name becomes 'Class-Name'
			 */
			$class_base_name = preg_replace( '/([a-z])([A-Z])/', '$1-$2', basename( str_replace( '\\', '/', $class ) ) );

			// $class_file becomes wordpressy 'class-class-name.php'
			$class_file = $prefix . '-' . strtolower( $class_base_name ) . '.php';

			$absolute_file_path = BPMJ_EDDPAYU_INCLUDES_DIR . DIRECTORY_SEPARATOR . $class_path . DIRECTORY_SEPARATOR . $class_file;
			if ( file_exists( $absolute_file_path ) ) {
				include $absolute_file_path;
				break;
			}
		}

		return true;
	}

	/**
	 * Setup Wordpress text domain
	 */
	protected function bootstrap_textdomain() {
		load_plugin_textdomain( BPMJ_EDDPAYU_DOMAIN, false, dirname( plugin_basename( BPMJ_EDDPAYU_FILE ) ) . '/languages/' );
	}

	/**
	 * Setup hooks
	 */
	protected function bootstrap_hooks() {
		add_action( 'init', [$this, 'hook_init'] );
		add_action( 'template_redirect', [$this, 'hook_template_redirect'] );
	}

	/**
	 * Action to perform on Wordpress 'init'
	 */
	public function hook_init() {
		do_action( BPMJ_EDDPAYU_EVENT_BEFORE_INIT );
		do_action( BPMJ_EDDPAYU_EVENT_INIT );
	}

	public function hook_template_redirect() {
	    eddpayu_maybe_redirect_to_failed_page($this->current_request);
	}
	
	/**
	 * Prefix given name with plugin's unique string
	 *
	 * @param string $name
	 *
	 * @return string
	 */
	public static function prefix_name( $name ) {
		return BPMJ_EDDPAYU_PREFIX . '_' . $name;
	}

	/**
	 *
	 */
	protected function bootstrap_includes() {
		if ( is_admin() ) {
			require BPMJ_EDDPAYU_INCLUDES_ADMIN_DIR . '/handlers.php';
		}
		include_once BPMJ_EDDPAYU_INCLUDES_DIR . '/functions.php';
		include_once BPMJ_EDDPAYU_INCLUDES_DIR . '/filters.php';
		include_once BPMJ_EDDPAYU_INCLUDES_DIR . '/metabox.php';
		include_once BPMJ_EDDPAYU_INCLUDES_DIR . '/mutex.php';
	}

	/**
	 * Create PayuGatewayConnector instance
	 */
	protected function bootstrap_connector() {
		$this->connector = PayuGatewayConnector::instance();
	}

	/**
	 * Create EddExtensions instance
	 */
	protected function bootstrap_edd_extensions() {
		$this->edd_extensions = EddExtensions::instance();
	}

	/**
	 * @return PayuGatewayConnector
	 */
	public function get_connector() {
		return $this->connector;
	}

	/**
	 *
	 */
	protected function bootstrap_activation_hooks() {
		register_deactivation_hook( __FILE__, array( $this, 'hook_unschedule_cron_jobs' ) );
	}

	/**
	 *
	 */
	public function hook_unschedule_cron_jobs() {
		wp_clear_scheduled_hook( 'bpmj_eddpayu_recurrent_payments' );
	}
}
