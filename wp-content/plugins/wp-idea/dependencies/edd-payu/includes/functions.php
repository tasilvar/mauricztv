<?php
/**
 * Functions trapped inside "if ( function_exists ) { ... }" are intended to be reimplemented by other plugins
 * supporting the same features. It would be great if those implementations are exactly the same as these below,
 * including the "if" clause.
 */

if ( ! function_exists( 'edd_recurring_payments_enabled' ) ) {
	/**
	 * @return bool
	 */
	function edd_recurring_payments_enabled() {
		$enabled = true;

		return apply_filters( 'edd_recurring_payments_enabled', $enabled );
	}
}

if ( ! function_exists( 'edd_gateway_supports_recurring_payments' ) ) {
	/**
	 * @param $gateway
	 *
	 * @return array
	 */
	function edd_gateway_supports_recurring_payments( $gateway ) {
		$supports = edd_get_gateway_supports( $gateway );
		$ret      = in_array( 'recurring_payments', $supports );

		return apply_filters( 'edd_gateway_supports_recurring_payments', $ret, $gateway );
	}
}

if ( ! function_exists( '_edd_get_gateways_supporting_recurring_payments' ) ) {
	/**
	 * Helper function that filters the provided $gateways array
	 *
	 * @param $gateways
	 *
	 * @return array
	 */
	function _edd_get_gateways_supporting_recurring_payments( $gateways ) {
		$ret = array();
		foreach ( $gateways as $gateway => $gateway_spec ) {
			$supports = isset( $gateway_spec[ 'supports' ] ) ? $gateway_spec[ 'supports' ] : array();
			if ( in_array( 'recurring_payments', $supports ) ) {
				$ret[] = $gateway;
			}
		}

		return $ret;
	}
}

if ( ! function_exists( 'edd_get_gateways_supporting_recurring_payments' ) ) {
	/**
	 * @return array
	 */
	function edd_get_gateways_supporting_recurring_payments() {
		$gateways = edd_get_payment_gateways();
		$ret      = _edd_get_gateways_supporting_recurring_payments( $gateways );

		return apply_filters( 'edd_get_gateways_supporting_recurring_payments', $ret );
	}
}

if ( ! function_exists( 'edd_get_enabled_gateways_supporting_recurring_payments' ) ) {
	/**
	 * @return array
	 */
	function edd_get_enabled_gateways_supporting_recurring_payments() {
		$gateways = edd_get_enabled_payment_gateways();
		$ret      = _edd_get_gateways_supporting_recurring_payments( $gateways );

		return apply_filters( 'edd_get_enabled_gateways_supporting_recurring_payments', $ret );
	}
}

if ( ! function_exists( 'edd_any_gateway_supports_recurring_payments' ) ) {
	/**
	 * @return bool
	 */
	function edd_any_gateway_supports_recurring_payments() {
		$gateways = edd_get_gateways_supporting_recurring_payments();
		if ( empty( $gateways ) ) {
			return false;
		}

		return true;
	}
}

if ( ! function_exists( 'edd_any_enabled_gateway_supports_recurring_payments' ) ) {
	/**
	 * @return bool
	 */
	function edd_any_enabled_gateway_supports_recurring_payments() {
		$gateways = edd_get_enabled_gateways_supporting_recurring_payments();
		if ( empty( $gateways ) ) {
			return false;
		}

		return true;
	}
}

if ( ! function_exists( 'edd_recurring_payments_enabled_for_download' ) ) {
	/**
	 * Check if recurring payments are possible and enabled for the specified download
	 *
	 * @param int $download_id
	 * @param int $price_id
	 *
	 * @return bool
	 */
	function edd_recurring_payments_enabled_for_download( $download_id, $price_id = null ) {
		$enabled = false;
		if ( edd_recurring_payments_possible_for_download( $download_id, $price_id ) ) {
			if ( $price_id ) {
				$variable_prices = edd_get_variable_prices( $download_id );
				if ( isset( $variable_prices[ $price_id ][ 'recurring_payments_enabled' ] ) && '1' === $variable_prices[ $price_id ][ 'recurring_payments_enabled' ] ) {
					$enabled = true;
				}
			} else {
				$enabled = '1' === get_post_meta( $download_id, '_edd_recurring_payments_enabled', true );
			}
		}

		return apply_filters( 'edd_recurring_payments_enabled_for_download', $enabled, $download_id );
	}
}

if ( ! function_exists( 'edd_recurring_payments_possible_for_download' ) ) {
	/**
	 * Check if recurring payments are possible for the specified download
	 *
	 * @param int $download_id
	 * @param int $price_id
	 *
	 * @return bool
	 */
	function edd_recurring_payments_possible_for_download( $download_id, $price_id = null ) {
		$possible = edd_recurring_payments_enabled();
		if ( $possible && ! edd_any_enabled_gateway_supports_recurring_payments() ) {
			$possible = false;
		}

		return apply_filters( 'edd_recurring_payments_possible_for_download', $possible, $download_id, $price_id );
	}
}

if ( ! function_exists( 'edd_download_is_recurring' ) ) {
	/**
	 * Check if the item is recurring and can be subject of recurring payments
	 *
	 * @param int $download_id
	 *
	 * @return bool
	 */
	function edd_download_is_recurring( $download_id ) {
		$is_recurring = false;

		return apply_filters( 'edd_download_is_recurring', $is_recurring, $download_id );
	}
}

if ( ! function_exists( 'edd_recurring_get_interval_units' ) ) {
	/**
	 * @return array
	 */
	function edd_recurring_get_interval_units() {
		$units = array(
			'days'   => __( 'Days', BPMJ_EDDPAYU_DOMAIN ),
			'weeks'  => __( 'Weeks', BPMJ_EDDPAYU_DOMAIN ),
			'months' => __( 'Months', BPMJ_EDDPAYU_DOMAIN ),
			'years'  => __( 'Years', BPMJ_EDDPAYU_DOMAIN ),
		);

		return apply_filters( 'edd_recurring_get_interval_units', $units );
	}
}

if ( ! function_exists( 'edd_recurring_get_interval' ) ) {
	/**
	 * @param int $download_id
	 * @param int $price_id
	 * @param bool $raw
	 *
	 * @return bool|mixed
	 */
	function edd_recurring_get_interval( $download_id, $price_id = 0, $raw = false ) {
		if ( $price_id ) {
			$variable_prices  = edd_get_variable_prices( $download_id );
			$payment_interval = isset( $variable_prices[ $price_id ][ 'recurring_payments_interval' ] )
				? $variable_prices[ $price_id ][ 'recurring_payments_interval' ]
				: false;
		} else {
			$payment_interval = get_post_meta( $download_id, '_edd_recurring_payments_interval', true );
		}

		$result = false;
		if ( $payment_interval && false !== strpos( $payment_interval, ' ' ) ) {
			list( $payment_interval_number, $payment_interval_unit ) = explode( ' ', $payment_interval );
			$payment_interval_number = (int) $payment_interval_number;
			if ( 0 < $payment_interval_number
			     && key_exists( $payment_interval_unit, edd_recurring_get_interval_units() )
			) {
				// Check if everything is ok with the value - we expect something like '30 days' or '2 months'
				if ( $raw ) {
					$result = $payment_interval;
				} else {
					$result = array( 'number' => $payment_interval_number, 'unit' => $payment_interval_unit );
				}
			}
		}

		return apply_filters( 'edd_recurring_get_interval', $result, $download_id, $price_id, $raw );
	}
}

if ( ! function_exists( 'edd_recurring_get_next_payment_date' ) ) {
	/**
	 * Gets next payment date for a download
	 *
	 * @param int $download_id
	 * @param int $price_id
	 * @param string $reference_date
	 *
	 * @return bool|string
	 */
	function edd_recurring_get_next_payment_date( $download_id, $price_id = 0, $reference_date = null ) {
		if ( ! edd_recurring_payments_enabled_for_download( $download_id, $price_id ) ) {
			return false;
		}
		$payment_interval = edd_recurring_get_interval( $download_id, $price_id );
		if ( false === $payment_interval ) {
			return false;
		}

		$reference_time    = $reference_date ? strtotime( $reference_date ) : time();
		$next_payment_date = date( 'Y-m-d', strtotime( '+' . $payment_interval[ 'number' ] . ' ' . $payment_interval[ 'unit' ], $reference_time ) );

		return apply_filters( 'edd_recurring_get_next_payment_date', $next_payment_date, $download_id, $price_id );
	}
}

if ( ! function_exists( 'edd_user_has_recurring_payments' ) ) {
	/**
	 * @param int $user_id
	 * @param int $download_id
	 * @param string $status
	 *
	 * @return bool
	 */
	function edd_user_has_recurring_payments( $user_id, $download_id = null, $status = null ) {
		$nearest_payment = edd_user_get_nearest_recurring_payment( $user_id, $download_id, $status );
		$result          = false;
		if ( $nearest_payment instanceof \EDD_Payment ) {
			$result = true;
		}

		return apply_filters( 'edd_user_has_recurring_payments', $result, $user_id, $download_id );
	}
}

if ( ! function_exists( 'edd_user_has_pending_recurring_payments' ) ) {
	/**
	 * @param int $user_id
	 * @param int $download_id
	 *
	 * @return bool
	 */
	function edd_user_has_pending_recurring_payments( $user_id, $download_id = null ) {
		$nearest_payment = edd_user_get_nearest_pending_recurring_payment( $user_id, $download_id );
		$result          = false;
		if ( $nearest_payment instanceof \EDD_Payment ) {
			$result = true;
		}

		return apply_filters( 'edd_user_has_pending_recurring_payments', $result, $user_id, $download_id );
	}
}

if ( ! function_exists( 'edd_user_get_nearest_recurring_payment' ) ) {
	/**
	 * @param int $user_id
	 * @param int $download_id
	 * @param string $status
	 *
	 * @return \EDD_Payment
	 */
	function edd_user_get_nearest_recurring_payment( $user_id, $download_id = null, $status = null ) {
		$gateway_meta_queries = apply_filters( 'edd_recurring_payment_meta_queries', array() );
		if ( empty( $gateway_meta_queries ) ) {
			return apply_filters( 'edd_user_get_nearest_recurring_payment', null, $user_id, $download_id );
		}
		$meta_query = array_merge( $gateway_meta_queries, array(
			'relation' => 'OR',
		) );

		$payment_query_args = array(
			'date_query' => array(
				array(
					'after'     => '-1 months',
					'before'    => '+5 years',
					'inclusive' => true,
				),
			),
			'meta_query' => array(
				'relation' => 'AND',
				$meta_query,
			),
			'number'     => $download_id ? - 1 : 1,
			'user'       => $user_id,
			'output'     => 'payments',
			'orderby'    => 'date',
			'order'      => 'ASC',
		);
		if ( $status ) {
			$payment_query_args[ 'status' ] = $status;
		}
		$payment_query_args = apply_filters( 'edd_user_has_recurring_payments_args', $payment_query_args );

		/** @var \EDD_Payment[] $payments */
		$payments = edd_get_payments( $payment_query_args );
		$result   = null;
		if ( $download_id ) {
			foreach ( $payments as $payment ) {
				if ( ! empty( $payment->downloads ) ) {
					foreach ( $payment->downloads as $download ) {
						if ( (int) $download[ 'id' ] === (int) $download_id
						     || edd_is_bundled_product( $download[ 'id' ] ) && in_array( $download_id, edd_get_bundled_products( $download[ 'id' ] ) ) ) {
							$result = $payment;
							break 2;
						}
					}
				}
			}
		} else if ( ! empty( $payments ) ) {
			$result = reset( $payments );
		}

		return apply_filters( 'edd_user_get_nearest_recurring_payment', $result, $user_id, $download_id );
	}
}

if ( ! function_exists( 'edd_user_get_nearest_pending_recurring_payment' ) ) {
	/**
	 * @param int $user_id
	 * @param int $download_id
	 *
	 * @return \EDD_Payment
	 */
	function edd_user_get_nearest_pending_recurring_payment( $user_id, $download_id = null ) {
		$result = edd_user_get_nearest_recurring_payment( $user_id, $download_id, 'pending' );

		return apply_filters( 'edd_user_get_nearest_pending_recurring_payment', $result, $user_id, $download_id );
	}
}

if ( ! function_exists( 'edd_cancel_subscription' ) ) {
	function edd_cancel_subscription( $data ) {
		if ( wp_verify_nonce( $data[ '_wpnonce' ], 'edd_payment_nonce' ) ) {

			$payment_id = absint( $data[ 'purchase_id' ] );

			if ( ! current_user_can( 'edit_shop_payments', $payment_id ) ) {
				wp_die( __( 'You do not have permission to edit this payment record', 'easy-digital-downloads' ), __( 'Error', 'easy-digital-downloads' ), array( 'response' => 403 ) );
			}

			edd_update_payment_status( $payment_id, 'revoked' );
			wp_redirect( add_query_arg( 'edd-message', 'subscription_cancelled', wp_get_referer() ) );
			edd_die();
		}
	}

	add_action( 'edd_cancel_subscription', 'edd_cancel_subscription' );
}

if ( ! function_exists( 'edd_cancel_subscription_messages' ) ) {
	function edd_cancel_subscription_messages() {
		if ( isset( $_GET[ 'edd-message' ] ) && 'subscription_cancelled' == $_GET[ 'edd-message' ] && current_user_can( 'edit_shop_payments' ) ) {
			add_settings_error( 'edd-notices', 'edd-subscription-cancelled', __( 'The subscription has been cancelled.', BPMJ_EDDPAYU_DOMAIN ), 'updated' );
			settings_errors( 'edd-notices' );
		}

	}

	add_action( 'admin_notices', 'edd_cancel_subscription_messages' );
}

if ( ! function_exists( 'edd_get_payment_charge_mode' ) ) {
	function edd_get_payment_charge_mode( $payment_id ) {
		return apply_filters( 'edd_get_payment_charge_mode', 'automatic', $payment_id );
	}
}

if ( ! function_exists( 'edd_direct_payment_to_gateway' ) ) {
	function edd_direct_payment_to_gateway() {
		if ( empty( $_GET[ 'edd_payment_to_gateway' ] ) ) {
			return;
		}

		$payment_key = $_GET[ 'edd_payment_to_gateway' ];
		$payment_id  = edd_get_purchase_id_by_key( $payment_key );
		if ( ! $payment_id ) {
			return;
		}

		$redirect_url = apply_filters( 'edd_direct_gateway_url', home_url(), $payment_id );
		wp_redirect( $redirect_url );
		edd_die();
	}

	add_action( 'init', 'edd_direct_payment_to_gateway' );
}

function eddpayu_maybe_redirect_to_failed_page( $current_request ) {

    if( !edd_is_success_page() ) {
        return;
    }
    
    if( $current_request->get_query_arg('payu_purchase_key') && $current_request->get_query_arg('error') === '501') {
        wp_redirect( edd_get_failed_transaction_uri() );
        exit;
    }
}
