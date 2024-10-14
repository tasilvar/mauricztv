<?php

namespace bpmj\wp\eddpayu\gateway_handlers;

use bpmj\wp\eddpayu\PayuGatewayConnector;
use bpmj\wp\eddpayu\service\EddExtensions;
use bpmj\wp\eddpayu\service\PayuTransparent;
use bpmj\wpidea\helpers\Price_Formatting;

class PayuHandlerRest extends PayuHandlerAbstract {

	const DIRECTION_PAYU_TO_EDD = 'PAYU_TO_EDD';
	const DIRECTION_EDD_TO_PAYU = 'EDD_TO_PAYU';

	/**
	 * List of items for recurring payment - this array is filled during setup_edd_payu_purchase_form (on 'init')
	 * @var array
	 */
	protected $cart_items_recurring_payment = array();

	/**
	 * List of items for standard payment - this array is filled during setup_edd_payu_purchase_form (on 'init')
	 * @var array
	 */
	protected $cart_items_standard_payment = array();

	/**
	 * This variable helps determine if the order update was initialized by PayU (eg. by notification) or EDD (by
	 * manual update in backend)
	 * @var string
	 */
	protected $updating_order_direction;

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
	protected $mutex_active = false;

	public function __construct() {
		global $edd_options;
		$this->pos_id                 = edd_get_option( 'payu_pos_id' );
		$this->key2                   = edd_get_option( 'payu_key2' );
		$this->environment            = edd_get_option( 'payu_api_environment' );
		$this->allow_noncard_payments = ! empty( $edd_options[ 'payu_recurrence_allow_standard_payments' ] ) && '1' == $edd_options[ 'payu_recurrence_allow_standard_payments' ];
	}

	public function bootstrap() {
		$this->updating_order_direction = static::DIRECTION_EDD_TO_PAYU;

		parent::bootstrap();

		add_action( 'edd_gateway_payu', array( $this, 'hook_process_payment' ) );
		add_action( 'init', array( $this, 'hook_init' ) );
		add_action( 'edd_post_refund_payment', array( $this, 'hook_process_refund' ) );
		add_action( 'edd_update_payment_status', array( $this, 'hook_process_payment_status_change' ), 10, 2 );
		add_action( 'bpmj_eddpayu_recurrent_payments', array( $this, 'cron_process_recurrent_payments' ) );
		add_action( 'edd_purchase_history_header_after', array( $this, 'hook_purchase_history_header' ) );
		add_action( 'edd_purchase_history_row_end', array( $this, 'hook_purchase_history_row' ) );
		add_action( 'edd_payment_receipt_after', array( $this, 'hook_payment_receipt_options' ) );
		add_filter( 'edd_direct_gateway_url', array( $this, 'filter_get_direct_gateway_url' ), 10, 2 );
		$this->schedule_recurrent_payments();

		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ), 1 );

		if ( is_admin() ) {
			add_filter( 'edd_payments_table_column', array( $this, 'filter_modify_payments_table_column' ), 10, 3 );
		}

		if ( ! empty( $_REQUEST[ 'payu_recurrent_payment_done' ] ) ) {
			$this->check_recurrent_payment_status( $_REQUEST[ 'payu_recurrent_payment_done' ] );
		}

		if ( ! empty( $_REQUEST[ 'payu-error' ] ) ) {
			$this->set_message( 'error', __( 'PayU payment failed. Reason: unknown error.', BPMJ_EDDPAYU_DOMAIN ) );
		}
	}

	/**
	 * @param array $purchase_data
	 */
	public function hook_process_payment( $purchase_data ) {
		$pay_for_recurring_items_only = ! empty( $purchase_data[ 'post_data' ][ 'edd_payu_pay_for_recurring_items_only' ] );

		if ( $pay_for_recurring_items_only ) {
			$purchase_data[ 'price' ]        = $this->get_recurring_items_total_amount();
			$purchase_data[ 'cart_details' ] = $this->get_cart_items_recurring_payment();
		}

		$payment_id = $this->create_payment_for_purchase( $purchase_data );
		if ( false === $payment_id ) {
			return;
		}

		$buyer = array(
			'firstName' => $purchase_data[ 'user_info' ][ 'first_name' ],
			'lastName'  => $purchase_data[ 'user_info' ][ 'last_name' ],
			'email'     => $purchase_data[ 'user_info' ][ 'email' ],
		);

		$products = $this->create_payu_products_array( $purchase_data[ 'cart_details' ] );

		$currency     = edd_get_option( 'currency' );
		$continue_url = edd_get_success_page_uri() . '?payu_purchase_key=' . $purchase_data[ 'purchase_key' ];

		if ( $pay_for_recurring_items_only ) {
			$continue_url = edd_get_checkout_uri( array( 'payu_recurrent_payment_done' => edd_get_payment_key( $payment_id ) ) );
		}
		$order = array(
			'notifyUrl'     => home_url( '/' ) . '?payu_notification',
			'continueUrl'   => $continue_url,
			'customerIp'    => $_SERVER[ 'REMOTE_ADDR' ],
			'merchantPosId' => $this->pos_id,
			'description'   => $this->get_transaction_description( $payment_id ),
			'currencyCode'  => $currency ? $currency : 'PLN',
			'totalAmount'   => $this->get_total_amount( $purchase_data ),
			'extOrderId'    => $payment_id . '-' . preg_replace( '#^https?://#', '', get_option( 'siteurl' ) ) . '-' . substr( sha1( microtime() ), 0, 4 ),
			'buyer'         => $buyer,
			'products'      => $products,
			'settings'      => array( 'invoiceDisabled' => true )
		);

		if ( ! empty( $purchase_data[ 'post_data' ][ 'edd_payu_card_token' ] ) ) {
			$token                 = $purchase_data[ 'post_data' ][ 'edd_payu_card_token' ];
			$order[ 'payMethods' ] = array(
				'payMethod' => array(
					'value' => $token,
					'type'  => 'CARD_TOKEN',
				),
			);
		}
		EddExtensions::instance()->log('hook_process_payment req ' . print_r($order, true));
		
		try {
			/** @var \OpenPayU_Result $response */
			$response = \OpenPayU_Order::create( $order );
			EddExtensions::instance()->log('hook_process_payment resp ' . print_r($response, true));

			if ( in_array( $response->getStatus(), array(
				'SUCCESS',
				'WARNING_CONTINUE_3DS',
				'WARNING_CONTINUE_CVV',
			) ) ) {
				$cart_discount_details = $this->get_cart_discount_details();
				if ( $pay_for_recurring_items_only ) {
					$this->create_discount_for_standard_items();
					$this->remove_recurring_items_from_cart();
				} else {
					edd_empty_cart();
				}
				edd_insert_payment_note( $payment_id, sprintf( __( 'PayU order created. Order number: %s', BPMJ_EDDPAYU_DOMAIN ), $response->getResponse()->orderId ) );
				update_post_meta( $payment_id, '_payu_order_id', $response->getResponse()->orderId );
				if ( ! empty( $response->getResponse()->payMethods ) && $response->getResponse()->payMethods->payMethod ) {
					$payMethod = json_decode( json_encode( $response->getResponse()->payMethods->payMethod, JSON_FORCE_OBJECT ), true );
					update_post_meta( $payment_id, '_payu_pay_method', $payMethod );
					if ( ! empty( $payMethod[ 'type' ] ) && 'CARD_TOKEN' === $payMethod[ 'type' ] ) {
						update_post_meta( $payment_id, '_payu_recurrent_payment_token', $payMethod[ 'value' ] );
						update_post_meta( $payment_id, '_payu_setup_recurrence', '1' );
						update_post_meta( $payment_id, '_payu_discount_details', $cart_discount_details );
						update_post_meta( $payment_id, '_payu_customer_ip', $this->get_remote_ip() );
						if ( in_array( $response->getStatus(), array(
							'WARNING_CONTINUE_3DS',
							'WARNING_CONTINUE_CVV',
						) ) ) {
							edd_insert_payment_note( $payment_id, __( 'Redirected to 3DS/CVV verification', BPMJ_EDDPAYU_DOMAIN ) );
							EddExtensions::instance()->log('hook_process_payment 3DS/CVV redir ' . $response->getResponse()->redirectUri);
							wp_redirect( $response->getResponse()->redirectUri );
						} else {
						    EddExtensions::instance()->log('hook_process_payment redir ' . $continue_url);
							wp_redirect( $continue_url );
						}
						exit;
					}
				}
				if ( $response->getResponse()->redirectUri ) {
					if ( ! empty( $purchase_data[ 'post_data' ][ 'edd_payu_pay_for_all_items_standard' ] ) ) {
						update_post_meta( $payment_id, '_payu_setup_recurrence', '1' );
						update_post_meta( $payment_id, '_payu_discount_details', $cart_discount_details );
					}
					EddExtensions::instance()->log('hook_process_payment redir ' . $response->getResponse()->redirectUri);
					wp_redirect( $response->getResponse()->redirectUri );
				} else {
				    EddExtensions::instance()->log('hook_process_payment send_to_success_page');
					edd_send_to_success_page();
				}
				exit;
			} else {
			    EddExtensions::instance()->log('hook_process_payment error ' . print_r($response, true));
				edd_record_gateway_error( __( 'Could not create PayU order', BPMJ_EDDPAYU_DOMAIN ), sprintf( __( 'PayU OrderCreate response: %s', BPMJ_EDDPAYU_DOMAIN ), json_encode( $response ) ) );
				edd_send_back_to_checkout( array(
					'payment-mode' => $purchase_data[ 'post_data' ][ 'edd-gateway' ],
					'payu-error'   => 1,
				) );
			}
		} catch ( \OpenPayU_Exception $e ) {
		    $_SESSION['cart-error-message'] = $e->getMessage();
		    EddExtensions::instance()->log('hook_process_payment exception ' . $e->getMessage());
			edd_record_gateway_error( __( 'Could not create PayU order (exception)', BPMJ_EDDPAYU_DOMAIN ), $e->getMessage() );
			edd_send_back_to_checkout( array(
				'payment-mode' => $purchase_data[ 'post_data' ][ 'edd-gateway' ],
				'payu-error'   => 1,
				'cart-error'   => 1,
			) );
		}
	}

	/**
	 *
	 * @throws \Exception
	 */
	public function hook_init() {
		if ( isset( $_REQUEST[ 'payu_notification' ] ) && 'POST' === $_SERVER[ 'REQUEST_METHOD' ] ) {
			$body    = trim( file_get_contents( 'php://input' ) );
			$message = $this->process_server_response( $body );
			EddExtensions::instance()->log('hook_init mess ' . $message);
			if ( 'OK' === substr( $message, 0, 2 ) ) {
				header( "HTTP/1.1 200 OK" );
			} else {
				header( "HTTP/1.1 400 Bad Request" );
			}
			exit ( $message );
		} else if ( isset( $_POST[ 'bpmj_eddpayu_cancel_subscription' ] ) ) {
			$this->process_cancel_subscription();
		} else if ( isset( $_POST[ 'bpmj_eddpayu_pay_for_subscription' ] ) ) {
			$this->process_pay_for_subscription();
		}
	}

	/**
	 * @param string $purchase_key
	 *
	 * @throws \OpenPayU_Exception
	 */
	protected function receive_payment( $purchase_key ) {

		$payment_id = edd_get_purchase_id_by_key( $purchase_key );

		if ( empty( $payment_id ) ) {
			return;
		}

		$payu_order_id = get_post_meta( $payment_id, '_payu_order_id', true );
		if ( ! $payu_order_id ) {
			return;
		}

		$message = $this->process_order_status_mutex( $payu_order_id, $payment_id );
		EddExtensions::instance()->log('receive_payment mess ' . $message);
	}

	/**
	 * @param string $body
	 *
	 * @return string
	 */
	protected function process_server_response( $body ) {
	    EddExtensions::instance()->log('process_server_response ' . $body);
		try {
			if ( empty( $body ) ) {
				return 'EMPTY BODY';
			}
			$result = \OpenPayU_Order::consumeNotification( $body );
			if ( ! $result->getResponse() || empty( $result->getResponse()->order ) ) {
				return 'INVALID RESPONSE - NO ORDER';
			}
			$payu_order_id = $result->getResponse()->order->orderId;
			if ( ! $payu_order_id ) {
				return 'INVALID RESPONSE - NO ORDER ID';
			}
			if ( 'PENDING' === $result->getResponse()->order->status ) {
				return 'OK: PENDING STATUS IGNORED';
			}

			return $this->process_order_status_mutex( $payu_order_id );
		} catch ( \OpenPayU_Exception $e ) {
			return 'EXCEPTION: ' . $e->getMessage();
		}
	}

	/**
	 * @param string $payu_order_id
	 * @param int $payment_id
	 *
	 * @return string
	 * @throws \OpenPayU_Exception
	 */
	protected function process_order_status_mutex( $payu_order_id, $payment_id = null ) {
		$mutex              = new \BPMJ_Mutex( 'payu-order', BPMJ_EDDPAYU_DIR );
		$this->mutex_active = $mutex->lock();
		try {
			$result = $this->process_order_status( $payu_order_id, $payment_id );
		} catch ( \OpenPayU_Exception $e ) {
			$mutex->unlock();
			throw $e;
		}
		$mutex->unlock();

		return $result;
	}

	/**
	 * @param string $payu_order_id PayU order id
	 * @param int $payment_id EDD payment id
	 *
	 * @return string
	 * @throws \OpenPayU_Exception
	 */
	protected function process_order_status( $payu_order_id, $payment_id = null ) {
		$order = $this->get_payu_order( $payu_order_id );
		if ( false === $order ) {
			return 'CANNOT RETRIEVE ORDER';
		}

		if ( ! $payment_id && $order->extOrderId ) {
			$payment_id = false === strpos( $order->extOrderId, '-' ) ? $order->extOrderId : substr( $order->extOrderId, 0, strpos( $order->extOrderId, '-' ) );
		}

		$payment = get_post( $payment_id );
		if ( ! $payment ) {
			return 'EDD PAYMENT NOT FOUND';
		}
		EddExtensions::instance()->add_debug_note( $payment_id, $_SERVER[ 'REQUEST_METHOD' ] . ' ' . $_SERVER[ 'REQUEST_URI' ], __( 'Request entrypoint', BPMJ_EDDPAYU_DOMAIN ) );
		EddExtensions::instance()->add_debug_note( $payment_id, $this->mutex_active ? __( 'Mutex is active', BPMJ_EDDPAYU_DOMAIN ) : __( 'Mutex is NOT active', BPMJ_EDDPAYU_DOMAIN ) );
		if ( isset( $_REQUEST[ 'payu_notification' ] ) && 'POST' === $_SERVER[ 'REQUEST_METHOD' ] ) {
			EddExtensions::instance()->add_debug_note( $payment_id, file_get_contents( 'php://input' ), __( 'PayU raw input', BPMJ_EDDPAYU_DOMAIN ) );
		}
		EddExtensions::instance()->add_debug_note( $payment_id, $order, __( 'PayU order', BPMJ_EDDPAYU_DOMAIN ) );

		$this->updating_order_direction = static::DIRECTION_PAYU_TO_EDD;

		$order_transactions_response = \OpenPayU_Order::retrieveTransaction( $payu_order_id );
		EddExtensions::instance()->log('process_order_status resp ' . print_r($order_transactions_response, true));
		if ( $order_transactions_response instanceof \OpenPayU_Result && isset( $order_transactions_response->getResponse()->transactions ) ) {
			$transactions            = $order_transactions_response->getResponse()->transactions;
			$registered_transactions = get_post_meta( $payment_id, '_payu_registered_transactions', true );
			if ( ! is_array( $registered_transactions ) ) {
				$registered_transactions = array();
			}
			if ( empty( $registered_transactions[ $payu_order_id ] ) ) {
				$registered_transactions[ $payu_order_id ] = array();
			}

			$translate_credit_card_scheme = function ( $card_scheme ) {
				switch ( $card_scheme ) {
					case 'MC':
						return 'Maestro/MasterCard';
					case 'VS':
						return 'Visa';
					default:
						return 'Other';
				}
			};

			$translate_card_type = function ( $card_classification ) {
				switch ( $card_classification ) {
					case 'DEBIT':
						return __( 'debit card', BPMJ_EDDPAYU_DOMAIN );
					default:
						return __( 'credit card', BPMJ_EDDPAYU_DOMAIN );
				}
			};

			/** @var \stdClass $transaction */
			foreach ( $transactions as $transaction_id => $transaction ) {
				if ( in_array( $transaction_id, $registered_transactions[ $payu_order_id ] ) ) {
					continue;
				}
				if ( isset( $transaction->card ) && isset( $transaction->card->cardData ) ) {
					/** @var \stdClass $card_data */
					$card_data = $transaction->card->cardData;
					edd_insert_payment_note( $payment_id, sprintf( _x( '%1$s transaction status: %2$s', 'Credit card transaction status', BPMJ_EDDPAYU_DOMAIN ),
						sprintf( _x( '%1$s %2$s', 'Maestro/MasterCard/Visa credit/debit card', BPMJ_EDDPAYU_DOMAIN ),
							$translate_credit_card_scheme( $card_data->cardScheme ),
							$translate_card_type( $card_data->cardClassification ) ),
						$card_data->cardResponseCodeDesc ) );
					if ( ! $card_data->cardResponseCodeDesc ) {
						EddExtensions::instance()->add_debug_note( $payment_id, $card_data, __( 'Card data (transaction)', BPMJ_EDDPAYU_DOMAIN ) );
					}
				}
				$registered_transactions[ $payu_order_id ][] = $transaction_id;
			}
			update_post_meta( $payment_id, '_payu_registered_transactions', $registered_transactions );
		}

		if ( 'COMPLETED' === $order->status && !in_array($payment->post_status, ['publish', 'future']) ) {
			EddExtensions::instance()->add_debug_note( $payment_id, __( 'Preparing to finish payment', BPMJ_EDDPAYU_DOMAIN ) );
			if ( '1' === get_post_meta( $payment_id, '_payu_setup_recurrence', true ) ) {
				EddExtensions::instance()->add_debug_note( $payment_id, 'Setting up recurrence' );
				delete_post_meta( $payment_id, '_payu_setup_recurrence' );
				$this->split_and_create_future_payments( $payment_id );
			}
			edd_insert_payment_note( $payment_id, __( 'PayU transaction completed successfully', BPMJ_EDDPAYU_DOMAIN ) );
			$update_post_data = array(
				'ID'            => $payment_id,
				'post_date'     => current_time( 'Y-m-d' ),
				'post_date_gmt' => get_gmt_from_date( current_time( 'Y-m-d' ) ),
			);
			EddExtensions::instance()->add_debug_note( $payment_id, $update_post_data, __( 'Updating payment dates', BPMJ_EDDPAYU_DOMAIN ) );
			wp_update_post( array(
				'ID'            => $payment_id,
				'post_date'     => current_time( 'Y-m-d' ),
				'post_date_gmt' => get_gmt_from_date( current_time( 'Y-m-d' ) ),
			) );
			EddExtensions::instance()->add_debug_note( $payment_id, 'Setting payment status to completed' );
			edd_update_payment_status( $payment_id, 'completed' );

			return 'OK: MARKED AS COMPLETED';
		} else if ( 'REJECTED' === $order->status && !in_array($payment->post_status, ['failed', 'publish', 'future']) ) {
			edd_insert_payment_note( $payment_id, __( 'PayU transaction was rejected', BPMJ_EDDPAYU_DOMAIN ) );

            if (false === metadata_exists('post', $payment_id, '_payu_recurrent_payment_token')) {
                edd_update_payment_status($payment_id, 'failed');
                return 'OK: MARKED AS FAILED';
            }

            return 'OK: NOT MARKED AS FAILED - RECURRING PAYMENT';
		} else if ( 'CANCELED' === $order->status && !in_array($payment->post_status, ['failed', 'publish', 'future']) ) {
			edd_insert_payment_note( $payment_id, __( 'PayU transaction was cancelled', BPMJ_EDDPAYU_DOMAIN ) );

			if (false === metadata_exists('post', $payment_id, '_payu_recurrent_payment_token')) {
                edd_update_payment_status($payment_id, 'failed');
                return 'OK: MARKED AS FAILED';
            }

            return 'OK: NOT MARKED AS FAILED - RECURRING PAYMENT';
        } else if ( 'WAITING_FOR_CONFIRMATION' === $order->status ) {
			edd_insert_payment_note( $payment_id, __( 'PayU transaction is waiting for manual confirmation', BPMJ_EDDPAYU_DOMAIN ) );

			return 'OK: MARKED AS WAITING FOR CONFIRMATION';
		}

		return 'OK: IGNORED';
	}

	/**
	 * Retrieve PayU order object
	 *
	 * @param $payu_order_id
	 *
	 * @return bool|\stdClass
	 */
	protected function get_payu_order( $payu_order_id ) {
		$order_response = \OpenPayU_Order::retrieve( $payu_order_id );
		EddExtensions::instance()->log('get_payu_order resp ' . print_r($order_response, true));
		if ( 'SUCCESS' !== $order_response->getStatus() ) {
			return false;
		}

		$order = $order_response->getResponse()->orders[ 0 ];
		if ( $order instanceof \stdClass ) {
			return $order;
		}

		return false;
	}

	/**
	 * @param \EDD_Payment $payment
	 *
	 * @return bool
	 */
	public function hook_process_refund( \EDD_Payment $payment ) {
		$payu_order_id = $payment->get_meta( '_payu_order_id' );
		if ( ! $payu_order_id ) {
			return false;
		}

		$order = $this->get_payu_order( $payu_order_id );
		if ( false === $order ) {
			return false;
		}

		if ( 'COMPLETED' === $order->status ) {
			$amount_text     = number_format( $payment->total, 2, '.', ' ' ) . ' ' . $payment->currency;
			$refund_response = \OpenPayU_Refund::create(
				$payu_order_id,
				sprintf( __( 'Refund of: %1$s for order: %2$s (EDD payment id: %3$s)', BPMJ_EDDPAYU_DOMAIN ), $amount_text, $payu_order_id, $payment->ID ),
				bcmul( $payment->total, 100 )
			);

			if ( 'SUCCESS' === $refund_response->getStatus() ) {
				edd_insert_payment_note( $payment->ID, __( 'PayU transaction refunded successfully', BPMJ_EDDPAYU_DOMAIN ) );

				return true;
			} else {
				edd_insert_payment_note( $payment->ID, sprintf( __( 'PayU transaction refund failed. Status: %s', BPMJ_EDDPAYU_DOMAIN ), $refund_response->getMessage() ) );
			}
		}

		return false;
	}

	/**
	 * @param int $payment_id
	 * @param string $new_status
	 */
	public function hook_process_payment_status_change( $payment_id, $new_status ) {
		if ( static::DIRECTION_EDD_TO_PAYU !== $this->updating_order_direction ) {
			return;
		}

		$payu_order_id = get_post_meta( $payment_id, '_payu_order_id', true );
		if ( ! $payu_order_id ) {
			return;
		}

		try {
			$order = $this->get_payu_order( $payu_order_id );
			if ( false === $order ) {
				return;
			}

			if ( 'publish' === $new_status && '1' === get_post_meta( $payment_id, '_payu_setup_recurrence', true ) ) {
				delete_post_meta( $payment_id, '_payu_setup_recurrence' );
				$this->split_and_create_future_payments( $payment_id );
			}

			if ( 'COMPLETED' !== $order->status && 'publish' === $new_status ) {
				$order_status_update_response = \OpenPayU_Order::statusUpdate( array(
					'orderId'     => $payu_order_id,
					'orderStatus' => 'COMPLETED',
				) );
				if ( 'SUCCESS' === $order_status_update_response->getStatus() ) {
					edd_insert_payment_note( $payment_id, __( 'PayU transaction status set to COMPLETED', BPMJ_EDDPAYU_DOMAIN ) );
				} else {
					edd_insert_payment_note( $payment_id, sprintf( __( 'Setting PayU transaction status to COMPLETED failed (current status: %s)', BPMJ_EDDPAYU_DOMAIN ), $order->status ) );
				}
			} else if ( 'CANCELED' !== $order->status && 'revoked' === $new_status ) {
				$order_cancel_response = \OpenPayU_Order::cancel( $payu_order_id );
				if ( 'SUCCESS' === $order_cancel_response->getStatus() ) {
					edd_insert_payment_note( $payment_id, __( 'PayU transaction status set to CANCELED', BPMJ_EDDPAYU_DOMAIN ) );
				} else {
					edd_insert_payment_note( $payment_id, sprintf( __( 'Setting PayU transaction status to CANCELED failed (current status: %s)', BPMJ_EDDPAYU_DOMAIN ), $order->status ) );
				}
			}
		} catch ( \OpenPayU_Exception $e ) {
			edd_insert_payment_note( $payment_id, sprintf( __( 'Could not update PayU order status. Reason: %s', BPMJ_EDDPAYU_DOMAIN ), $e->getMessage() ) );
		}
	}

	/**
	 * @inheritdoc
	 */
	public function setup_edd_payu_purchase_form() {
		if ( $this->payu_form_set_up ) {
			return;
		}
		$this->payu_form_set_up = true;

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

	public function filter_modify_checkout_purchase_button_label() {
		return __( 'Purchase and pay with PayU', BPMJ_EDDPAYU_DOMAIN );
	}

	public function filter_modify_checkout_button_mixed_items( $button_html ) {
		$color                      = edd_get_option( 'checkout_color', 'blue' );
		$color                      = ( $color == 'inherit' ) ? '' : $color;
		$style                      = edd_get_option( 'button_style', 'button' );
		$pay_for_recurring_label    = __( 'Purchase and pay with credit card for recurring items', BPMJ_EDDPAYU_DOMAIN );
		$pay_for_recurring_info     = __( 'You will be able to complete purchase for other items using a different payment method', BPMJ_EDDPAYU_DOMAIN );
		$pay_for_all_label          = __( 'Purchase and pay with credit card for all items', BPMJ_EDDPAYU_DOMAIN );
		$pay_for_all_label_standard = static::get_standard_payment_label();
		$script_tag_all             = $this->payu_recurring_script_for_all_items();
		$script_tag_recurring       = $this->payu_recurring_script_for_recurring_items();
		$new_button_html            = '<p><span id="edd-payu-button-pay-for-recurring-hidden"></span>' .
		                              '<button type="submit" class="edd-submit ' . $color . ' ' . $style . ' edd-payu-submit-button" data-payu-script="' . esc_attr( $script_tag_recurring ) . '" id="edd-payu-button-pay-for-recurring">' . $pay_for_recurring_label . '</button>' .
		                              '<span class="edd-description">' . $pay_for_recurring_info . '</span>' .
		                              '</p>';
		$new_button_html            .= '<p><span id="edd-payu-button-pay-for-all-hidden"></span><button type="submit" class="edd-submit ' . $color . ' ' . $style . ' edd-payu-submit-button" data-payu-script="' . esc_attr( $script_tag_all ) . '" id="edd-payu-button-pay-for-all">' . $pay_for_all_label . '</button>' .
		                               '<span class="edd-description">' . static::get_cards_payment_description() . '</span>' .
		                               '</p>';
		if ( $this->allow_noncard_payments ) {
			$new_button_html .= '<p>' .
			                    '<button type="submit" name="edd_payu_pay_for_all_items_standard" class="edd-submit ' . $color . ' ' . $style . ' edd-payu-submit-button" id="edd-payu-button-pay-for-all-standard" value="1">' . $pay_for_all_label_standard . '</button>' .
			                    '<span class="edd-description">' . static::get_standard_payment_description() . '</span>' .
			                    '</p>';
		}
		$new_button_html .= '<div style="display: none;">' . $button_html . '</div>';
		$new_button_html .= $this->get_recurrence_hidden_fields_html();

		return $new_button_html;
	}

	public function filter_modify_checkout_button_recurring_items_only( $button_html ) {
		$color             = edd_get_option( 'checkout_color', 'blue' );
		$color             = ( $color == 'inherit' ) ? '' : $color;
		$style             = edd_get_option( 'button_style', 'button' );
		$pay_for_all_label = __( 'Purchase and pay with credit card', BPMJ_EDDPAYU_DOMAIN );
		$script_tag_all    = $this->payu_recurring_script_for_all_items();
		$new_button_html   = '<p><span id="edd-payu-button-pay-for-all-hidden"></span>' .
		                     '<button type="submit" class="edd-submit ' . $color . ' ' . $style . ' edd-payu-submit-button" data-payu-script="' . esc_attr( $script_tag_all ) . '" id="edd-payu-button-pay-for-all">' . $pay_for_all_label . '</button>' .
		                     '<span class="edd-description">' . static::get_cards_payment_description() . '</span>' .
		                     '</p>';
		if ( $this->allow_noncard_payments ) {
			$new_button_html .= '<p>' .
			                    '<button type="submit" name="edd_payu_pay_for_all_items_standard" class="edd-submit ' . $color . ' ' . $style . ' edd-payu-submit-button" id="edd-payu-button-pay-for-all-standard" value="1">' . static::get_standard_payment_label() . '</button>' .
			                    '<span class="edd-description">' . static::get_standard_payment_description() . '</span>' .
			                    '</p>';
		}
		$new_button_html .= '<div style="display: none;">' . $button_html . '</div>';
		$new_button_html .= $this->get_recurrence_hidden_fields_html();

		return $new_button_html;
	}

	/**
	 * Create PayU <script> tag for recurring items
	 *
	 * @return string
	 */
	public function payu_recurring_script_for_recurring_items() {
		$recurring_items_amount = '00.00';

		foreach ( $this->cart_items_recurring_payment as $item ) {
			$recurring_items_amount = bcadd( $recurring_items_amount, $item[ 'price' ], 2 );
		}

		$script_attrs = array(
			'pay-button'       => '#edd-payu-button-pay-for-recurring-hidden',
			'total-amount'     => $recurring_items_amount,
			'customer-email'   => '',
			'success-callback' => 'edd_payu_process_token_response_for_recurring_items',
		);

		return PayuTransparent::instance()->create_script_tag( $script_attrs, false );
	}

	/**
	 * Create PayU <script> tag for all items
	 *
	 * @return string
	 */
	public function payu_recurring_script_for_all_items() {
		$all_items_amount = number_format( edd_get_cart_total(), 2, '.', '' );
		$script_attrs     = array(
			'pay-button'       => '#edd-payu-button-pay-for-all-hidden',
			'total-amount'     => $all_items_amount,
			'customer-email'   => '',
			'success-callback' => 'edd_payu_process_token_response_for_all_items',
		);

		return PayuTransparent::instance()->create_script_tag( $script_attrs, false );
	}

	/**
	 * Get HTML with necessary hidden inputs to enable recurrent payments
	 *
	 * @return string
	 */
	protected function get_recurrence_hidden_fields_html() {
		$fields = '<input type="hidden" id="edd-payu-card-token" name="edd_payu_card_token" value="" />' .
		          '<input type="hidden" id="edd-payu-pay-for-recurring-items-only" name="edd_payu_pay_for_recurring_items_only" value="0" />';

		return $fields;
	}

	/**
	 * @return string
	 */
	protected static function get_standard_payment_label() {
		return __( 'Purchase and pay with other payment methods', BPMJ_EDDPAYU_DOMAIN );
	}

	/**
	 * @return string
	 */
	protected static function get_cards_payment_description() {
		return __( 'Your credit card will be charged automatically for each period.
			You can cancel the subscription anytime.', BPMJ_EDDPAYU_DOMAIN );
	}

	/**
	 * @return string
	 */
	protected static function get_standard_payment_description() {
		return __( 'Payments for future periods will be prepared automatically and you will be asked to fulfill them manually on each occurrence. 
			You can process future payment or cancel the subscription anytime.', BPMJ_EDDPAYU_DOMAIN );
	}

	/**
	 * Remove all standard (non-recurring) items from the payment
	 *
	 * @param int $payment_id
	 */
	protected function remove_standard_items_from_payment( $payment_id ) {
		$payment = new \EDD_Payment( $payment_id );
		foreach ( $this->get_cart_items_standard_payment() as $item ) {
			$payment->remove_download( $item[ 'id' ] );
		}
		$payment->save();
	}

	/**
	 * Creates payment's next occurrence
	 *
	 * @param int $parent_payment_id
	 * @param string $payment_date
	 * @param array $cart_details
	 */
	protected function create_future_payment( $parent_payment_id, $payment_date, $cart_details ) {
		$payment                 = new \EDD_Payment( $parent_payment_id );
		$recurrent_payment_token = $payment->get_meta( '_payu_recurrent_payment_token' );
		$price                   = '00.00';

		foreach ( $cart_details as $item ) {
			$price = bcadd( $price, $item[ 'item_price' ], 2 );
		}

		$sequence_number = $payment->get_meta( '_payu_recurrent_sequence_number' );
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
			'gateway'      => PayuGatewayConnector::PAYU_GATEWAY_ID,
		);
		if ( ! apply_filters( 'bpmj_eddpayu_should_create_next_payment', true, $parent_payment_id, $payment_data, $sequence_number ) ) {
			return;
		}
		$future_payment_id = edd_insert_payment( $payment_data );
		$first_payment_id  = get_post_meta( $parent_payment_id, '_payu_recurrent_first_payment_id', true );
		if ( ! $first_payment_id ) {
			$first_payment_id = $parent_payment_id;
		}
		update_post_meta( $future_payment_id, '_payu_setup_recurrence', '1' );
		update_post_meta( $future_payment_id, '_payu_recurrent_first_payment_id', $first_payment_id );
		update_post_meta( $future_payment_id, '_payu_recurrent_previous_payment_id', $parent_payment_id );
		if ( $recurrent_payment_token ) {
			update_post_meta( $future_payment_id, '_payu_recurrent_payment_token', $recurrent_payment_token );
		}
		update_post_meta( $future_payment_id, '_payu_payment_subtype', 'recurrent' );
		update_post_meta( $future_payment_id, '_payu_recurrent_last_try', date( 'Y-m-d' ) );
		update_post_meta( $future_payment_id, '_payu_recurrent_sequence_number', $sequence_number );
		update_post_meta( $future_payment_id, '_payu_discount_details', get_post_meta( $parent_payment_id, '_payu_discount_details', true ) );
		$future_payment      = new \EDD_Payment( $future_payment_id );
		$future_payment_meta = $old_future_payment_meta = $future_payment->get_meta();

		/*
		 * Copy all meta over to the new payment
		 */
		foreach ( $payment->get_meta() as $meta_key => $meta_value ) {
//			if ( 'bpmj_' === substr( $meta_key, 0, 5 ) && ! isset( $future_payment_meta[ $meta_key ] ) ) {
			if ( !in_array($meta_key, ['date', 'downloads', 'cart_details']) ) {
			    $future_payment_meta[ $meta_key ] = $meta_value;
			}
		}

		$future_payment->update_meta( '_edd_payment_meta', $future_payment_meta, $old_future_payment_meta );
		EddExtensions::instance()->correct_discounts( $future_payment_id );
		edd_insert_payment_note( $parent_payment_id, sprintf( __( 'Next occurrence payment created (%1$s). Payment date: %2$s', BPMJ_EDDPAYU_DOMAIN ), '#' . $future_payment_id, $payment_date ) );
	}

	/**
	 * @param int $payment_id
	 *
	 * @return bool
	 * @throws \OpenPayU_Exception
	 */
	protected function process_recurrent_payment( $payment_id ) {
		EddExtensions::instance()->correct_discounts( $payment_id );
		$payment = new \EDD_Payment( $payment_id );
		if ( 'publish' === $payment->post_status ) {
			return false;
		}
		if ( date( 'Y-m-d' ) === $payment->get_meta( '_payu_recurrent_last_try' ) ) {
			// We are only allowed to try charging card once per day
			return false;
		}
		$payment->update_meta( '_payu_recurrent_last_try', date( 'Y-m-d' ) );
		$token = $payment->get_meta( '_payu_recurrent_payment_token' );
		if ( ! $token ) {
			return false;
		}
		$payu_order_id = get_post_meta( $payment_id, '_payu_order_id', true );
		if ( $payu_order_id ) {
			$payu_order_status = $this->process_order_status_mutex( $payu_order_id, $payment_id );
			EddExtensions::instance()->log('process_recurrent_payment payment_id ' . $payment_id . ' payu_order_id ' . $payu_order_id . ' mess ' . $payu_order_status);
			EddExtensions::instance()->add_debug_note( $payment_id, array(
				'payu_order_id'     => $payu_order_id,
				'payu_order_status' => $payu_order_status
			), __( 'Checking PayU order status', BPMJ_EDDPAYU_DOMAIN ) );
			if ( 'OK' === substr( $payu_order_status, 0, 2 ) ) {
				return false;
			}
		}
		$buyer    = array(
			'firstName' => $payment->first_name,
			'lastName'  => $payment->last_name,
			'email'     => $payment->email,
		);
		$products = $this->create_payu_products_array( $payment->cart_details );

		$order = array(
			'notifyUrl'     => home_url( '/' ) . '?payu_notification',
			'customerIp'    => $_SERVER[ 'SERVER_ADDR' ],
			'merchantPosId' => $this->pos_id,
			'recurring'     => 'STANDARD',
			'description'   => $this->get_transaction_description( $payment_id ),
			'currencyCode'  => $payment->currency,
			'totalAmount'   => bcmul( $payment->total, 100 ),
			'extOrderId'    => $payment_id . '-' . preg_replace( '#^https?://#', '', get_option( 'siteurl' ) ) . '-' . substr( sha1( microtime() ), 0, 4 ),
			'buyer'         => $buyer,
			'products'      => $products,
			'payMethods'    => array(
				'payMethod' => array(
					'value' => $token,
					'type'  => 'CARD_TOKEN',
				)
			)
		);
		EddExtensions::instance()->log('process_recurrent_payment req ' . print_r($order, true));
		
		try {
			/** @var \OpenPayU_Result $response */
			$response = \OpenPayU_Order::create( $order );
			EddExtensions::instance()->log('process_recurrent_payment resp ' . print_r($response, true));

			if ( 'SUCCESS' === $response->getStatus() ) {
				edd_insert_payment_note( $payment_id, sprintf( __( 'PayU order created. Order number: %s', BPMJ_EDDPAYU_DOMAIN ), $response->getResponse()->orderId ) );
				update_post_meta( $payment_id, '_payu_order_id', $response->getResponse()->orderId );

				wp_update_post( array(
					'ID'            => $payment_id,
					'post_date'     => current_time( 'mysql' ),
					'post_date_gmt' => '',
				) );

				return true;
			} else {
				edd_insert_payment_note( $payment_id, sprintf( __( 'Could not create PayU recurrent order: %s', BPMJ_EDDPAYU_DOMAIN ), json_encode( $response ) ) );
                do_action( 'bpmj_payu_recurrence_transaction_failure', $payment_id, json_encode( $response ) );
			}
		} catch ( \OpenPayU_Exception $e ) {
			edd_insert_payment_note( $payment_id, sprintf( __( 'Could not create PayU recurrent order (exception): %s', BPMJ_EDDPAYU_DOMAIN ), $e->getMessage() ) );
            do_action( 'bpmj_payu_recurrence_transaction_failure', $payment_id, $e->getMessage() );
		}

		return false;
	}

	/**
	 * Prepares $products array suitable for sending to PayU API
	 *
	 * @param array $cart_details
	 *
	 * @return array
	 */
	protected function create_payu_products_array( array $cart_details ) {
        $products = array();
        foreach ( $cart_details as $cart_item ) {
            $products[] = array(
                'name'      => html_entity_decode( $cart_item[ 'name' ], ENT_NOQUOTES, 'UTF-8' ),
                'unitPrice' => Price_Formatting::round_and_format_to_int( $cart_item[ 'price' ], Price_Formatting::MULTIPLY_BY_100 ),
                'quantity'  => $cart_item[ 'quantity' ],
            );
        }

		return $products;
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
		$last_cron_date = get_transient( 'bpmj_eddpayu_cron_date' );
		$today          = date( 'Y-m-d' );
		if ( $last_cron_date === $today ) {
			// If the cron method fired at least once today we reuse the chunk length set before
			$payment_chunk_length = (int) get_transient( 'bpmj_eddpayu_cron_chunk' );
			if ( ! $payment_chunk_length ) {
				$payment_chunk_length = - 1;
			}
		} else {
			// Otherwise we need to fetch all payments and calculate chunk length for today
			$payment_chunk_length = - 1;
			set_transient( 'bpmj_eddpayu_cron_date', $today );
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
				'_payu_payment_subtype'         => array(
					'key'   => '_payu_payment_subtype',
					'value' => 'recurrent',
				),
				'_payu_recurrent_last_try'      => array(
					'key'     => '_payu_recurrent_last_try',
					'value'   => $today,
					'compare' => '<',
				),
				'_payu_recurrent_payment_token' => array(
					'key'     => '_payu_recurrent_payment_token',
					'compare' => 'EXISTS',
				),
			),
			'meta_key'   => '_payu_payment_subtype',
			'meta_value' => 'recurrent',
			'number'     => $payment_chunk_length,
			'order'      => 'ASC',
			'orderby'    => '_payu_recurrent_last_try',
		) );

		if ( - 1 === $payment_chunk_length ) {
			// Calculate chunk length for today
			// We want to perform only 3% of all payments scheduled for the day at once (but at least 10)
			$count                = count( $payments );
			$payment_chunk_length = max( 10, ceil( $count / 33 ) );
			set_transient( 'bpmj_eddpayu_cron_chunk', $payment_chunk_length );
			$payments = array_slice( $payments, 0, $payment_chunk_length );
		}

		foreach ( $payments as $payment ) {
			/** @var \WP_Post $payment */
			EddExtensions::instance()->add_debug_note( $payment->ID, __( 'Starting recurrent payment process', BPMJ_EDDPAYU_DOMAIN ) );
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

		return bcmul( $total, 100 );
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
	 * Performed when the user chose to cancel his subscription
	 */
	protected function process_pay_for_subscription() {
		$payment_id   = filter_input( INPUT_POST, 'payment_id', FILTER_VALIDATE_INT );
		$nonce        = filter_input( INPUT_POST, 'bpmj_eddpayu_pay_for_subscription_nonce' );
		$nonce_action = 'bpmj-eddpayu-pay-for-subscription-nonce-' . $payment_id;
		$redirect     = wp_get_referer();
		if ( wp_verify_nonce( $nonce, $nonce_action ) ) {
			$redirect = $this->get_direct_gateway_url( $payment_id );
		}
		EddExtensions::instance()->log('process_pay_for_subscription redir ' . $redirect);
		wp_redirect( $redirect );
	}

	/**
	 * @param string $url
	 * @param int $payment_id
	 *
	 * @return mixed|string
	 */
	public function filter_get_direct_gateway_url( $url, $payment_id ) {
		if ( 'recurrent' !== get_post_meta( $payment_id, '_payu_payment_subtype', true ) || ! ! get_post_meta( $payment_id, '_payu_recurrent_payment_token', true ) ) {
			// If the payment is not payu recurrent or it has non-empty recurrent payment token then bail - we only allow tpay standard payments
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
		$payment       = new \EDD_Payment( $payment_id );
		$purchase_data = array(
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
		edd_set_purchase_session( $purchase_data );
		$buyer = array(
			'firstName' => $payment->first_name,
			'lastName'  => $payment->last_name,
			'email'     => $payment->email,
		);

		$products     = $this->create_payu_products_array( $payment->cart_details );
		$continue_url = edd_get_success_page_uri() . '?payu_purchase_key=' . $purchase_data[ 'purchase_key' ];
		$customer_ip  = get_post_meta( $payment_id, '_payu_customer_ip', true );
		$order        = array(
			'notifyUrl'     => home_url( '/' ) . '?payu_notification',
			'continueUrl'   => $continue_url,
			'customerIp'    => $customer_ip ? $customer_ip : $this->get_remote_ip(),
			'merchantPosId' => $this->pos_id,
			'description'   => $this->get_transaction_description( $payment_id ),
			'currencyCode'  => $purchase_data[ 'currency' ] ? $purchase_data[ 'currency' ] : 'PLN',
			'totalAmount'   => $this->get_total_amount( $purchase_data ),
			'extOrderId'    => $payment_id . '-' . preg_replace( '#^https?://#', '', get_option( 'siteurl' ) ) . '-' . substr( sha1( microtime() ), 0, 4 ),
			'buyer'         => $buyer,
			'products'      => $products,
			'settings'      => array( 'invoiceDisabled' => true )
		);
		EddExtensions::instance()->log('get_direct_gateway_url req=' . print_r($order, true));

		try {
			/** @var \OpenPayU_Result $response */
			$response = \OpenPayU_Order::create( $order );
			EddExtensions::instance()->log('get_direct_gateway_url resp=' . print_r($response, true));
			
			if ( in_array( $response->getStatus(), array(
				'SUCCESS',
				'WARNING_CONTINUE_3DS',
				'WARNING_CONTINUE_CVV',
			) ) ) {
				edd_insert_payment_note( $payment_id, sprintf( __( 'PayU order created. Order number: %s', BPMJ_EDDPAYU_DOMAIN ), $response->getResponse()->orderId ) );
				update_post_meta( $payment_id, '_payu_order_id', $response->getResponse()->orderId );
				if ( $response->getResponse()->redirectUri ) {
					return $response->getResponse()->redirectUri;
				}
			} else {
				edd_record_gateway_error( __( 'Could not create PayU order', BPMJ_EDDPAYU_DOMAIN ), sprintf( __( 'PayU OrderCreate response: %s', BPMJ_EDDPAYU_DOMAIN ), json_encode( $response ) ) );
			}
		} catch ( \OpenPayU_Exception $e ) {
			edd_record_gateway_error( __( 'Could not create PayU order (exception)', BPMJ_EDDPAYU_DOMAIN ), $e->getMessage() );
		}

		return home_url( '/' );
	}

	/**
	 * Display additional status description for PayU recurrent payments
	 *
	 * @param mixed $value
	 * @param int $payment_id
	 * @param string $column_name
	 *
	 * @return string
	 */
	public function filter_modify_payments_table_column( $value, $payment_id, $column_name ) {
		if ( 'status' === $column_name && 'recurrent' === get_post_meta( $payment_id, '_payu_payment_subtype', true ) ) {
			return $value . ' (' . __( 'PayU recurrent payment', BPMJ_EDDPAYU_DOMAIN ) . ')';
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

		$this->set_message( 'info', sprintf( __( 'Recurrent payments set up successfully. Payment status: %s. All recurring items have been removed from the cart.', BPMJ_EDDPAYU_DOMAIN ), '<strong>' . edd_get_payment_status( $payment_id, true ) . '</strong>' ) );
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
			if ( 'recurrent' === get_post_meta( $payment_id, '_payu_payment_subtype', true ) && 'pending' === get_post_status( $payment_id ) ):
//				_e( 'Scheduled recurrent payment', BPMJ_EDDPAYU_DOMAIN );
				?>
                <a href="<?php echo esc_url( add_query_arg( 'payment_key', edd_get_payment_key( $payment_id ), edd_get_success_page_uri() ) ); ?>"><?php _e( 'Subscription details', BPMJ_EDDPAYU_DOMAIN ); ?></a>
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

		if ( 'recurrent' === get_post_meta( $payment_id, '_payu_payment_subtype', true ) && 'pending' === get_post_status( $payment_id ) ):
			$automatic = (bool) get_post_meta( $payment_id, '_payu_recurrent_payment_token' );
			?>
            <tr>
                <td><strong><?php _e( 'Options', BPMJ_EDDPAYU_DOMAIN ); ?>:</strong></td>
                <td>
					<?php if ( ! $automatic ): ?>
                        <form action="" method="post">
							<?php wp_nonce_field( 'bpmj-eddpayu-pay-for-subscription-nonce-' . $payment_id, 'bpmj_eddpayu_pay_for_subscription_nonce' ); ?>
                            <input type="hidden" name="payment_id" value="<?php echo $payment_id; ?>"/>
                            <input class="edd-submit blue button" type="submit"
                                   name="bpmj_eddpayu_pay_for_subscription"
                                   value="<?php _e( 'Pay with PayU', BPMJ_EDDPAYU_DOMAIN ); ?>"/>
                        </form>
					<?php endif; ?>
                    <form id="bpmj-eddpayu-cancel-subscription-form" action="" method="post">
						<?php wp_nonce_field( 'bpmj-eddpayu-cancel-subscription-nonce-' . $payment_id, 'bpmj_eddpayu_cancel_subscription_nonce' ); ?>
                        <input type="hidden" name="payment_id" value="<?php echo $payment_id; ?>"/>
                        <input class="edd-submit blue button" type="submit"
                               name="bpmj_eddpayu_cancel_subscription"
                               value="<?php _e( 'Cancel subscription', BPMJ_EDDPAYU_DOMAIN ); ?>"/>
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
		$nonce        = filter_input( INPUT_POST, 'bpmj_eddpayu_cancel_subscription_nonce' );
		$nonce_action = 'bpmj-eddpayu-cancel-subscription-nonce-' . $payment_id;
		if ( wp_verify_nonce( $nonce, $nonce_action ) ) {
			edd_update_payment_status( $payment_id, 'revoked' );
		}
		EddExtensions::instance()->log('process_cancel_subscription redir ' . wp_get_referer());
		wp_redirect( wp_get_referer() );
	}

	/**
	 * Frontend scripts
	 */
	public function frontend_scripts() {
		wp_enqueue_script( 'bpmj_eddpayu_front_scripts', BPMJ_EDDPAYU_URL . 'assets/js/edd-payu.min.js', array( 'jquery' ), BPMJ_EDDPAYU_VERSION );
		wp_localize_script( 'bpmj_eddpayu_front_scripts', 'bpmj_eddpayu', array(
			'ajax_url'                    => edd_get_ajax_url(),
			'admin_url'                   => admin_url(),
			'purchase_loading'            => __( 'Please Wait...', BPMJ_EDDPAYU_DOMAIN ),
			'confirm_cancel_subscription' => __( 'Are you sure you want to cancel this subscription?', BPMJ_EDDPAYU_DOMAIN ),
		) );
	}

	/**
	 * Schedules (or reschedules if necessary) recurrent payments cron
	 */
	protected function schedule_recurrent_payments() {
		$hook       = 'bpmj_eddpayu_recurrent_payments';
		$recurrence = 'bpmj_eddpayu_once_every_10_min';
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
	 * @return string
	 */
	protected function get_remote_ip() {
		if ( ! empty( $_SERVER[ 'HTTP_CLIENT_IP' ] ) ) {
			return $_SERVER[ 'HTTP_CLIENT_IP' ];
		} elseif ( ! empty( $_SERVER[ 'HTTP_X_FORWARDED_FOR' ] ) ) {
			return $_SERVER[ 'HTTP_X_FORWARDED_FOR' ];
		}

		return $_SERVER[ 'REMOTE_ADDR' ];
	}
}
