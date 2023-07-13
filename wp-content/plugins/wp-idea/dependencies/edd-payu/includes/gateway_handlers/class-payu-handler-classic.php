<?php

namespace bpmj\wp\eddpayu\gateway_handlers;

use bpmj\wp\eddpayu\service\EddExtensions;

class PayuHandlerClassic extends PayuHandlerAbstract {

	const TRANSACTION_STATUS_NEW = 1;
	const TRANSACTION_STATUS_CANCELLED = 2;
	const TRANSACTION_STATUS_REJECTED = 3;
	const TRANSACTION_STATUS_STARTED = 4;
	const TRANSACTION_STATUS_COMPLETE = 99;

	public function bootstrap() {
		parent::bootstrap();

		add_action( 'edd_gateway_payu', array( $this, 'hook_process_payment' ) );
		add_action( 'init', array( $this, 'hook_init' ) );
	}

	protected function get_api_path() {
		return '';
	}

	protected function get_api_version() {
		return '';
	}

	public function __construct() {
		global $edd_options;
		$this->pos_id                 = edd_get_option( 'payu_pos_id' );
		$this->pos_auth_key           = edd_get_option( 'payu_pos_auth_key' );
		$this->key1                   = edd_get_option( 'payu_key1' );
		$this->key2                   = edd_get_option( 'payu_key2' );
		$this->environment            = edd_get_option( 'payu_api_environment' );
		$this->allow_noncard_payments = ! empty( $edd_options[ 'payu_recurrence_allow_standard_payments' ] ) && '1' == $edd_options[ 'payu_recurrence_allow_standard_payments' ];
	}

	/**
	 * @param string $method
	 *
	 * @return string
	 */
	protected function get_classic_service_url( $method ) {
		$method = trim( $method, '/' );

		return \OpenPayU_Configuration::getServiceUrl() . "paygw/UTF/{$method}/";
	}

	/**
	 * @param array $purchase_data
	 */
	public function hook_process_payment( $purchase_data ) {
		$payment_id = $this->create_payment_for_purchase( $purchase_data );
		if ( false === $payment_id ) {
			return;
		}

		$payu_url     = $this->get_classic_service_url( 'NewPayment' );
		$amount       = $this->get_total_amount( $purchase_data );
		$payu_session = $purchase_data[ 'purchase_key' ];

		$t_now = time();

		$payu_args = array(
			'pos_id'       => $this->pos_id,
			'session_id'   => $payu_session,
			'pos_auth_key' => $this->pos_auth_key,
			'amount'       => $amount,
			'desc'         => $this->get_transaction_description( $payment_id ),
			'first_name'   => $purchase_data[ 'user_info' ][ 'first_name' ],
			'last_name'    => $purchase_data[ 'user_info' ][ 'last_name' ],
			'email'        => $purchase_data[ 'user_info' ][ 'email' ],
			'language'     => 'pl',
			'client_ip'    => $_SERVER[ 'REMOTE_ADDR' ],
			'ts'           => $t_now,
		);

		/*
		 * Message signature (SIG)
		 * Formula:
		 * md5(pos_id + session_id + pos_auth_key + amount + desc + first_name + last_name + email + language + client_ip
		 * + ts + key1)
		 */
		$sig                = md5( implode( '', $payu_args ) . $this->key1 );
		$payu_args[ 'sig' ] = $sig;

		$payu_args = apply_filters( 'bpmj_eddpayu_classic_payu_args', $payu_args, $purchase_data );
		EddExtensions::instance()->log('hook_process_payment url ' . $payu_url . ' req ' . print_r($payu_args, true));

		$payu_url .= '?' . http_build_query( $payu_args );
		$payu_url = str_replace( '&amp;', '&', $payu_url );

		edd_empty_cart();

		wp_redirect( $payu_url );
		exit;
	}

	/**
	 *
	 */
	public function hook_init() {
		$current_url_path = parse_url( $_SERVER[ 'REQUEST_URI' ], PHP_URL_PATH );
		if ( $current_url_path === parse_url( edd_get_success_page_uri(), PHP_URL_PATH ) ) {
			// Check if the user is on EDD success page
			if ( ! empty( $_REQUEST[ 'payu_session' ] ) ) {
			    EddExtensions::instance()->log('hook_init success_page session_id ' . $_REQUEST[ 'payu_session' ]);
			    $message = $this->receive_payment_mutex( $_REQUEST[ 'payu_session' ] );
				EddExtensions::instance()->log('hook_init success_page mess ' . $message);
			}
		} else if ( $current_url_path === parse_url( edd_get_failed_transaction_uri(), PHP_URL_PATH ) ) {
			if ( ! empty( $_REQUEST[ 'payu_session' ] ) ) {
			    EddExtensions::instance()->log('hook_init failed_transaction session_id ' . $_REQUEST[ 'payu_session' ]);
			    $message = $this->receive_payment_mutex( $_REQUEST[ 'payu_session' ] );
				EddExtensions::instance()->log('hook_init failed_transaction mess ' . $message);
			}
		} else if ( isset( $_POST[ 'pos_id' ] ) && isset( $_POST[ 'session_id' ] ) && isset( $_POST[ 'ts' ] ) && isset( $_POST[ 'sig' ] ) ) {
			$this->process_server_response( $_POST[ 'session_id' ], $_POST[ 'ts' ], $_POST[ 'sig' ] );
		}
	}

	protected function receive_payment_mutex( $payu_session ) {
	    $mutex = new \BPMJ_Mutex( 'payu-order', BPMJ_EDDPAYU_DIR );
	    try {
	        $result = $this->receive_payment( $payu_session );
	    } catch ( \OpenPayU_Exception $e ) {
	        $mutex->unlock();
	        throw $e;
	    }
	    $mutex->unlock();
	    return $result;
	}
	
	/**
	 * @param string $payu_session
	 */
	protected function receive_payment( $payu_session ) {
		$payu_request          = array(
			'pos_id'     => $this->pos_id,
			'session_id' => $payu_session,
			'ts'         => time(),
		);
		$payu_request[ 'sig' ] = md5( implode( '', $payu_request ) . $this->key1 );
		$payu_url              = $this->get_classic_service_url( 'Payment/get' );
		EddExtensions::instance()->log('receive_payment url ' . $payu_url . ' req ' . print_r($payu_request, true));
		$response              = wp_remote_post( $payu_url, array(
			'timeout'     => 45,
			'redirection' => 5,
			'body'        => $payu_request,
		) );
		EddExtensions::instance()->log('receive_payment resp ' . print_r($response, true));

		if ( is_wp_error( $response ) ) {
		    return 'INVALID RESPONSE';
		}

		$xml_body = wp_remote_retrieve_body( $response );
		$data     = simplexml_load_string( $xml_body );

		$status = (string) $data->status;

		if ( $status !== 'OK' ) {
		    return 'INVALID RESPONSE - STATUS ' . $status;
		}

		$amount       = (string) $data->trans->amount;
		$trans_status = (int) $data->trans->status;
		$ts           = (string) $data->trans->ts;
		$sig          = (string) $data->trans->sig;

		$payment_id = edd_get_purchase_id_by_key( $payu_session );

		if ( empty( $payment_id ) ) {
		    return 'NO PAYMENT FOR ' . $payu_session;
		}

		$amount_home = (string) bcmul( edd_get_payment_amount( $payment_id ), 100 );
		$desc_home   = get_bloginfo( 'name' ) . ' ' . sprintf( __( 'Payment no #%s', BPMJ_EDDPAYU_DOMAIN ), $payment_id );
		$payment     = get_post( $payment_id );
		$status_home = $payment->post_status;

		$sig_compare = md5( $this->pos_id . $payu_session . $trans_status . $amount_home . $desc_home . $ts . $this->key2 );

		if ( $trans_status !== self::TRANSACTION_STATUS_COMPLETE ) {
			if ( in_array( $trans_status, array(
				self::TRANSACTION_STATUS_CANCELLED,
				self::TRANSACTION_STATUS_REJECTED
			), true ) ) {
			    if ( !in_array($payment->post_status, ['failed', 'publish', 'future']) ) {
					edd_insert_payment_note( $payment_id, __( 'PayU transaction failed', BPMJ_EDDPAYU_DOMAIN ) );
					edd_update_payment_status( $payment_id, 'failed' );

					return 'OK: MARKED AS FAILED';
				}
				else {
				    return 'OK: IGNORED ' . ($trans_status == self::TRANSACTION_STATUS_CANCELLED ? 'TRANSACTION_STATUS_CANCELLED' : 'TRANSACTION_STATUS_REJECTED');
				}
			}

			return 'OK: IGNORED [' . $trans_status . ']';
		} else if ( $amount === $amount_home && $sig_compare === $sig ) {
		    if ( in_array($status_home, ['publish', 'future']) ) {
		        return 'OK: IGNORED TRANSACTION_STATUS_COMPLETE'; // payment already processed
            }

			edd_insert_payment_note( $payment_id, __( 'PayU transaction completed successfully', BPMJ_EDDPAYU_DOMAIN ) );
			edd_update_payment_status( $payment_id, 'completed' );

			return 'OK: MARKED AS COMPLETED';
		}
		
		return 'ERROR: INVALID AMOUNT/SIG';
	}

	/**
	 * @param string $session_id
	 * @param string $ts
	 * @param string $sig
	 */
	protected function process_server_response( $session_id, $ts, $sig ) {
	    EddExtensions::instance()->log('process_server_response session_id ' . $session_id);
		$sig_compare = md5( $this->pos_id . $session_id . $ts . $this->key2 );

		if ( $sig_compare === $sig ) {
		    $message = $this->receive_payment_mutex( $session_id );
		}
		else {
		    $message = 'ERROR: INVALID SIG';
		}
		EddExtensions::instance()->log('process_server_response mess ' . $message);
		
		if ( 'OK' === substr( $message, 0, 2 ) ) {
		    header( "HTTP/1.1 200 OK" );
		    exit('OK');
		} else {
		    header( "HTTP/1.1 400 Bad Request" );
		}
		
		exit;
	}
}
