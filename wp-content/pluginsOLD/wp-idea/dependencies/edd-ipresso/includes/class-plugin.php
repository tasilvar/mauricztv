<?php

namespace bpmj\wp\eddip;

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
	 * EDDFM list handler instance
	 * @var \bpmj\wp\eddip\iPresso
	 */
	protected $handler;

	/**
	 * Private constructor to prevent direct instantiation
	 */
	private function __construct() {

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
		$this->bootstrap_handler();
	}

	/**
	 * Prepare plugin and composer autoloader
	 */
	protected function bootstrap_autoloader() {
		// This plugin's autoloader
		spl_autoload_register( array( $this, 'autoload' ) );

		if ( defined( 'BPMJ_EDDIP_INCLUDES_VENDOR_DIR' ) ) {
			// Composer autoloader
			require BPMJ_EDDIP_INCLUDES_VENDOR_DIR . '/autoload.php';
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
		if ( strpos( $class_fqn, BPMJ_EDDIP_NAMESPACE ) !== 0 ) {
			return false;
		}

		/*
		 * if $class_fqn = \BPMJ\EDDFM\Some\Dir\ClassName
		 * then $class becomes 'Some\Dir\ClassName'
		 */
		$class = substr( $class_fqn, strrpos( $class_fqn, BPMJ_EDDIP_NAMESPACE ) + BPMJ_EDDIP_NAMESPACE_LENGTH + 1 );

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

			$absolute_file_path = BPMJ_EDDIP_INCLUDES_DIR . DIRECTORY_SEPARATOR . $class_path . DIRECTORY_SEPARATOR . $class_file;
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
		load_plugin_textdomain( BPMJ_EDDIP_DOMAIN, false, basename( BPMJ_EDDIP_DIR ) . '/languages' );
	}

	/**
	 * Setup hooks
	 */
	protected function bootstrap_hooks() {
		add_action( 'init', array( $this, 'hook_init' ) );
		add_action( 'admin_init', array( $this, 'hook_admin_scripts' ) );
		add_action( 'wp_head', array( $this, 'hook_ipresso_tracking_code' ) );
	}

	/**
	 * Action to perform on Wordpress 'init'
	 */
	public function hook_init() {
		do_action( BPMJ_EDDIP_EVENT_BEFORE_INIT );
		do_action( BPMJ_EDDIP_EVENT_INIT );
	}

	/**
	 * Register scripts and styles for backend
	 */
	public function hook_admin_scripts() {
		// Scripts
		wp_register_script( 'bpmj_eddip_select2', BPMJ_EDDIP_URL . 'assets/js/jquery.tagsinput.min.js', array( 'jquery' ) );
		wp_register_script( 'bpmj_eddip_admin_script', BPMJ_EDDIP_URL . 'assets/js/scripts.js', array( 'jquery' ) );


		wp_enqueue_script( 'bpmj_eddip_select2' );
		wp_enqueue_script( 'bpmj_eddip_admin_script' );
		wp_localize_script( 'bpmj_eddip_admin_script', 'bpmj_eddip_admin', array(
			'add_tag' => __( 'Add tag', BPMJ_EDDIP_DOMAIN ),
		) );

		// Styles
		wp_register_style( 'bpmj_eddip_select2', BPMJ_EDDIP_URL . 'assets/css/jquery.tagsinput.min.css' );
		wp_enqueue_style( 'bpmj_eddip_select2' );
	}

	/**
	 * Output iPresso tracking code (if one's specified)
	 */
	public function hook_ipresso_tracking_code() {
		global $edd_options;

		if ( ! empty( $edd_options[ 'bpmj_eddip_tracking_code' ] ) ) {
			echo $edd_options[ 'bpmj_eddip_tracking_code' ];
		}
	}

	/**
	 * Prefix given name with plugin's unique string
	 *
	 * @param string $name
	 *
	 * @return string
	 */
	public static function prefix_name( $name ) {
		return BPMJ_EDDIP_PREFIX . '_' . $name;
	}

	protected function bootstrap_includes() {

		if ( ! class_exists( 'EDD_Newsletter_V2' ) ) {
			require_once BPMJ_EDDIP_INCLUDES_DIR . '/class-edd-newsletter-v2.php';
		}

		if ( file_exists( BPMJ_EDDIP_INCLUDES_DIR . '/handlers.php' ) ) {
			require_once BPMJ_EDDIP_INCLUDES_DIR . '/handlers.php';
		}
	}

	private function bootstrap_handler() {
		$this->handler = new iPresso( 'ipresso', 'iPresso' );
	}

	/**
	 * @return iPresso
	 */
	public function get_ipresso_handler() {
		return $this->handler;
	}
}
