<?php

namespace bpmj\wp\eddtpay\gateways;

use bpmj\wp\eddtpay\service\EddExtensions;
use bpmj\wpidea\Caps;
use bpmj\wpidea\helpers\Translator_Static_Helper;

class TpayRecurrence {

	const ERROR_BAD_REQUEST = 1;
    const ERROR_BAD_HTTP_RESPONSE_BODY = 2;
    const ERROR_TPAY_ERROR = 3;
    const ERROR_WRONG_SIGN = 4;
    const HASH_TYPE = 'sha1';

	/**
	 * List of items for recurring payment - this array is filled during setup_edd_tpay_purchase_form (on 'init')
	 * @var array
	 */
	protected $cart_items_recurring_payment = array();

	/**
	 * List of items for standard payment - this array is filled during setup_edd_tpay_purchase_form (on 'init')
	 * @var array
	 */
	protected $cart_items_standard_payment = array();

	/**
	 * @var bool
	 */
	protected $cart_items_set_up = false;

	/**
	 * @var array
	 */
	protected $cart_discount_details = array();

	/**
	 * @var bool
	 */
	protected $tpay_form_set_up = false;

	/**
	 * @var array
	 */
	protected $messages = array();

	/**
	 * @var TpayRecurrence
	 */
	private static $instance;

	/**
	 * @var string
	 */
	protected $api_key;

	/**
	 * @var string
	 */
	protected $api_password;

	/**
	 * @var string
	 */
	protected $verification_code;

	/**
	 * @var bool
	 */
	protected $allow_noncard_payments;

	/**
	 * @return TpayRecurrence
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new static();
		}

		return self::$instance;
	}

	/**
	 * @return string
	 */
	protected static function get_standard_payment_description() {
		return __( 'Payments for future periods will be prepared automatically and you will be asked to fulfill them manually on each occurrence. 
			You can process future payment or cancel the subscription anytime.', 'edd-tpay' );
	}

	/**
	 * @return string
	 */
	protected static function get_cards_payment_description() {
		return __( 'Your credit card will be charged automatically for each period.
			You can cancel the subscription anytime.', 'edd-tpay' );
	}

	/**
	 * @return string
	 */
	protected static function get_standard_payment_label() {
		return __( 'Purchase and pay with other payment methods', 'edd-tpay' );
	}

	/**
	 * @param string $api_key
	 * @param string $api_password
	 * @param string $verification_code
	 * @param bool $allow_noncard_payments
	 */
	public function bootstrap( $api_key, $api_password, $verification_code, $allow_noncard_payments = false ) {
		$this->api_key                = $api_key;
		$this->api_password           = $api_password;
		$this->verification_code      = $verification_code;
		$this->allow_noncard_payments = $allow_noncard_payments;

		add_action( 'edd_gateway_tpay_gateway', array( $this, 'hook_process_payment' ), 1 );
		add_action( 'init', array( $this, 'hook_init' ) );
		add_action( 'edd_post_refund_payment', array( $this, 'hook_process_refund' ) );
		add_action( 'bpmj_eddtpay_recurrent_payments', array( $this, 'cron_process_recurrent_payments' ) );
		add_action( 'bpmj_eddtpay_failure_notifications', array( $this, 'cron_process_failure_notifications' ) );
		add_action( 'edd_purchase_history_header_after', array( $this, 'hook_purchase_history_header' ) );
		add_action( 'edd_purchase_history_row_end', array( $this, 'hook_purchase_history_row' ) );
		add_action( 'edd_payment_receipt_after', array( $this, 'hook_payment_receipt_options' ) );
		add_action( 'edd_updated_edited_purchase', array( $this, 'hook_updated_edited_purchase' ) );
		add_action( 'bpmjd_tra_edd_verify_tpay_cards', array( $this, 'hook_process_server_response' ) );
		add_action( 'bpmjd_tra_recurrence_create_future_payments', array(
			$this,
			'hook_recurrence_create_future_payments'
		) );
		add_action( 'edd_update_payment_status', array( $this, 'hook_update_payment_status' ), 10, 3 );
		add_filter( 'edd_direct_gateway_url', array( $this, 'filter_get_direct_gateway_url' ), 10, 2 );
		$this->schedule_recurrent_payments();
		$this->schedule_failure_notifications();

		if ( is_admin() ) {
			add_filter( 'edd_payments_table_column', array( $this, 'filter_modify_payments_table_column' ), 10, 3 );
		}

		if ( ! empty( $_REQUEST[ 'tpay_recurrent_payment_done' ] ) ) {
			$this->check_recurrent_payment_status( $_REQUEST[ 'tpay_recurrent_payment_done' ] );
		}

		if ( ! empty( $_REQUEST[ 'tpay-error' ] ) ) {
			$this->set_message( 'error', __( 'Tpay payment failed. Reason: unknown error.', 'edd-tpay' ) );
		}

		remove_action( 'edd_after_cc_fields', 'edd_default_cc_address_fields' );
		remove_action( 'edd_cc_form', 'edd_get_cc_form' );

		/*
		 * When the request is done by AJAX we need to setup the Tpay form earlier. On the other hand, when the request
		 * is NOT AJAX 'init' would be too early because at this point we cannot determine on what page the user
		 * currently is
		 */
		$hook = 'parse_request';
		if ( defined( 'DOING_AJAX' ) ) {
			$hook = 'init';
		}
		add_action( $hook, array( $this, 'hook_setup_tpay_checkout' ), 9 );
		add_action( 'edd_checkout_cart_top', array( $this, 'hook_print_tpay_checkout_messages' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ), 1 );
		add_action( 'edd_view_order_details_update_inner', array( $this, 'hook_view_order_details_update_inner' ) );
		
		$this->hotfix_pb_457_init();
	}

	/**
	 * @param array $purchase_data
	 */
	public function hook_process_payment( $purchase_data ) {
		try {
			$purchase_type                = ! empty( $purchase_data[ 'post_data' ][ 'edd-purchase' ] ) ? $purchase_data[ 'post_data' ][ 'edd-purchase' ] : '';
			$pay_for_recurring_items_only = 'edd_tpay_pay_for_recurring_items_only' === $purchase_type;
			$pay_with_credit_card         = $pay_for_recurring_items_only || 'edd_tpay_pay_for_all_items' === $purchase_type;
			if ( $pay_with_credit_card && 'edd_tpay_pay_for_all_items_standard' === $purchase_type ) {
				$pay_with_credit_card = false;
			}

			if ( $pay_for_recurring_items_only ) {
				$purchase_data[ 'price' ]        = $this->get_recurring_items_total_amount();
				$purchase_data[ 'cart_details' ] = $this->get_cart_items_recurring_payment();
			}

			$payment_id = $this->create_payment_for_purchase( $purchase_data );
			if ( false === $payment_id ) {
				return;
			}

			remove_action( 'edd_gateway_tpay_gateway', 'bpmjd_tra_edd_process_payment' );

			$cart_discount_details = $this->get_cart_discount_details();
			if ( $pay_with_credit_card ) {
				$redirect_url = $this->init_card_payment( $payment_id, $purchase_data, $pay_for_recurring_items_only );
			} else {
				$redirect_url = $this->init_standard_payment( $payment_id, $purchase_data );
			}

			if ( $redirect_url ) {
				update_post_meta( $payment_id, '_tpay_setup_recurrence', '1' );
				if ( ! empty( $cart_discount_details ) ) {
					update_post_meta( $payment_id, '_tpay_discount_details', $cart_discount_details );
				}

				wp_redirect( $redirect_url );
				exit;
			}
		} catch ( \Exception $e ) {
			edd_record_gateway_error( __( 'Could not create Tpay order (exception)', 'edd-tpay' ), $e->getMessage() );
			edd_send_back_to_checkout( array(
				'payment-mode' => $purchase_data[ 'post_data' ][ 'edd-gateway' ],
				'tpay-error'   => $e->getCode(),
			) );
		}
	}

	/**
	 * @param int $payment_id
	 * @param array $purchase_data
	 * @param bool $pay_for_recurring_items_only
	 *
	 * @return string
	 * @throws \Exception
	 */
	protected function init_card_payment( $payment_id, array $purchase_data, $pay_for_recurring_items_only = false ) {
		$currency     = edd_get_option( 'currency' );
		$continue_url = '';

		if ( $pay_for_recurring_items_only ) {
			$continue_url = edd_get_checkout_uri( array( 'tpay_recurrent_payment_done' => edd_get_payment_key( $payment_id ) ) );
		}

		$tpay_data = array(
			'name'     => $purchase_data[ 'user_info' ][ 'first_name' ] . ' ' . $purchase_data[ 'user_info' ][ 'last_name' ],
			'email'    => $purchase_data[ 'user_info' ][ 'email' ],
			'desc'     => $this->get_transaction_description( $payment_id ),
			'amount'   => $this->get_total_amount( $purchase_data ),
			'currency' => $this->get_tpay_currency_code( $currency ? $currency : 'PLN' ),
			'order_id' => $payment_id . '-' . preg_replace( '#^https?://#', '', get_option( 'siteurl' ) ),
            'onetimer' => '',
			'language' => substr( get_user_locale(), 0, 2 ),
		);

		$tpay_data[ 'sign' ] = hash(self::HASH_TYPE, 'register_sale&' . implode( '&', $tpay_data ) . '&' . $this->verification_code );

		$data      = $this->request_tpay_api( 'register_sale', $tpay_data );
		$sale_auth = isset( $data[ 'sale_auth' ] ) ? $data[ 'sale_auth' ] : '';
		if ( empty( $sale_auth ) ) {
			throw new \Exception( __( 'No sale_auth in the response', 'edd-tpay' ), 4 );
		}

		if ( $pay_for_recurring_items_only ) {
			$this->create_discount_for_standard_items();
			$this->remove_recurring_items_from_cart();
		} else {
			edd_empty_cart();
		}
		edd_insert_payment_note( $payment_id, __( 'Tpay sale registered.', 'edd-tpay' ) );
		if ( ! empty( $data[ 'cli_auth' ] ) ) {
			update_post_meta( $payment_id, '_tpay_cli_auth', $data[ 'cli_auth' ] );
		}
		update_post_meta( $payment_id, '_tpay_sale_auth', $sale_auth );
		if ( $continue_url ) {
			\WPI_Cart::instance()->session->set( 'tpay_continue_url', $continue_url );
		}

		return 'https://secure.tpay.com/cards/?sale_auth=' . $sale_auth;
	}

	/**
	 * @param int $payment_id
	 * @param array $purchase_data
	 *
	 * @return mixed|string
	 */
	protected function init_standard_payment( $payment_id, array $purchase_data ) {
		return bpmjd_tra_prepare_tpay_payment( $payment_id, $purchase_data );
	}

	/**
	 *
	 */
	public function hook_init() {
		$current_url_path = parse_url( $_SERVER[ 'REQUEST_URI' ], PHP_URL_PATH );
		if ( $current_url_path === parse_url( edd_get_success_page_uri(), PHP_URL_PATH ) && \WPI_Cart::instance()->session->get( 'tpay_continue_url' ) ) {
			wp_redirect( \WPI_Cart::instance()->session->get( 'tpay_continue_url' ) );
			exit;
		} else if ( isset( $_POST[ 'bpmj_eddtpay_cancel_subscription' ] ) ) {
			$this->process_cancel_subscription();
		} else if ( isset( $_POST[ 'bpmj_eddtpay_pay_for_subscription' ] ) ) {
			$this->process_pay_for_subscription();
		}
	}

	/**
	 *
	 * @return array
	 */
	public function hook_process_server_response() {
		$response = array( 'result' => 0, );
		try {
			if ( empty( $_POST ) ) {
				throw new \Exception( 'Empty body' );
			}

			$notification = $this->retrieve_and_check_cards_response();
			if ( ! is_array( $notification ) ) {
				throw new \Exception( 'Invalid or corrupt data' );
			}

			$type    = $notification[ 'type' ];
			$payment = null;
			if ( ! empty( $notification[ 'order_id' ] ) ) {
				$payment_id = $notification[ 'order_id' ];
				if ( false !== strpos( $payment_id, '-' ) ) {
					$payment_id = substr( $payment_id, 0, strpos( $payment_id, '-' ) );
				}
				$payment = get_post( $payment_id );
			}
			if ( 'sale' === $type ) {
				if ( ! $payment ) {
					throw new \Exception( sprintf( 'EDD order (%s) not found', $notification[ 'order_id' ] ) );
				}
				$payment_id = $payment->ID;
				if ( 'correct' === $notification[ 'status' ] && 'publish' !== $payment->post_status ) {
					if ( '1' === get_post_meta( $payment_id, '_tpay_setup_recurrence', true ) ) {
						delete_post_meta( $payment_id, '_tpay_setup_recurrence' );
						if ( empty( $notification[ 'cli_auth' ] ) ) {
							edd_insert_payment_note( $payment_id, __( 'Could not create recurrent payments - missing cli_auth', 'edd-tpay' ) );
						} else {
							update_post_meta( $payment_id, '_tpay_cli_auth', $notification[ 'cli_auth' ] );
							$this->split_and_create_future_payments( $payment_id );
						}
					}
					edd_insert_payment_note( $payment_id, __( 'Tpay transaction completed successfully', 'edd-tpay' ) );
					wp_update_post( array(
						'ID'            => $payment_id,
						'post_date'     => current_time( 'Y-m-d' ),
						'post_date_gmt' => get_gmt_from_date( current_time( 'Y-m-d' ) ),
					) );
					edd_update_payment_status( $payment_id, 'completed' );
				}
			} else if ( 'refund' === $type && $payment && 'publish' === $payment->post_status ) {
				$payment_id = $payment->ID;
				edd_insert_payment_note( $payment_id, __( 'Tpay transaction refunded successfully', 'edd-tpay' ) );
				edd_update_payment_status( $payment_id, 'refunded' );
			}

			$response[ 'result' ] = 1;
		} catch ( \Exception $e ) {
			$response[ 'error' ] = $e->getMessage();
		}

		return $response;
	}

	/**
	 * @param \EDD_Payment $payment
	 *
	 * @return bool
	 */
	public function hook_process_refund( \EDD_Payment $payment ) {
		$sale_auth = $payment->get_meta( '_tpay_sale_auth' );
		if ( ! $sale_auth ) {
			return false;
		}

		$refund_params           = array(
			'sale_auth' => $sale_auth,
			'desc'      => $this->get_transaction_description( $payment->ID ),
		);
		$refund_params[ 'sign' ] = hash(self::HASH_TYPE, implode( '', $refund_params ) . $this->verification_code );

		try {
			$response = $this->request_tpay_api( 'refund', $refund_params );
			if ( empty( $response[ 'status' ] ) ) {
				edd_insert_payment_note( $payment->ID, sprintf( __( 'Tpay transaction refund failed: %s', 'edd-tpay' ), $response[ 'reason' ] ) );
			} else {
				edd_insert_payment_note( $payment->ID, __( 'Tpay transaction refunded successfully', 'edd-tpay' ) );

				return true;
			}
		} catch ( \Exception $e ) {
			edd_insert_payment_note( $payment->ID, sprintf( __( 'Tpay transaction refund failed: %s', 'edd-tpay' ), $e->getMessage() ) );
		}

		return false;
	}

	/**
	 * @inheritdoc
	 */
	public function setup_edd_tpay_purchase_form() {
		if ( $this->tpay_form_set_up ) {
			return;
		}
		$this->tpay_form_set_up = true;

		$this->setup_cart_items();

		$anything_recurring = ! empty( $this->cart_items_recurring_payment );
		$anything_standard  = ! empty( $this->cart_items_standard_payment );

		add_filter( 'edd_get_checkout_button_purchase_label', array(
			$this,
			'filter_modify_checkout_purchase_button_label'
		) );

		if ( $anything_recurring && $anything_standard ) {
			add_filter( 'edd_checkout_button_purchase', array( $this, 'filter_modify_checkout_button_mixed_items' ) );
		} else if ( $anything_recurring ) {
			add_filter( 'edd_checkout_button_purchase', array(
				$this,
				'filter_modify_checkout_button_recurring_items_only'
			) );
		}
	}

	/**
	 * @return string
	 */
	public function filter_modify_checkout_purchase_button_label() {
		return __( 'Purchase and pay with Tpay', 'edd-tpay' );
	}

	/**
	 * @param string $button_html
	 *
	 * @return string
	 */
	public function filter_modify_checkout_button_mixed_items( $button_html ) {
		$color                      = edd_get_option( 'checkout_color', 'blue' );
		$color                      = ( $color == 'inherit' ) ? '' : $color;
		$style                      = edd_get_option( 'button_style', 'button' );
		$pay_for_recurring_label    = __( 'Purchase and pay with credit card for recurring items', 'edd-tpay' );
		$pay_for_recurring_info     = __( 'You will be able to complete purchase for other items using a different payment method', 'edd-tpay' );
		$pay_for_all_label          = __( 'Purchase and pay with credit card for all items', 'edd-tpay' );
		$pay_for_all_label_standard = static::get_standard_payment_label();
		$new_button_html            = '<p>' .
		                              '<button type="submit" name="edd-purchase" value="edd_tpay_pay_for_recurring_items_only" class="edd-submit ' . $color . ' ' . $style . ' edd-tpay-submit-button" id="edd-tpay-button-pay-for-recurring">' . $pay_for_recurring_label . '</button>' .
		                              '<span class="edd-description">' . $pay_for_recurring_info . '</span>' .
		                              '</p>';
		$new_button_html            .= '<p>' .
		                               '<button type="submit" name="edd-purchase" value="edd_tpay_pay_for_all_items" class="edd-submit ' . $color . ' ' . $style . ' edd-tpay-submit-button" id="edd-tpay-button-pay-for-all">' . $pay_for_all_label . '</button>' .
		                               '<span class="edd-description">' . static::get_cards_payment_description() . '</span>' .
		                               '</p>';
		if ( $this->allow_noncard_payments ) {
			$new_button_html .= '<p>' .
			                    '<button type="submit" name="edd-purchase" value="edd_tpay_pay_for_all_items_standard" class="edd-submit ' . $color . ' ' . $style . ' edd-tpay-submit-button" id="edd-tpay-button-pay-for-all-standard">' . $pay_for_all_label_standard . '</button>' .
			                    '<span class="edd-description">' . static::get_standard_payment_description() . '</span>' .
			                    '</p>';
		}
		$new_button_html .= '<input type="hidden" name="edd-purchase" id="bpmj-eddtpay-submit-type" />';
		$new_button_html .= '<div style="display: none;">' . $button_html . '</div>';

		return $new_button_html;
	}

	/**
	 * @param string $button_html
	 *
	 * @return string
	 */
	public function filter_modify_checkout_button_recurring_items_only( $button_html ) {
		$color             = edd_get_option( 'checkout_color', 'blue' );
		$color             = ( $color == 'inherit' ) ? '' : $color;
		$style             = edd_get_option( 'button_style', 'button' );
		$pay_for_all_label = Translator_Static_Helper::translate('tpay.checkout_button.text');
		$new_button_html   = '<p>' .
		                     '<button type="submit" name="edd-purchase" value="edd_tpay_pay_for_all_items" class="edd-submit ' . $color . ' ' . $style . ' edd-tpay-submit-button" id="edd-tpay-button-pay-for-all">' . $pay_for_all_label . '</button>' .
		                     '<span class="edd-description">' . static::get_cards_payment_description() . '</span>' .
		                     '</p>';
		if ( $this->allow_noncard_payments ) {
			$new_button_html .= '<p>' .
			                    '<button type="submit" name="edd-purchase" value="edd_tpay_pay_for_all_items_standard" class="edd-submit ' . $color . ' ' . $style . ' edd-tpay-submit-button" id="edd-tpay-button-pay-for-all-standard">' . static::get_standard_payment_label() . '</button>' .
			                    '<span class="edd-description">' . static::get_standard_payment_description() . '</span>' .
			                    '</p>';
		}
		$new_button_html .= '<input type="hidden" name="edd-purchase" id="bpmj-eddtpay-submit-type" />';
		$new_button_html .= '<div style="display: none;">' . $button_html . '</div>';

		return $new_button_html;
	}

	/**
	 * Creates payment's next occurrence
	 *
	 * @param int $parent_payment_id
	 * @param string $payment_date
	 * @param array $cart_details
	 */
	protected function create_future_payment( $parent_payment_id, $payment_date, $cart_details ) {
		$payment = new \EDD_Payment( $parent_payment_id );
		$price   = '00.00';

		foreach ( $cart_details as $item ) {
			$price = bcadd( $price, $item[ 'item_price' ], 2 );
		}

		$sequence_number = $payment->get_meta( '_tpay_recurrent_sequence_number' );
		$sequence_number = $sequence_number ? $sequence_number + 1 : 1;
		$payment_data    = array(
			'price'        => $price,
			'post_date'    => $payment_date,
			'purchase_key' => EddExtensions::instance()->generate_purchase_key( $payment->email ),
			'currency'     => $payment->currency,
			'cart_details' => $cart_details,
			'user_info'    => array(
				'id'         => $payment->user_id,
				'first_name' => $payment->first_name,
				'last_name'  => $payment->last_name,
				'email'      => $payment->email,
				'discount'   => $payment->discounts,
			),
			'status'       => 'pending',
			'gateway'      => BPMJ_TRA_EDD_GATEWAY_ID,
		);
		if ( ! apply_filters( 'bpmj_eddtpay_should_create_next_payment', true, $parent_payment_id, $payment_data, $sequence_number ) ) {
			return;
		}
		$future_payment_id = edd_insert_payment( $payment_data );
		$first_payment_id  = get_post_meta( $parent_payment_id, '_tpay_recurrent_first_payment_id', true );
		if ( ! $first_payment_id ) {
			$first_payment_id = $parent_payment_id;
		}
		update_post_meta( $future_payment_id, '_tpay_setup_recurrence', '1' );
		update_post_meta( $future_payment_id, '_tpay_recurrent_first_payment_id', $first_payment_id );
		update_post_meta( $future_payment_id, '_tpay_recurrent_previous_payment_id', $parent_payment_id );
		if ( $payment->get_meta( '_tpay_cli_auth' ) ) {
			update_post_meta( $future_payment_id, '_tpay_cli_auth', $payment->get_meta( '_tpay_cli_auth' ) );
		}
		update_post_meta( $future_payment_id, '_tpay_payment_subtype', 'recurrent' );
		update_post_meta( $future_payment_id, '_tpay_recurrent_next_try', date( 'Y-m-d H:i:s', strtotime( '+10 minutes' ) ) );
		update_post_meta( $future_payment_id, '_tpay_recurrent_sequence_number', $sequence_number );
		$discount_details = get_post_meta( $parent_payment_id, '_tpay_discount_details', true );
		if ( ! empty( $discount_details ) ) {
			update_post_meta( $future_payment_id, '_tpay_discount_details', $discount_details );
		}
		$future_payment = new \EDD_Payment( $future_payment_id );
		$future_payment_meta = $old_future_payment_meta = $future_payment->get_meta();

		/*
		 * Copy all meta over to the new payment
		 */
		foreach ( $payment->get_meta() as $meta_key => $meta_value ) {
			switch ( $meta_key ) {
				case 'date':
					$meta_value = $payment_date;
					break;
			}
			if ( in_array( $meta_key, ['key', 'downloads', 'cart_details'] ) ) {
				continue;
			}
			$future_payment_meta[ $meta_key ] = $meta_value;
		}
		$future_payment->update_meta( '_edd_payment_meta', $future_payment_meta, $old_future_payment_meta );
		EddExtensions::instance()->correct_discounts( $future_payment_id );
		edd_insert_payment_note( $parent_payment_id, sprintf( __( 'Next occurrence payment created (%1$s). Payment date: %2$s', 'edd-tpay' ), '#' . $future_payment_id, $payment_date ) );
	}

	/**
	 * @param int $payment_id
	 *
	 * @return bool
	 */
	protected function process_recurrent_payment( $payment_id ) {
		EddExtensions::instance()->correct_discounts( $payment_id );
		$payment  = new \EDD_Payment( $payment_id );
		$cli_auth = $payment->get_meta( '_tpay_cli_auth' );
		if ( ! $cli_auth ) {
			return false;
		}

		try {
			$presale_data = array(
				'cli_auth' => $cli_auth,
				'desc'     => $this->get_transaction_description( $payment_id ),
				'amount'   => number_format( $payment->total, 2, '.', '' ),
				'currency' => $this->get_tpay_currency_code( $payment->currency ),
				'order_id' => $payment_id . '-' . preg_replace( '#^https?://#', '', get_option( 'siteurl' ) ),
				'language' => substr( get_user_locale(), 0, 2 ),
			);

			$presale_data[ 'sign' ] = hash(self::HASH_TYPE, 'presale&' . implode( '&', $presale_data ) . '&' . $this->verification_code );
			$presale_response       = $this->request_tpay_api( 'presale', $presale_data );

			$sale_auth = isset( $presale_response[ 'sale_auth' ] ) ? $presale_response[ 'sale_auth' ] : '';
			if ( empty( $sale_auth ) ) {
				throw new \Exception( __( 'No sale_auth in the response', 'edd-tpay' ), 4 );
			}

			$sale_data = array(
				'cli_auth'  => $cli_auth,
				'sale_auth' => $sale_auth,
			);

			$sale_data[ 'sign' ] = hash(self::HASH_TYPE, 'sale&' . implode( '&', $sale_data ) . '&' . $this->verification_code );
			$sale_response       = $this->request_tpay_api( 'sale', $sale_data );
			if ( in_array( $sale_response[ 'status' ], array( 'correct', 'done' ) ) ) {
				update_post_meta( $payment_id, '_tpay_sale_auth', $sale_auth );
				if ( ! empty( $sale_response[ 'cli_auth' ] ) ) {
					update_post_meta( $payment_id, '_tpay_cli_auth', $sale_response[ 'cli_auth' ] );
				}
				if ( '1' === get_post_meta( $payment_id, '_tpay_setup_recurrence', true ) ) {
					delete_post_meta( $payment_id, '_tpay_setup_recurrence' );
					$this->split_and_create_future_payments( $payment_id );
				}
				edd_insert_payment_note( $payment_id, __( 'Tpay transaction completed successfully', 'edd-tpay' ) );
				edd_update_payment_status( $payment_id, 'completed' );

				return true;
			} else {
				$this->set_transaction_failure( $payment, sprintf( __( 'Tpay transaction failed: %s.', 'edd-tpay' ), $sale_response[ 'reason' ] ) );
                do_action( 'bpmj_tra_recurrence_transaction_failure', $payment_id, $sale_response[ 'reason' ] );
			}
		} catch ( \Exception $e ) {
			$this->set_transaction_failure( $payment, sprintf( __( 'Could not create Tpay recurrent order (exception): %s', 'edd-tpay' ), $e->getMessage() ), $e );
            do_action( 'bpmj_tra_recurrence_transaction_failure', $payment_id, $e->getMessage() );
		}

		return false;
	}

	/**
	 * @param int $payment_id
	 */
	public function hook_recurrence_create_future_payments( $payment_id ) {
		$this->split_and_create_future_payments( $payment_id );
	}

	/**
	 * Splits products by next payment date and creates separate payments for each group
	 *
	 * @param int $parent_payment_id
	 */
	protected function split_and_create_future_payments( $parent_payment_id ) {
		$items_by_payment_date = array();
		$payment               = new \EDD_Payment( $parent_payment_id );
		$reference_date        = $payment->completed_date ? $payment->completed_date : $payment->date;

		foreach ( $payment->cart_details as $item ) {
			$download_id = $item[ 'id' ];
			$price_id    = empty( $item[ 'item_number' ][ 'options' ][ 'price_id' ] ) ? null : $item[ 'item_number' ][ 'options' ][ 'price_id' ];
			if ( edd_recurring_payments_enabled_for_download( $download_id, $price_id ) ) {
				$next_payment_date = edd_recurring_get_next_payment_date( $download_id, $price_id, $reference_date );
				if ( false === $next_payment_date ) {
					continue;
				}
				if ( empty( $items_by_payment_date[ $next_payment_date ] ) ) {
					$items_by_payment_date[ $next_payment_date ] = array();
				}
				$items_by_payment_date[ $next_payment_date ][] = $item;
			}
		}

		// We create one payment for each payment date
		foreach ( $items_by_payment_date as $payment_date => $cart_details ) {
			$this->create_future_payment( $parent_payment_id, $payment_date, $cart_details );
		}
	}

	/**
	 * Scan for recurrent payments to charge their card tokens
	 */
	public function cron_process_recurrent_payments() {
		$last_cron_date = get_transient( 'bpmj_eddtpay_cron_date' );
		$today          = date( 'Y-m-d' );
		if ( $last_cron_date === $today ) {
			// If the cron method fired at least once today we reuse the chunk length set before
			$payment_chunk_length = (int) get_transient( 'bpmj_eddtpay_cron_chunk' );
			if ( ! $payment_chunk_length ) {
				$payment_chunk_length = - 1;
			}
		} else {
			// Otherwise we need to fetch all payments and calculate chunk length for today
			$payment_chunk_length = - 1;
			set_transient( 'bpmj_eddtpay_cron_date', $today );
		}
		/** @var \WP_Post[] $payments */
		$payments = edd_get_payments( array(
			'date_query' => array(
				array(
					'after'     => '-1 months',
					'before'    => 'now',
					'inclusive' => true,
				),
			),
			'status'     => 'pending',
			'meta_query' => array(
				'_tpay_payment_subtype'    => array(
					'key'   => '_tpay_payment_subtype',
					'value' => 'recurrent',
				),
				'_tpay_recurrent_next_try' => array(
					'key'     => '_tpay_recurrent_next_try',
					'value'   => date( 'Y-m-d H:i:s' ),
					'compare' => '<',
				),
				'_tpay_cli_auth'           => array(
					'key'     => '_tpay_cli_auth',
					'compare' => 'EXISTS',
				),
				'_tpay_charging_blocked'   => array(
					'key'     => '_tpay_charging_blocked',
					'compare' => 'NOT EXISTS',
				),
			),
			'meta_key'   => '_tpay_payment_subtype',
			'meta_value' => 'recurrent',
			'number'     => $payment_chunk_length,
			'order'      => 'ASC',
			'orderby'    => '_tpay_recurrent_next_try',
		) );

		if ( - 1 === $payment_chunk_length ) {
			// Calculate chunk length for today
			// We want to perform only 3% of all payments scheduled for the day at once (but at least 10)
			$count                = count( $payments );
			$payment_chunk_length = max( 10, ceil( $count / 33 ) );
			set_transient( 'bpmj_eddtpay_cron_chunk', $payment_chunk_length );
			$payments = array_slice( $payments, 0, $payment_chunk_length );
		}

		foreach ( $payments as $payment ) {
			/** @var \WP_Post $payment */
			$this->process_recurrent_payment( $payment->ID );
		}
	}

	/**
	 * @return string
	 */
	protected function get_recurring_items_total_amount() {
		$total = '0.00';
		foreach ( $this->get_cart_items_recurring_payment() as $cart_item ) {
			$total = bcadd( $total, $cart_item[ 'price' ], 2 );
		}

		return $total;
	}

	/**
	 * @return array
	 */
	public function get_cart_items_recurring_payment() {
		$this->setup_cart_items();

		return $this->cart_items_recurring_payment;
	}

	/**
	 * @return array
	 */
	public function get_cart_items_standard_payment() {
		$this->setup_cart_items();

		return $this->cart_items_standard_payment;
	}

	/**
	 * Populates @see $cart_items_standard_payment and @see $cart_cart_items_recurring_payment
	 */
	protected function setup_cart_items() {
		if ( $this->cart_items_set_up ) {
			return;
		}
		$this->cart_items_set_up = true;
		$cart_details            = edd_get_cart_content_details();

		if ( is_array( $cart_details ) && edd_recurring_payments_enabled() ) {
			foreach ( $cart_details as $cart_index => $download_cart_info ) {
				$download_id                        = $download_cart_info[ 'id' ];
				$price_id                           = empty( $download_cart_info[ 'item_number' ][ 'options' ] ) || empty( $download_cart_info[ 'item_number' ][ 'options' ][ 'price_id' ] ) ? null : $download_cart_info[ 'item_number' ][ 'options' ][ 'price_id' ];
				$download_cart_info[ 'cart_index' ] = $cart_index;
				if ( edd_recurring_payments_enabled_for_download( $download_id, $price_id ) ) {
					$this->cart_items_recurring_payment[] = $download_cart_info;
				} else {
					$this->cart_items_standard_payment[] = $download_cart_info;
				}
			}
		} else {
			$this->cart_items_standard_payment = $cart_details;
		}

		$this->cart_discount_details = EddExtensions::instance()->get_cart_discount_details();
	}

	/**
	 * Remove only recurring items from cart
	 */
	protected function remove_recurring_items_from_cart() {
		foreach ( $this->get_cart_items_recurring_payment() as $cart_item ) {
			edd_remove_from_cart( $cart_item[ 'cart_index' ] );
		}
	}

	/**
	 * Display additional status description for Tpay recurrent payments
	 *
	 * @param mixed $value
	 * @param int $payment_id
	 * @param string $column_name
	 *
	 * @return string
	 */
	public function filter_modify_payments_table_column( $value, $payment_id, $column_name ) {
		if ( 'status' === $column_name && 'recurrent' === get_post_meta( $payment_id, '_tpay_payment_subtype', true ) ) {
			return $value . ' (' . __( 'Tpay recurrent payment', 'edd-tpay' ) . ')';
		}

		return $value;
	}

	/**
	 * This function helps emulate discounts that the user received for codes entered during checkout. It is meaningful
	 * during two-step checkout (1st payment: recurring items, 2nd payment: standard items) when the first payment
	 * could deactivate user's discount codes and it would be unfair to leave the user without discounts for the second
	 * payment
	 */
	protected function create_discount_for_standard_items() {
		$standard_items_discount = '0.00';
		foreach ( $this->get_cart_items_standard_payment() as $cart_item ) {
			$standard_items_discount = bcadd( $standard_items_discount, $cart_item[ 'discount' ], 2 );
		}
		edd_unset_all_cart_discounts();
		if ( $standard_items_discount !== '0.00' ) {
			$edd_auto_discount_code = 'AUTODSCNT' . strtoupper( substr( sha1( microtime() ), 0, 8 ) );

			$discount_data = array(
				'name'              => 'EDD Autodiscount',
				'code'              => $edd_auto_discount_code,
				'type'              => 'flat',
				'amount'            => $standard_items_discount,
				'products'          => array(),
				'product_condition' => 'all',
				'not_global'        => '0',
				'start'             => '',
				'expiration'        => '',
				'min_price'         => '',
				'max'               => '',
			);
			$discount_id   = edd_store_discount( $discount_data );
			if ( false !== $discount_id ) {
				edd_set_cart_discount( $edd_auto_discount_code );
				update_post_meta( $discount_id, '_edd_auto_discount', '1' );
			}
		}
	}

	/**
	 * This is used on checkout page when payment for recurring items has been processed
	 *
	 * @param string $purchase_key
	 */
	protected function check_recurrent_payment_status( $purchase_key ) {
		$payment_id = edd_get_purchase_id_by_key( $purchase_key );
		if ( ! $payment_id ) {
			return;
		}

		$this->set_message( 'info', sprintf( __( 'Recurrent payments set up successfully. Payment status: %s. All recurring items have been removed from the cart.', 'edd-tpay' ), '<strong>' . edd_get_payment_status( $payment_id, true ) . '</strong>' ) );
	}

	/**
	 * Display recurrent payment column for payments table
	 */
	public function hook_purchase_history_header() {
		echo '<th></th>';
	}

	/**
	 * Display recurrent payment column for payments table
	 *
	 * @param int $payment_id
	 */
	public function hook_purchase_history_row( $payment_id ) {
		?>
        <td>
			<?php
			if ( 'recurrent' === get_post_meta( $payment_id, '_tpay_payment_subtype', true ) && 'pending' === get_post_status( $payment_id ) ):
//				_e( 'Scheduled recurrent payment', 'edd-tpay' );
				?>
                <a href="<?php echo esc_url( add_query_arg( 'payment_key', edd_get_payment_key( $payment_id ), edd_get_success_page_uri() ) ); ?>"><?php _e( 'Subscription details', 'edd-tpay' ); ?></a>
			<?php
			endif;
			?>
        </td>
		<?php
	}

	/**
	 * Display options for recurrent payment at the receipt page
	 *
	 * @param \WP_Post $payment
	 */
	public function hook_payment_receipt_options( \WP_Post $payment ) {
		$payment_id = $payment->ID;

		if ( 'recurrent' === get_post_meta( $payment_id, '_tpay_payment_subtype', true ) && 'pending' === get_post_status( $payment_id ) ):
			$automatic = (bool) get_post_meta( $payment_id, '_tpay_cli_auth' );
			if ( ! $automatic ):
				?>
                <tr>
                    <td><strong><?php _e( 'Options', 'edd-tpay' ); ?></strong></td>
                    <td>
                        <form action="" method="post">
							<?php wp_nonce_field( 'bpmj-eddtpay-pay-for-subscription-nonce-' . $payment_id, 'bpmj_eddtpay_pay_for_subscription_nonce' ); ?>
                            <input type="hidden" name="payment_id" value="<?php echo $payment_id; ?>"/>
                            <input class="edd-submit blue button" type="submit"
                                   name="bpmj_eddtpay_pay_for_subscription"
                                   value="<?php _e( 'Pay with Tpay', 'edd-tpay' ); ?>"/>
                        </form>
                    </td>
                </tr>
			<?php endif; ?>
            <tr>
                <td><?php if ( $automatic ): ?><strong><?php _e( 'Options', 'edd-tpay' ); ?>
                        :</strong><?php else: ?>&nbsp;<?php endif; ?></td>
                <td>
                    <form id="bpmj-eddtpay-cancel-subscription-form" action="" method="post">
						<?php wp_nonce_field( 'bpmj-eddtpay-cancel-subscription-nonce-' . $payment_id, 'bpmj_eddtpay_cancel_subscription_nonce' ); ?>
                        <input type="hidden" name="payment_id" value="<?php echo $payment_id; ?>"/>
                        <input class="edd-submit red button" type="submit"
                               name="bpmj_eddtpay_cancel_subscription"
                               value="<?php _e( 'Cancel subscription', 'edd-tpay' ); ?>"/>
                    </form>
                </td>
            </tr>
		<?php
		endif;
	}

	/**
	 * Performed when the user chose to cancel his subscription
	 */
	protected function process_cancel_subscription() {
		$payment_id   = filter_input( INPUT_POST, 'payment_id', FILTER_VALIDATE_INT );
		$nonce        = filter_input( INPUT_POST, 'bpmj_eddtpay_cancel_subscription_nonce' );
		$nonce_action = 'bpmj-eddtpay-cancel-subscription-nonce-' . $payment_id;
		if ( wp_verify_nonce( $nonce, $nonce_action ) ) {
			edd_update_payment_status( $payment_id, 'abandoned' );
		}
		wp_redirect( wp_get_referer() );
	}

	/**
	 * @param int $payment_id
	 * @param string $new_status
	 * @param string $old_status
	 */
	public function hook_update_payment_status( $payment_id, $new_status, $old_status ) {
	    if($this->process_manual_completion($payment_id, $new_status, $old_status)) {
	        return;
	    }

	    $this->process_abandonment( $payment_id, $new_status, $old_status );
	}

	private function process_abandonment( $payment_id, $new_status, $old_status ) {
	    if ( $new_status === $old_status || 'abandoned' !== $new_status ) {
	        return;
	    }
	    
	    $cli_auth = get_post_meta( $payment_id, '_tpay_cli_auth', true );
	    if ( ! $cli_auth ) {
	        return;
	    }
	    
	    $deregister_data = array(
	        'cli_auth' => $cli_auth,
	    );
	    
	    $deregister_data[ 'sign' ] = hash(self::HASH_TYPE, 'deregister&' . implode( '&', $deregister_data ) . '&' . $this->verification_code );
	    try {
	        $deregister_response = $this->request_tpay_api( 'deregister', $deregister_data, true );
	        edd_insert_payment_note( $payment_id, sprintf( __( 'Tpay sale deregistered (result: %s)', 'bpmjd_tra_edd' ), empty( $deregister_response[ 'result' ] ) ? 'none' : $deregister_response[ 'result' ] ) );
	        delete_post_meta( $payment_id, '_tpay_cli_auth' );
	    } catch ( \Exception $e ) {
	    }
	}

	private function process_manual_completion( $payment_id, $new_status, $old_status ) {
	    if ( 'publish' === $new_status && '1' === get_post_meta( $payment_id, '_tpay_setup_recurrence', true ) ) {
	        delete_post_meta( $payment_id, '_tpay_setup_recurrence' );
	        $this->split_and_create_future_payments( $payment_id );
	        return true;
	    }
	    return false;
	}

	/**
	 * Performed when the user chose to cancel his subscription
	 */
	protected function process_pay_for_subscription() {
		$payment_id   = filter_input( INPUT_POST, 'payment_id', FILTER_VALIDATE_INT );
		$nonce        = filter_input( INPUT_POST, 'bpmj_eddtpay_pay_for_subscription_nonce' );
		$nonce_action = 'bpmj-eddtpay-pay-for-subscription-nonce-' . $payment_id;
		$redirect     = wp_get_referer();
		if ( wp_verify_nonce( $nonce, $nonce_action ) ) {
			$redirect = $this->get_direct_gateway_url( $payment_id );
		}
		wp_redirect( $redirect );
	}

	/**
	 * @param string $url
	 * @param int $payment_id
	 *
	 * @return mixed|string
	 */
	public function filter_get_direct_gateway_url( $url, $payment_id ) {
		if ( 'recurrent' !== get_post_meta( $payment_id, '_tpay_payment_subtype', true ) || ! ! get_post_meta( $payment_id, '_tpay_cli_auth', true ) ) {
			// If the payment is not tpay recurrent or it has non-empty cli_auth then bail - we only allow tpay standard payments
			return $url;
		}

		return $this->get_direct_gateway_url( $payment_id );
	}

	/**
	 * @param int $payment_id
	 *
	 * @return mixed|string
	 */
	protected function get_direct_gateway_url( $payment_id ) {
		$payment      = new \EDD_Payment( $payment_id );
		$payment_data = array(
			'price'        => $payment->total,
			'date'         => $payment->date,
			'user_email'   => $payment->email,
			'purchase_key' => $payment->key,
			'currency'     => $payment->currency,
			'downloads'    => $payment->downloads,
			'user_info'    => array(
				'id'         => $payment->user_id,
				'email'      => $payment->email,
				'first_name' => $payment->first_name,
				'last_name'  => $payment->last_name,
				'discount'   => $payment->discounts,
				'address'    => $payment->address,
			),
			'cart_details' => $payment->cart_details,
			'status'       => $payment->status,
			'fees'         => $payment->fees,
		);
		edd_set_purchase_session( $payment_data );

		return $this->init_standard_payment( $payment_id, $payment_data );
	}

	/**
	 * Schedules (or reschedules if necessary) recurrent payments cron
	 */
	protected function schedule_recurrent_payments() {
		$hook       = 'bpmj_eddtpay_recurrent_payments';
		$recurrence = 'bpmj_eddtpay_once_every_10_min';
		$schedule   = wp_get_schedule( $hook );
		if ( false !== $schedule && $recurrence !== $schedule ) {
			// If the hook has obsolete interval we need to clear it and let it be reset below
			wp_clear_scheduled_hook( $hook );
		}
		if ( ! wp_next_scheduled( $hook ) ) {
			wp_schedule_event( time(), $recurrence, $hook );
		}
	}

	/**
	 *
	 */
	protected function schedule_failure_notifications() {
		$hook       = 'bpmj_eddtpay_failure_notifications';
		$recurrence = 'bpmj_eddtpay_once_every_10_min';
		$schedule   = wp_get_schedule( $hook );
		if ( false !== $schedule && $recurrence !== $schedule ) {
			// If the hook has obsolete interval we need to clear it and let it be reset below
			wp_clear_scheduled_hook( $hook );
		}
		if ( ! wp_next_scheduled( $hook ) ) {
			wp_schedule_event( time(), $recurrence, $hook );
		}
	}

	/**
	 * @return array
	 */
	protected function get_cart_discount_details() {
		$this->setup_cart_items();

		return $this->cart_discount_details;
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
			edd_record_gateway_error( __( 'Error on inserting a payment record', 'edd-tpay' ), sprintf( __( 'Payment data: %s', 'edd-tpay' ), json_encode( $payment_data ) ) );
			edd_send_back_to_checkout( '?payment-mode=' . $purchase_data[ 'post_data' ][ 'edd-gateway' ] );

			return false;
		}

		return $payment_id;
	}

	/**
	 * Frontend scripts
	 */
	public function frontend_scripts() {
		wp_enqueue_script( 'bpmj_eddtpay_front_scripts', BPMJ_TRA_EDD_URL . 'assets/js/edd-tpay.min.js', array( 'jquery' ), BPMJ_TRA_EDD_VERSION );
	}

	/**
	 * @param int $payment_id
	 *
	 * @return string
	 */
	protected function get_transaction_description( $payment_id ) {
		return get_bloginfo( 'name' ) . ' ' . sprintf( __( 'Payment no #%s', 'edd-tpay' ), $payment_id );
	}

	/**
	 * @param array $purchase_data
	 *
	 * @return int
	 */
	protected function get_total_amount( $purchase_data ) {
		return number_format( $purchase_data[ 'price' ], 2, '.', '' );
	}

	/**
	 *
	 */
	public function hook_setup_tpay_checkout() {
		if ( isset( $_REQUEST[ 'payment-mode' ] ) && BPMJ_TRA_EDD_GATEWAY_ID === $_REQUEST[ 'payment-mode' ]
		     || isset( $_REQUEST[ 'edd_payment_mode' ] ) && BPMJ_TRA_EDD_GATEWAY_ID === $_REQUEST[ 'edd_payment_mode' ]
		     /**
		      * We cannot use @see edd_is_checkout because it's to early for that
		      * - we have to check it with a more primitive solution
		      */
		     || ! defined( 'DOING_AJAX' ) && parse_url( edd_get_checkout_uri(), PHP_URL_PATH ) === parse_url( $_SERVER[ 'REQUEST_URI' ], PHP_URL_PATH ) && ! edd_show_gateways()
		) {
			$this->setup_edd_tpay_purchase_form();
		}
	}

	/**
	 * Prints various notification messages
	 */
	public function hook_print_tpay_checkout_messages() {
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

	/**
	 * @param string $currency
	 *
	 * @return int
	 */
	public function get_tpay_currency_code( $currency ) {
		$currency_map = array(
			'PLN' => 985,
			'GBP' => 826,
			'USD' => 840,
			'EUR' => 978,
			'CZK' => 203,
			'NOK' => 578,
			'DKK' => 208,
			'SEK' => 752,
			'CHF' => 756,
		);

		return $currency_map[ $currency ];
	}

	/**
	 * @param string $method
	 * @param array $params
	 * @param bool $skip_sign_verification
	 *
	 * @return array
	 * @throws \Exception
	 */
	protected function request_tpay_api( $method, $params, $skip_sign_verification = false ) {
		$params[ 'api_password' ] = $this->api_password;
		$api_endpoint             = 'https://secure.tpay.com/api/cards/' . $this->api_key . '/' . $method;

		$response = wp_remote_post( $api_endpoint, array( 'body' => json_encode( $params ), 'timeout' => 15 ) );

		if ( is_wp_error( $response ) ) {
			throw new \Exception( sprintf( __( 'Bad HTTP request (%s)', 'edd-tpay' ), $response->get_error_message() ), static::ERROR_BAD_REQUEST );
		}
		$data = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( ! is_array( $data ) ) {
			throw new \Exception( __( 'Bad HTTP response body', 'edd-tpay' ), static::ERROR_BAD_HTTP_RESPONSE_BODY );
		}
		if ( ! empty( $data[ 'err_code' ] ) && ! empty( $data[ 'err_desc' ] ) ) {
			throw new \Exception( sprintf( __( 'Tpay error (err_code: %1$s, err_desc: %2$s)', 'edd-tpay' ), $data[ 'err_code' ], $data[ 'err_desc' ] ), static::ERROR_TPAY_ERROR );
		}
		if ( $skip_sign_verification ) {
			return $data;
		}

		$verified_data = $this->retrieve_and_check_cards_response( $data );
		if ( false === $verified_data ) {
			throw new \Exception( __( 'Invalid sign in cards response', 'edd-tpay' ), static::ERROR_WRONG_SIGN );
		}

		return $verified_data;
	}

	/**
	 * @return array|bool
	 */
	protected function retrieve_and_check_cards_response( $input = null ) {
		if ( ! $input ) {
			$input = $_POST;
		}
		$type      = empty( $input[ 'type' ] ) ? '' : $input[ 'type' ];
		$test_mode = empty( $input[ 'test_mode' ] ) ? '' : $input[ 'test_mode' ];
		$sale_auth = empty( $input[ 'sale_auth' ] ) ? '' : $input[ 'sale_auth' ];
		$order_id  = empty( $input[ 'order_id' ] ) ? '' : $input[ 'order_id' ];
		$cli_auth  = empty( $input[ 'cli_auth' ] ) ? '' : $input[ 'cli_auth' ];
		$card      = empty( $input[ 'card' ] ) ? '' : $input[ 'card' ];
		$currency  = empty( $input[ 'currency' ] ) ? '' : $input[ 'currency' ];
		$amount    = empty( $input[ 'amount' ] ) ? '' : $input[ 'amount' ];
		$date      = empty( $input[ 'date' ] ) ? '' : $input[ 'date' ];
		$status    = empty( $input[ 'status' ] ) ? '' : $input[ 'status' ];
		$reason    = empty( $input[ 'reason' ] ) ? '' : $input[ 'reason' ];

		if ( empty( $input[ 'sign' ] ) ) {
			return false;
		}

		if ( $input[ 'sign' ] !== hash(self::HASH_TYPE, $type . $test_mode . $sale_auth . $order_id . $cli_auth . $card . $currency . $amount . $date . $status . $reason . $this->verification_code ) ) {
			return false;
		}

		return array(
			'type'      => $type,
			'test_mode' => $test_mode,
			'sale_auth' => $sale_auth,
			'order_id'  => $order_id,
			'cli_auth'  => $cli_auth,
			'card'      => $card,
			'currency'  => $currency,
			'amount'    => $amount,
			'date'      => $date,
			'status'    => $status,
			'reason'    => $reason,
		);
	}

	/**
	 * @param \EDD_Payment $payment
	 */
	protected function set_recurrent_next_try_time( $payment ) {
		$frequency = $payment->get_meta( '_tpay_recurrent_next_try_frequency' );
		if ( ! $frequency ) {
			$frequency = 1;
		}

		$payment->update_meta( '_tpay_recurrent_next_try', date( 'Y-m-d H:i:s', strtotime( '+' . ( $frequency * 10 ) . ' minutes' ) ) );
		if ( $frequency < 100 ) {
			/*
			 * Each time this function is called it doubles the amount of time between each try: 10 minutes, 20 minutes,
			 * 40 minutes and so on (up to maximum 1280 minutes)
			 */
			$frequency *= 2;
		}
		$payment->update_meta( '_tpay_recurrent_next_try_frequency', $frequency );
	}

	/**
	 * @param \EDD_Payment $payment
	 * @param string $message
	 * @param \Exception|null $e
	 */
	protected function set_transaction_failure( $payment, $message, \Exception $e = null ) {
		edd_insert_payment_note( $payment->ID, $message );
		$this->set_recurrent_next_try_time( $payment );
		$notification = array(
			'payment_id' => $payment->ID,
			'date'       => current_time( 'mysql' ),
			'message'    => $message,
		);
		if ( $e instanceof \Exception ) {
            $error_code = $e->getCode();
            
			if ( static::ERROR_BAD_REQUEST === $error_code || static::ERROR_WRONG_SIGN === $error_code ) {
				/*
				 * If it's a bad request exception then we have sent a request to charge a credit card but don't know
				 * the outcome - we have to be super sure the card won't get charged again
				 */
				$notification[ 'unknown_status' ] = true;
				$payment->update_meta( '_tpay_charging_blocked', 1 );
			}
		}
		$failure_notifications   = get_option( '_tpay_failure_notifications', array() );
		$failure_notifications[] = $notification;
		update_option( '_tpay_failure_notifications', $failure_notifications, false );
	}

	/**
	 * @param $payment_id
	 */
	public function hook_view_order_details_update_inner( $payment_id ) {
		if ( 1 === (int) get_post_meta( $payment_id, '_tpay_charging_blocked', true ) ):
			?>
            <div class="edd-admin-box-inside" style="background-color: #ffa500;">
                <p>
					<?php _e( 'Status of this payment is unknown. Login to Tpay.com panel and check the status manually. If the card hasn\'t been charged, reenable charging of the credit card by selecting the checkbox below.', 'edd-tpay' ); ?>
                </p>
                <p>
                    <label class="label"
                           for="tpay-reenable-charging"><?php _e( 'Reenable charging for this payment:', 'edd-tpay' ); ?></label>
                    <input type="checkbox" id="tpay-reenable-charging" name="tpay-reenable-charging" value="1"/>
                </p>
            </div>
		<?php
		endif;
	}

	/**
	 * @param $payment_id
	 */
	public function hook_updated_edited_purchase( $payment_id ) {
		if ( ! empty( 'tpay-reenable-charging' ) ) {
			delete_post_meta( $payment_id, '_tpay_charging_blocked' );
			delete_post_meta( $payment_id, '_tpay_recurrent_next_try_frequency' );
			$next_try = get_post_meta( $payment_id, '_tpay_recurrent_next_try', true );
			if ( ! $next_try || $next_try < date( 'Y-m-d H:i:s' ) ) {
				update_post_meta( $payment_id, '_tpay_recurrent_next_try', date( 'Y-m-d H:i:s' ) );
			}
		}
	}

	/**
	 *
	 */
	public function cron_process_failure_notifications() {
		$time = date( 'H:i:s', current_time( 'timestamp' ) );
		if ( $time >= '08:00:00' && date( 'Y-m-d' ) !== get_option( '_tpay_failure_notifications_last_date' ) ) {
			/*
			 * We want this job to fire once per day, but not earlier than 8:00 AM
			 */
			update_option( '_tpay_failure_notifications_last_date', date( 'Y-m-d' ), false );
			$notifications = get_option( '_tpay_failure_notifications', array() );
			if ( ! empty( $notifications ) ) {
				delete_option( '_tpay_failure_notifications' );
				$body_parts = array();
				foreach ( $notifications as $notification ) {
					$payment = new \EDD_Payment( $notification[ 'payment_id' ] );
					$info    = array(
						sprintf( __( 'Payment: %s', 'edd-tpay' ), '#' . $payment->ID ),
						sprintf( __( 'Date: %s', 'edd-tpay' ), $notification[ 'date' ] ),
						sprintf( __( 'Reason: %s', 'edd-tpay' ), $notification[ 'message' ] ),
					);
					if ( ! empty( $notification[ 'unknown_status' ] ) ) {
						$info[] = '*' . __( 'Warning: The status of this payment is unknown. To prevent charging the credit card for second time further processing of this payment has been blocked. You can unblock the payment in control panel.', 'edd-tpay' ) . '*';
					}
					$info[]       = add_query_arg( 'id', $payment->ID, admin_url( 'edit.php?post_type=download&page=edd-payment-history&view=view-order-details' ) );
					$body_parts[] = implode( "\r\n", $info );
				}
				$body = implode( "\r\n\r\n", $body_parts );

                $to_email = apply_filters( 'wpi_admin_notices_email', get_option( 'admin_email' ) );
				@wp_mail( $to_email, sprintf( __( 'Tpay: failed transactions (%s)', 'edd-tpay' ), count( $notifications ) ), $body );
			}
		}
	}
	
	private function hotfix_pb_457_init() {
	    add_submenu_page(
            null,
            'Dane do poprawienia',
            '',
            Caps::CAP_MANAGE_SETTINGS,
            'wp-idea-hotfix_pb_457',
            [$this, 'hotfix_pb_457_page']
        );
	}

	public function hotfix_pb_457_page() {
	    $base_url = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['SCRIPT_NAME'];

	    $posts = edd_get_payments( array(
	        'date_query' => array(
	            array(
	                'after'     => '-3 months',
	                'inclusive' => true,
	            ),
	        ),
	        'status'     => 'pending',
	        'meta_query' => array(
	            '_tpay_payment_subtype'    => array(
	                'key'   => '_tpay_payment_subtype',
	                'value' => 'recurrent',
	            ),
	        ),
	        'meta_key'   => '_tpay_payment_subtype',
	        'meta_value' => 'recurrent',
	        'number'     => 100,
	        'order'      => 'ASC',
	        'orderby'    => 'ID',
	    ) );

	    if( isset($_GET['id_to_do']) ) {
	        $status = self::hotfix_pb_457_do_correction((int) $_GET['id_to_do']);
	        if($status) echo '<h2>Naprawiono ID ' . $_GET['id_to_do'] . '</h2>';
	    }

	    foreach ($posts as $post) {
	        $payment = new \EDD_Payment( $post->ID );
	        $payment_meta = $payment->get_meta();

	        if( !empty($payment_meta['downloads']) && !empty($payment_meta['cart_details'])) {
	            continue;
	        }
	        echo '<p>';
	        
	        echo '<b>ID ' . $post->ID . '</b></br>';
	        if( empty($payment_meta['downloads']) ) {
	            echo '&nbsp;-downloads' . '</br>';
	        }
	        if( empty($payment_meta['cart_details']) ) {
	            echo '&nbsp;-cart' . '</br>';
	        }
	        
	        $previous_id = $payment->get_meta('_tpay_recurrent_previous_payment_id');
	        if( $previous_id ) {
	            echo '&nbsp;+previous_id ' . $previous_id . '</br>';
	            $previous_payment = new \EDD_Payment( $previous_id );
	            $previous_payment_meta = $previous_payment->get_meta();

	            $correctable = false;
	            if( empty($payment_meta['downloads']) && !empty($previous_payment_meta['downloads']) ) {
	                echo '&nbsp;+downloads' . '</br>';
	                $correctable = true;
	            }
	            if( empty($payment_meta['cart_details']) && !empty($previous_payment_meta['cart_details']) ) {
	                echo '&nbsp;+cart' . '</br>';
	                $correctable = true;
	            }
	            
	            if($correctable) {
	                $url = $base_url . '?page=wp-idea-hotfix_pb_457&id_to_do=' . $post->ID;
	                echo '<button onclick="window.location.href=\'' . $url . '\'">Napraw</button>';
	            }
	        }
	        else {
	            echo '&nbsp;-previous_id' . '</br>';
	        }

	        echo '</p>';
	    }

	}

	public static function hotfix_pb_457_do_correction(int $id_to_do): bool {
	    $payment = new \EDD_Payment( $id_to_do );
	    if( !$payment ) {
	        echo 'ID ' . $id_to_do . ': nie można utworzyć obiektu EDD_Payment';
	        return false;
	    }

	    $payment_meta = $payment->get_meta();
	    if( !empty($payment_meta['downloads']) && !empty($payment_meta['cart_details'])) {
	        echo 'ID ' . $id_to_do . ': downloads i cart_details nie są puste';
	        return false;
	    }
	    
	    $previous_id = $payment->get_meta('_tpay_recurrent_previous_payment_id');
	    $previous_payment = new \EDD_Payment( $previous_id );
	    
	    $items_by_payment_date = array();
	    $reference_date = $previous_payment->date;

	    foreach ( $previous_payment->cart_details as $item ) {
	        $download_id = $item[ 'id' ];
	        $price_id    = empty( $item[ 'item_number' ][ 'options' ][ 'price_id' ] ) ? null : $item[ 'item_number' ][ 'options' ][ 'price_id' ];
	        if ( edd_recurring_payments_enabled_for_download( $download_id, $price_id ) ) {
	            $next_payment_date = edd_recurring_get_next_payment_date( $download_id, $price_id, $reference_date );
	            if ( false === $next_payment_date ) {
	                continue;
	            }
	            if ( empty( $items_by_payment_date[ $next_payment_date ] ) ) {
	                $items_by_payment_date[ $next_payment_date ] = array();
	            }
	            $items_by_payment_date[ $next_payment_date ][] = $item;
	        }
	    }

	    if( !isset($items_by_payment_date[$payment_meta['date']]) ) {
	        echo 'ID ' . $id_to_do . ': w previous brak następcy dla daty ' . $payment_meta['date'];
	        return false;
	    }
	    $cart_details = $items_by_payment_date[$payment_meta['date']];
	    
	    $payment_meta['user_info']['discount'] = $previous_payment->discounts;
	    $payment->update_meta( '_edd_payment_meta', $payment_meta );
	    $payment = new \EDD_Payment( $id_to_do );
	    
	    if( empty($payment_meta['downloads']) ) {
    	    foreach ( $cart_details as $item ) {
    	        
    	        $args = array(
    	            'quantity'   => $item['quantity'],
    	            'price_id'   => isset( $item['item_number']['options']['price_id'] ) ? $item['item_number']['options']['price_id'] : null,
    	            'tax'        => $item['tax'],
    	            'item_price' => isset( $item['item_price'] ) ? $item['item_price'] : $item['price'],
    	            'fees'       => isset( $item['fees'] ) ? $item['fees'] : array(),
    	            'discount'   => isset( $item['discount'] ) ? $item['discount'] : 0,
    	        );
    	        
    	        $options = isset( $item['item_number']['options'] ) ? $item['item_number']['options'] : array();
    	        
    	        $payment->add_download( $item['id'], $args, $options );
    	    }
    	    $payment->save();
	    }
	    
	    EddExtensions::instance()->correct_discounts( $id_to_do );

	    return true;
	}

	public static function hotfix_pb_457_correctable_list():array {
	    $posts = edd_get_payments( array(
	        'date_query' => array(
	            array(
	                'after'     => '-3 months',
	                'inclusive' => true,
	            ),
	        ),
	        'status'     => 'pending',
	        'meta_query' => array(
	            '_tpay_payment_subtype'    => array(
	                'key'   => '_tpay_payment_subtype',
	                'value' => 'recurrent',
	            ),
	        ),
	        'meta_key'   => '_tpay_payment_subtype',
	        'meta_value' => 'recurrent',
	        'number'     => 100,
	        'order'      => 'ASC',
	        'orderby'    => 'ID',
	    ) );
	    
	    $correctable_list = [];
	    foreach ($posts as $post) {
	        $payment = new \EDD_Payment( $post->ID );
	        $payment_meta = $payment->get_meta();
	        
	        if( !empty($payment_meta['downloads']) && !empty($payment_meta['cart_details'])) {
	            continue;
	        }

	        $previous_id = $payment->get_meta('_tpay_recurrent_previous_payment_id');
	        if( $previous_id ) {
	            $previous_payment = new \EDD_Payment( $previous_id );
	            $previous_payment_meta = $previous_payment->get_meta();
	            
	            $correctable = false;
	            if( empty($payment_meta['downloads']) && !empty($previous_payment_meta['downloads']) ) {
	                $correctable = true;
	            }
	            if( empty($payment_meta['cart_details']) && !empty($previous_payment_meta['cart_details']) ) {
	                $correctable = true;
	            }
	            
	            if($correctable) {
	                $correctable_list[] = $post->ID;
	            }
	        }
	    }
	    
	    return $correctable_list;
	}
	
}
