<?php

namespace bpmj\wp\eddpayu\gateway_handlers;

use bpmj\wp\eddpayu\OAuthCacheTransient;
use bpmj\wp\eddpayu\PayuGatewayConnector;
use bpmj\wpidea\helpers\Price_Formatting;

class PayuHandlerAbstract {

	protected $pos_id;
	protected $pos_auth_key;
	protected $key1;
	protected $key2;
	protected $payu_form_set_up = false;
	protected $messages = array();
	protected $environment;
	/**
	 * @var bool
	 */
	protected $allow_noncard_payments = false;

	public function bootstrap() {
		if ( $this->environment ) {
			\OpenPayU_Configuration::setEnvironment( $this->environment, $this->get_api_domain(), $this->get_api_path(), $this->get_api_version() );
		}
		if ( $this->pos_id ) {
			\OpenPayU_Configuration::setMerchantPosId( $this->pos_id );
		}
		if ( $this->key2 ) {
			\OpenPayU_Configuration::setSignatureKey( $this->key2 );
		}
		if ( $this->pos_id ) {
			\OpenPayU_Configuration::setOauthClientId( $this->pos_id );
		}
		if ( $this->key1 ) {
			\OpenPayU_Configuration::setOauthClientSecret( $this->key1 );
		}
		\OpenPayU_Configuration::setOauthTokenCache( new OAuthCacheTransient() );

		remove_action( 'edd_after_cc_fields', 'edd_default_cc_address_fields' );
		remove_action( 'edd_cc_form', 'edd_get_cc_form' );

		/*
		 * When the request is done by AJAX we need to setup the PayU form earlier. On the other hand, when the request
		 * is NOT AJAX 'init' would be too early because at this point we cannot determine on what page the user
		 * currently is
		 */
		$hook = 'parse_request';
		if ( defined( 'DOING_AJAX' ) ) {
			$hook = 'init';
		}
		add_action( $hook, array( $this, 'hook_setup_payu_checkout' ), 9 );
		add_action( 'edd_checkout_cart_top', array( $this, 'hook_print_payu_checkout_messages' ) );
	}

	protected function get_api_domain() {
		return 'payu.com';
	}

	protected function get_api_path() {
		return 'api/';
	}

	protected function get_api_version() {
		return 'v2_1/';
	}

	/**
	 * Adds a message to the message queue
	 *
	 * @param string $message_type one of 'success', 'error', 'info', 'warn'
	 * @param string $message
	 */
	protected function set_message( $message_type, $message ) {
		if ( empty( $this->messages[ $message_type ] ) ) {
			$this->messages[ $message_type ] = array();
		}
		$this->messages[ $message_type ][] = $message;
	}

	/**
	 * @param array $purchase_data
	 *
	 * @return bool|int
	 */
	protected function create_payment_for_purchase( $purchase_data ) {
		$payment_data = array(
			'price'        => $purchase_data[ 'price' ],
			'user_email'   => $purchase_data[ 'user_email' ],
			'purchase_key' => $purchase_data[ 'purchase_key' ],
			'currency'     => edd_get_option( 'currency' ),
			'cart_details' => $purchase_data[ 'cart_details' ],
			'user_info'    => $purchase_data[ 'user_info' ],
			'status'       => 'pending',
		);
		$payment_id   = edd_insert_payment( $payment_data );

		if ( ! $payment_id ) {
			edd_record_gateway_error( __( 'Error on inserting a payment record', BPMJ_EDDPAYU_DOMAIN ), sprintf( __( 'Payment data: %s', BPMJ_EDDPAYU_DOMAIN ), json_encode( $payment_data ) ) );
			edd_send_back_to_checkout( '?payment-mode=' . $purchase_data[ 'post_data' ][ 'edd-gateway' ] );

			return false;
		}

		return $payment_id;
	}

	/**
	 * @param int $payment_id
	 *
	 * @return string
	 */
	protected function get_transaction_description( $payment_id ) {
		$bloginfo_name = preg_replace("/&#?[a-z0-9]{2,8};/i",'',get_bloginfo( 'name' )); 
        return $bloginfo_name . ' ' . sprintf( __( 'Payment no #%s', BPMJ_EDDPAYU_DOMAIN ), $payment_id );
	}

	/**
	 * @param array $purchase_data
	 *
	 * @return int
	 */
	protected function get_total_amount( $purchase_data ): int
    {
        return Price_Formatting::round_and_format_to_int( $purchase_data[ 'price' ], Price_Formatting::MULTIPLY_BY_100 );
	}

	public function hook_setup_payu_checkout() {
		if ( isset( $_REQUEST[ 'payment-mode' ] ) && PayuGatewayConnector::PAYU_GATEWAY_ID === $_REQUEST[ 'payment-mode' ]
		     || isset( $_REQUEST[ 'edd_payment_mode' ] ) && PayuGatewayConnector::PAYU_GATEWAY_ID === $_REQUEST[ 'edd_payment_mode' ]
		     /**
		      * We cannot use @see edd_is_checkout because it's to early for that
		      * - we have to check it with a more primitive solution
		      */
		     || ! defined( 'DOING_AJAX' ) && parse_url( edd_get_checkout_uri(), PHP_URL_PATH ) === parse_url( $_SERVER[ 'REQUEST_URI' ], PHP_URL_PATH ) && ! edd_show_gateways()
		) {
			$this->setup_edd_payu_purchase_form();
		}
	}

	/**
	 * This method is called only if PayU is the selected payment mode
	 */
	public function setup_edd_payu_purchase_form() {
		// stub
	}

	/**
	 * Prints various notification messages
	 */
	public function hook_print_payu_checkout_messages() {
		foreach ( $this->messages as $message_type => $message_array ):
			?>
            <div class="edd-alert edd-alert-<?php echo $message_type; ?>">
				<?php foreach ( $message_array as $message ): ?>
                    <p><?php echo $message; ?></p>
				<?php endforeach; ?>
            </div>
			<?php
		endforeach;
	}
}
