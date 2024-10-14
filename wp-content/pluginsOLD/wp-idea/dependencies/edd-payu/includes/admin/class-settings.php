<?php

namespace bpmj\wp\eddpayu\admin;

class Settings {
	/**
	 * @var Settings
	 */
	private static $instance;

	private function __construct() {
		add_filter( 'edd_settings_gateways', array( $this, 'filter_gateway_settings' ) );
		add_filter( 'edd_settings_sections_gateways', array( $this, 'filter_gateway_settings_sections' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'hook_enqueue_scripts' ) );
	}

	/**
	 * @return Settings
	 */
	public static function instance() {
		if ( ! isset( static::$instance ) ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * @param array $edd_gw_settings
	 *
	 * @return array
	 */
	public function filter_gateway_settings( $edd_gw_settings ) {

		$payu_settings = array(
			'payu_settings'                           => array(
				'id'   => 'payu_settings',
				'name' => '<strong>' . __( 'PayU Settings', BPMJ_EDDPAYU_DOMAIN ) . '</strong>',
				'type' => 'header',
			),
			'payu_pos_id'                             => array(
				'id'   => 'payu_pos_id',
				'name' => __( 'PayU POS Id', BPMJ_EDDPAYU_DOMAIN ),
				'desc' => __( 'Enter your PayU POS Id', BPMJ_EDDPAYU_DOMAIN ),
				'type' => 'text',
				'size' => 'regular',
			),
			'payu_pos_auth_key'                       => array(
				'id'   => 'payu_pos_auth_key',
				'name' => __( 'PayU POS auth key', BPMJ_EDDPAYU_DOMAIN ),
				'desc' => __( 'Enter your POS\' PayU payment authorization key (pos_auth_key), 7 characters', BPMJ_EDDPAYU_DOMAIN ),
				'type' => 'text',
				'size' => 'regular',
			),
			'payu_key1'                               => array(
				'id'   => 'payu_key1',
				'name' => __( 'PayU key:', BPMJ_EDDPAYU_DOMAIN ),
				'desc' => __( 'Enter your POS\' first key (MD5)', BPMJ_EDDPAYU_DOMAIN ),
				'type' => 'text',
				'size' => 'regular',
			),
			'payu_key2'                               => array(
				'id'   => 'payu_key2',
				'name' => __( 'PayU second key:', BPMJ_EDDPAYU_DOMAIN ),
				'desc' => __( 'Enter your POS\' second key (MD5)', BPMJ_EDDPAYU_DOMAIN ),
				'type' => 'text',
				'size' => 'regular',
			),
			'payu_api_type'                           => array(
				'id'      => 'payu_api_type',
				'name'    => __( 'PayU API type:', BPMJ_EDDPAYU_DOMAIN ),
				'desc'    => __( 'Select your POS\' API type', BPMJ_EDDPAYU_DOMAIN ),
				'type'    => 'radio',
				'options' => array(
					'rest'    => __( 'REST (Checkout - Express Payment)', BPMJ_EDDPAYU_DOMAIN ),
					'classic' => __( 'Classic (Express Payment)', BPMJ_EDDPAYU_DOMAIN ),
				),
				'std'     => 'rest',
			),
			'payu_api_environment'                    => array(
				'id'      => 'payu_api_environment',
				'name'    => __( 'PayU API environment:', BPMJ_EDDPAYU_DOMAIN ),
				'desc'    => __( 'Select PayU API environment', BPMJ_EDDPAYU_DOMAIN ),
				'type'    => 'radio',
				'options' => array(
					'secure'  => __( 'Secure (default)', BPMJ_EDDPAYU_DOMAIN ),
					'sandbox' => __( 'Sandbox (for testing)', BPMJ_EDDPAYU_DOMAIN ),
				),
				'std'     => 'secure',
			),
			'payu_return_url_failure'                 => array(
				'id'    => 'payu_return_url_failure',
				'name'  => __( 'PayU return URL - failure:', BPMJ_EDDPAYU_DOMAIN ),
				'desc'  => __( 'Copy and paste this URL in your POS settings in PayU control panel', BPMJ_EDDPAYU_DOMAIN ),
				'type'  => 'payu_return_url',
				'value' => edd_get_failed_transaction_uri() . '?payu_transaction=%transId%&payu_session=%sessionId%&payu_error=%error%',
			),
			'payu_return_url_success'                 => array(
				'id'    => 'payu_return_url_success',
				'name'  => __( 'PayU return URL - success:', BPMJ_EDDPAYU_DOMAIN ),
				'desc'  => __( 'Copy and paste this URL in your POS settings in PayU control panel', BPMJ_EDDPAYU_DOMAIN ),
				'type'  => 'payu_return_url',
				'value' => edd_get_success_page_uri() . '?payu_transaction=%transId%&payu_session=%sessionId%',
			),
			'payu_return_url_reports'                 => array(
				'id'    => 'payu_return_url_reports',
				'name'  => __( 'PayU reports URL:', BPMJ_EDDPAYU_DOMAIN ),
				'desc'  => __( 'Copy and paste this URL in your POS settings in PayU control panel', BPMJ_EDDPAYU_DOMAIN ),
				'type'  => 'payu_return_url',
				'value' => home_url( '/' ),
			),
			'payu_recurrence_allow_standard_payments' => array(
				'id'   => 'payu_recurrence_allow_standard_payments',
				'name' => __( 'Enable standard payment methods for recurrent orders', BPMJ_EDDPAYU_DOMAIN ),
				'desc' => __( 'When enabled customers will be able to choose non-card payment methods to pay for recurrent products. The system will automatically generate payments for consecutive periods, but the customer has to be informed and make the payment manually. Automatic charging is possible only with credit card payments.', BPMJ_EDDPAYU_DOMAIN ),
				'type' => 'checkbox',
			),
			'payu_enable_debug' => array(
				'id'   => 'payu_enable_debug',
				'name' => __( 'Enable debug', BPMJ_EDDPAYU_DOMAIN ),
				'desc' => __( 'When enabled, the system will store additional diagnostic information during payment process.', BPMJ_EDDPAYU_DOMAIN ),
				'type' => 'checkbox',
			),
		);

		$edd_gw_settings[ 'payu' ] = $payu_settings;

		return $edd_gw_settings;
	}

	/**
	 * @param $sections
	 *
	 * @return mixed
	 */
	public function filter_gateway_settings_sections( $sections ) {
		$sections[ 'payu' ] = __( 'PayU', BPMJ_EDDPAYU_DOMAIN );

		return $sections;
	}

	public function hook_enqueue_scripts() {
		global $pagenow;
		if ( 'edit.php' === $pagenow && ! empty( $_GET[ 'page' ] ) && 'edd-settings' === $_GET[ 'page' ] && ! empty( $_GET[ 'tab' ] ) && 'gateways' === $_GET[ 'tab' ] && ! empty( $_GET[ 'section' ] ) && 'payu' === $_GET[ 'section' ] ) {
			wp_enqueue_script( 'bpmj_eddpayu_admin_settings', BPMJ_EDDPAYU_URL . 'assets/js/edd-payu-admin.min.js', array( 'jquery' ), BPMJ_EDDPAYU_VERSION );
		}
	}
}