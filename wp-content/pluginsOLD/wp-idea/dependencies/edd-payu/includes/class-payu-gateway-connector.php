<?php

namespace bpmj\wp\eddpayu;

use bpmj\wp\eddpayu\admin\Settings;
use bpmj\wp\eddpayu\gateway_handlers\PayuHandlerAbstract;

class PayuGatewayConnector {

	const PAYU_GATEWAY_ID = 'payu';

	/**
	 * @var PayuGatewayConnector
	 */
	private static $instance;

	/**
	 * @var Settings
	 */
	protected $settings;

	/**
	 * @var PayuHandlerAbstract
	 */
	protected $handler;

	/**
	 * @return PayuGatewayConnector
	 */
	public static function instance() {
		if ( ! isset( static::$instance ) ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * PayuGatewayConnector constructor
	 */
	private function __construct() {
		if ( is_admin() ) {
			add_action( 'admin_init', array( $this, 'hook_admin_init' ) );
			$this->settings = Settings::instance();
		}
		add_filter( 'edd_payment_gateways', array( $this, 'filter_register_gateway' ) );
		add_action( 'plugins_loaded', array( $this, 'hook_setup_payu_handler' ) );
		if ( BPMJ_EDDPAYU_DEV ) {
			add_filter( 'edd_download_is_recurring', function ( $enabled, $download_id ) {
				$download = new \EDD_Download( $download_id );
				if ( false !== strpos( $download->post_title, 'recurring' ) ) {
					return true;
				}

				return $enabled;
			}, 10, 3 );
		}
	}

	/**
	 *
	 */
	public function hook_admin_init() {
		if ( ! defined( 'EDD_VERSION' ) ) {
			return;
		}
		$enabled_gateways = edd_get_option( 'gateways' );
		if ( key_exists( 'payu_gateway', $enabled_gateways ) ) {
			$this->upgrade_gateway_settings( $enabled_gateways );
		}
	}

	/**
	 * @param array $gateways
	 *
	 * @return array mixed
	 */
	public function filter_register_gateway( $gateways ) {
		$gateways[ static::PAYU_GATEWAY_ID ] = array(
			'admin_label'    => __( 'PayU', BPMJ_EDDPAYU_DOMAIN ),
			'checkout_label' => __( 'PayU', BPMJ_EDDPAYU_DOMAIN ),
			'supports'       => array(
				'recurring_payments',
			),
		);

		return $gateways;
	}

	public function hook_setup_payu_handler() {
		if ( ! defined( 'EDD_VERSION' ) ) {
			add_action( 'admin_notices', array( $this, 'no_edd_notice' ) );

			return;
		}
		if ( edd_is_gateway_active( static::PAYU_GATEWAY_ID ) && false !== edd_get_option( 'payu_api_type' ) ) {
			$api_type           = edd_get_option( 'payu_api_type' );
			$gateway_handler_ns = __NAMESPACE__ . '\gateway_handlers';
			$api_handler_class  = $gateway_handler_ns . '\PayuHandler' . ucfirst( strtolower( $api_type ) );
			if ( class_exists( $api_handler_class ) && is_subclass_of( $api_handler_class, $gateway_handler_ns . '\PayuHandlerAbstract' ) ) {
				$this->handler = new $api_handler_class();
				$this->handler->bootstrap();
			}
		}
	}

	/**
	 * @param $enabled_gateways
	 */
	protected function upgrade_gateway_settings( $enabled_gateways ) {
		/*
		 * payu_gateway is the key for old (1.0) EDD PayU gateway - we check if it's enabled and, if so, upgrade
		 * settings to gracefully enable new gateway
		 */
		unset( $enabled_gateways[ 'payu_gateway' ] );
		$enabled_gateways[ static::PAYU_GATEWAY_ID ] = '1';
		edd_update_option( 'gateways', $enabled_gateways );

		$old_pos_id      = edd_get_option( 'payu_id' );
		$old_pos_auth_id = edd_get_option( 'payu_pin' );

		edd_update_option( 'payu_pos_id', $old_pos_id );
		edd_update_option( 'payu_pos_auth_key', $old_pos_auth_id );

		// old plugin supported only classic API
		edd_update_option( 'payu_api_type', 'classic' );

		edd_delete_option( 'payu_id' );
		edd_delete_option( 'payu_pin' );

		if ( 'payu_gateway' === edd_get_option( 'default_gateway' ) ) {
			// we need to update default gateway to new one
			edd_update_option( 'default_gateway', static::PAYU_GATEWAY_ID );
		}
	}

	/**
	 * @return PayuHandlerAbstract
	 */
	public function get_handler() {
		return $this->handler;
	}

	/**
	 *
	 */
	public function no_edd_notice() {
		?>
        <div class="error">
            <p>
				<?php
				_e( 'WP EDD PayU requires Easy Digital Downloads to run properly.', BPMJ_EDDPAYU_DOMAIN );
				?>
            </p>
        </div>
		<?php
	}
}