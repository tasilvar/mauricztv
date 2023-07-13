<?php

namespace bpmj\wp\eddfm;

if ( !defined( 'ABSPATH' ) ) {
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
	 * @var \bpmj\wp\eddfm\Freshmail
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
		if ( !isset( self::$instance ) ) {
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

		if ( defined( 'BPMJ_EDDFM_INCLUDES_VENDOR_DIR' ) ) {
			// Composer autoloader
			require BPMJ_EDDFM_INCLUDES_VENDOR_DIR . '/autoload.php';
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
		if ( strpos( $class_fqn, BPMJ_EDDFM_NAMESPACE ) !== 0 ) {
			return false;
		}

		/*
		 * if $class_fqn = \BPMJ\EDDFM\Some\Dir\ClassName
		 * then $class becomes 'Some\Dir\ClassName'
		 */
		$class = substr( $class_fqn, strrpos( $class_fqn, BPMJ_EDDFM_NAMESPACE ) + BPMJ_EDDFM_NAMESPACE_LENGTH + 1 );

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

			$absolute_file_path = BPMJ_EDDFM_INCLUDES_DIR . DIRECTORY_SEPARATOR . $class_path . DIRECTORY_SEPARATOR . $class_file;
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
		load_plugin_textdomain( BPMJ_EDDFM_DOMAIN, false, basename( BPMJ_EDDFM_DIR ) . '/languages' );
	}

	/**
	 * Setup hooks
	 */
	protected function bootstrap_hooks() {
		add_action( 'init', array( $this, 'hook_init' ) );
	}

	/**
	 * Action to perform on Wordpress 'init'
	 */
	public function hook_init() {
		do_action( BPMJ_EDDFM_EVENT_BEFORE_INIT );
		do_action( BPMJ_EDDFM_EVENT_INIT );
	}

	/**
	 * Prefix given name with plugin's unique string
	 *
	 * @param string $name
	 *
	 * @return string
	 */
	public static function prefix_name( $name ) {
		return BPMJ_EDDFM_PREFIX . '_' . $name;
	}

	protected function bootstrap_includes() {
		if ( !class_exists( 'EDD_Newsletter_V2' ) ) {
			require_once BPMJ_EDDFM_INCLUDES_DIR . '/class-edd-newsletter-v2.php';
		}
	}

	private function bootstrap_handler() {
		$this->handler = new Freshmail( 'freshmail', 'FreshMail' );
	}

	/**
	 * @return Freshmail
	 */
	public function get_freshmail_handler() {
		return $this->handler;
	}
}
