<?php

namespace bpmj\wp\eddpayu\service;

use bpmj\wpidea\assets\Assets_Dir;

class EddExtensions {
	/**
	 * @var EddExtensions
	 */
	private static $instance;

	private function __construct() {
		add_action( 'wp_ajax_bpmj_eddpayu_validate_checkout_form', array( $this, 'ajax_validate_checkout_form' ) );
		add_action( 'wp_ajax_nopriv_bpmj_eddpayu_validate_checkout_form', array(
			$this,
			'ajax_validate_checkout_form'
		) );
		add_action( 'wp_ajax_bpmj_eddpayu_get_purchase_submit_html', array( $this, 'ajax_get_purchase_submit_html' ) );
		add_action( 'wp_ajax_nopriv_bpmj_eddpayu_get_purchase_submit_html', array(
			$this,
			'ajax_get_purchase_submit_html'
		) );
		add_action( 'edd_insert_payment', array( $this, 'hook_clear_auto_discounts' ) );
		add_action( 'edd_checkout_error_checks', array( $this, 'hook_check_auto_discounts' ) );
	}

	/**
	 * @return EddExtensions
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new static();
		}

		return self::$instance;
	}

	/**
	 * This method implements form validation phase from edd_process_purchase_form and should remain 100% compatible
	 * with first section of that function
	 *
	 * @see edd_process_purchase_form
	 */
	public function ajax_validate_checkout_form() {
		do_action( 'edd_pre_process_purchase' );

		// Make sure the cart isn't empty
		if ( ! edd_get_cart_contents() && ! edd_cart_has_fees() ) {
			$valid_data = false;
			edd_set_error( 'empty_cart', __( 'Your cart is empty', 'easy-digital-downloads' ) );
		} else {
			// Validate the form $_POST data
			$valid_data = edd_purchase_form_validate_fields();

			// Allow themes and plugins to hook to errors
			do_action( 'edd_checkout_error_checks', $valid_data, $_POST );
		}

		$is_ajax = isset( $_POST[ 'edd_ajax' ] );

		// Process the login form
		if ( isset( $_POST[ 'edd_login_submit' ] ) ) {
			edd_process_purchase_login();
		}

		// Validate the user
		$user = edd_get_purchase_form_user( $valid_data );

		// Let extensions validate fields after user is logged in if user has used login/registration form
		do_action( 'edd_checkout_user_error_checks', $user, $valid_data, $_POST );

		if ( false === $valid_data || edd_get_errors() || ! $user ) {
			if ( $is_ajax ) {
				do_action( 'edd_ajax_checkout_errors' );
				edd_die();
			}
		}

		echo 'success';
		edd_die();
	}

	/**
	 * @param string $user_email
	 *
	 * @return string
	 */
	public function generate_purchase_key( $user_email ) {
		$auth_key     = defined( 'AUTH_KEY' ) ? AUTH_KEY : '';
		$purchase_key = strtolower( md5( $user_email . date( 'Y-m-d H:i:s' ) . $auth_key . uniqid( 'edd', true ) ) );

		return $purchase_key;
	}

	/**
	 * When the payment is registered, we check if it uses an "EDD Autodiscount". If it does, that autodiscount gets
	 * deleted as it's not needed anymore
	 *
	 * @param int $payment_id
	 */
	public function hook_clear_auto_discounts( $payment_id ) {
		$payment = new \EDD_Payment( $payment_id );
		foreach ( explode( ',', $payment->discounts ) as $discount_raw ) {
			$discount_code = trim( $discount_raw );
			/** @var \EDD_Discount $discount */
			$discount = edd_get_discount_by_code( $discount_code );
			if ( false !== $discount && '1' === get_post_meta( $discount->ID, '_edd_auto_discount', true ) ) {
				edd_remove_discount( $discount->ID );
			}
		}
	}

	/**
	 * Checks if AUTODSCNT code is used in the cart and, if so, the user tried to use other discount codes
	 *
	 * @param array $data
	 */
	public function hook_check_auto_discounts( $data ) {
		$discounts           = explode( ',', $data[ 'discount' ] );
		$auto_discount_found = false;
		foreach ( explode( ',', $data[ 'discount' ] ) as $discount_raw ) {
			$discount_code = trim( $discount_raw );
			/** @var \EDD_Discount $discount */
			$discount = edd_get_discount_by_code( $discount_code );
			if ( false !== $discount && '1' === get_post_meta( $discount->ID, '_edd_auto_discount', true ) ) {
				$auto_discount_found = true;
				break;
			}
		}
		if ( $auto_discount_found && count( $discounts ) > 1 ) {
			edd_set_error( 'edd-cannot-have-other-discount-codes', __( 'You cannot use other discount codes when AUTODSCNT code is active during checkout. Remove AUTODSCNT code or all other discount codes to continue.', BPMJ_EDDPAYU_DOMAIN ) );
		}
	}

	/**
	 * @param array $cart_contents
	 *
	 * @return array
	 */
	public function get_cart_discount_details( $cart_contents = null ) {
		$cart_contents    = is_array( $cart_contents ) ? $cart_contents : edd_get_cart_contents();
		$discount_details = array();
		if ( empty( $cart_contents ) ) {
			return $discount_details;
		}

		$length              = count( $cart_contents ) - 1;
		$is_last_cart_item   = false;
		$flat_discount_total = 0.00;
		$discounts           = edd_get_cart_discounts();

		if ( empty( $discounts ) ) {
			return $discount_details;
		}

		$last_discount         = $discounts[ count( $discounts ) - 1 ];
		$is_last_discount_flat = 'flat' === edd_get_discount_type( edd_get_discount_id_by_code( $last_discount ) );

		foreach ( $cart_contents as $key => $item ) {
			if ( $key >= $length ) {
				$is_last_cart_item = true;
			}

			$item[ 'quantity' ] = edd_item_quantities_enabled() ? absint( $item[ 'quantity' ] ) : 1;
			if ( empty( $item[ 'quantity' ] ) ) {
				continue;
			}

			if ( ! isset( $item[ 'options' ] ) ) {
				$item[ 'options' ] = array();
			}

			$price = edd_get_cart_item_price( $item[ 'id' ], $item[ 'options' ] );

			if ( $discounts ) {
				foreach ( $discounts as $discount ) {
					$code_id           = edd_get_discount_id_by_code( $discount );
					$discounted_amount = 0.00;

					// Check discount exists
					if ( ! $code_id ) {
						continue;
					}

					$reqs              = edd_get_discount_product_reqs( $code_id );
					$excluded_products = edd_get_discount_excluded_products( $code_id );

					// Make sure requirements are set and that this discount shouldn't apply to the whole cart
					if ( ! empty( $reqs ) && edd_is_discount_not_global( $code_id ) ) {
						// This is a product(s) specific discount
						foreach ( $reqs as $download_id ) {
							if ( $download_id == $item[ 'id' ] && ! in_array( $item[ 'id' ], $excluded_products ) ) {
								$discounted_amount = $price - edd_get_discounted_amount( $discount, $price );
							}
						}
					} else {
						// This is a global cart discount
						if ( ! in_array( $item[ 'id' ], $excluded_products ) ) {
							if ( 'flat' === edd_get_discount_type( $code_id ) ) {
								/* *
								 * In order to correctly record individual item amounts, global flat rate discounts
								 * are distributed across all cart items. The discount amount is divided by the number
								 * of items in the cart and then a portion is evenly applied to each cart item
								 */
								$items_subtotal = 0.00;
								$cart_items     = $cart_contents;
								foreach ( $cart_items as $cart_item ) {
									if ( ! in_array( $cart_item[ 'id' ], $excluded_products ) ) {
										$item_price     = edd_get_cart_item_price( $cart_item[ 'id' ], $cart_item[ 'options' ] );
										$items_subtotal += $item_price * $cart_item[ 'quantity' ];
									}
								}

								$subtotal_percent  = ( ( $price * $item[ 'quantity' ] ) / $items_subtotal );
								$code_amount       = edd_get_discount_amount( $code_id );
								$discounted_amount = $code_amount * $subtotal_percent;

								$flat_discount_total += round( $discounted_amount, edd_currency_decimal_filter() );

								if ( $is_last_cart_item && $flat_discount_total < $code_amount ) {
									$adjustment        = $code_amount - $flat_discount_total;
									$discounted_amount += $adjustment;
								}
							} else {
								$discounted_amount = $price - edd_get_discounted_amount( $discount, $price );
							}
						}
					}

//					if ( 'flat' !== edd_get_discount_type( $code_id ) ) {
					if ( ! $is_last_discount_flat ) {
						/*
						 * This is needed due to a bug in EDD that occurs when there are a mixture of fixed and
						 * percentage discounts applied to the cart and quantities are enabled. If the last discount
						 * is not flat then total discount amount is multiplied by quantity regardless of individual
						 * discount types.
						 */
						$discounted_amount = $discounted_amount * $item[ 'quantity' ];
					}

					if ( ! isset( $discount_details[ $item[ 'id' ] ] ) ) {
						$discount_details[ $item[ 'id' ] ] = array();
					}
					$discount_details[ $item[ 'id' ] ][ $discount ] = $discounted_amount;
				}
			}
		}

		return $discount_details;
	}

	/**
	 * @param $payment_id
	 */
	public function correct_discounts( $payment_id ) {
		$payment          = new \EDD_Payment( $payment_id );
		$cart_details     = $payment->cart_details;
		$discount_details = get_post_meta( $payment_id, '_payu_discount_details', true );

		$discounts = $payment->discounts;

		if ( empty( $cart_details ) || empty( $discount_details ) || 'none' === $discounts ) {
			if ( 'none' !== $payment->discounts ) {
				$payment->discounts = 'none';
				$payment->save();
			}

			return;
		}

		if ( ! is_array( $discounts ) ) {
			$discounts = array_map( 'trim', explode( ',', $discounts ) );
		}

		if ( EDD()->customers->installed() ) {
			$customer_or_user_id = $payment->customer_id;
		} else {
			$customer_or_user_id = edd_get_payment_user_id( $payment_id );
		}

		$discounts_to_remove = array();
		foreach ( $discounts as $discount_code ) {
			$code_id = edd_get_discount_id_by_code( $discount_code );
			if ( ! edd_is_discount_active( $code_id, true, false )
			     || ! edd_is_discount_started( $code_id, false )
			     || edd_is_discount_maxed_out( $code_id, false )
			     || $this->edd_is_discount_used( $discount_code, $code_id, $customer_or_user_id, $payment_id )
			) {
				$discounts_to_remove[] = $discount_code;
			}
		}

		if ( empty( $discounts_to_remove ) ) {
			// All discounts valid - nothing to do
			return;
		}

		$new_subtotal = 0.0;
		$new_tax      = 0.0;

		foreach ( $cart_details as $cart_index => &$cart_item ) {
			$item_discounts      = isset( $discount_details[ $cart_item[ 'id' ] ] ) ? $discount_details[ $cart_item[ 'id' ] ] : array();
			$new_discount_amount = $cart_item[ 'discount' ];
			foreach ( $discounts_to_remove as $discount_code ) {
				$amount = isset( $item_discounts[ $discount_code ] ) ? $item_discounts[ $discount_code ] : 0;
				if ( ! $amount ) {
					continue;
				}
				$new_discount_amount -= $amount;
			}
			if ( $new_discount_amount < 0 ) {
				$new_discount_amount = 0;
			} else {
				$new_discount_amount = round( $new_discount_amount, edd_currency_decimal_filter() );
			}
			if ( $cart_item[ 'discount' ] !== $new_discount_amount ) {
				// Unfortunately EDD doesn't provide any shortcuts for this - we need to calculate these things from scratch
				$subtotal = $cart_item[ 'item_price' ] * $cart_item[ 'quantity' ];
				$tax      = edd_get_cart_item_tax( $cart_item[ 'id' ], $cart_item[ 'item_number' ][ 'options' ], $subtotal - $new_discount_amount );
				foreach ( $cart_item[ 'fees' ] as $fee ) {
					$subtotal += (float) $fee[ 'amount' ];
				}
				if ( edd_prices_include_tax() ) {
					$subtotal -= round( $tax, edd_currency_decimal_filter() );
				}
				$total = $subtotal - $new_discount_amount + $tax;
				if ( $total < 0 ) {
					$total = 0;
				}
				$cart_item[ 'discount' ] = $new_discount_amount;
				$cart_item[ 'subtotal' ] = round( $subtotal, edd_currency_decimal_filter() );
				$cart_item[ 'tax' ]      = round( $tax, edd_currency_decimal_filter() );
				$cart_item[ 'price' ]    = round( $total, edd_currency_decimal_filter() );
				$new_subtotal            += $cart_item[ 'subtotal' ] - $new_discount_amount;
				$new_tax                 += $cart_item[ 'tax' ];
			}
		}
		$new_discounts = array_diff( $discounts, $discounts_to_remove );
		if ( empty( $new_discounts ) ) {
			$payment->discounts = 'none';
		} else {
			$payment->discounts = implode( ',', $new_discounts );
		}
		$payment->subtotal = $new_subtotal;
		$payment->tax      = $new_tax;
		$payment->total    = $payment->subtotal + $payment->fees_total + $payment->tax;

		$payment->save();
		$payment_meta                   = $payment->get_meta();
		$payment_meta[ 'cart_details' ] = $cart_details;
		$payment->update_meta( '_edd_payment_meta', $payment_meta );
	}

	/**
	 * Output standard purchase submit HTML
	 */
	public function ajax_get_purchase_submit_html() {
		edd_checkout_submit();
		wp_die();
	}

	/**
	 * This is @see edd_is_discount_used() replacement. Unfortunately currently (up to version 2.7.6) EDD's version
	 * of this is buggy and doesn't work as expected
	 *
	 * @param string $code
	 * @param int $code_id
	 * @param string $user
	 *
	 * @return bool
	 */
	protected function edd_is_discount_used( $code, $code_id, $user = '', $ignore_payment_id = null ) {
		$result = false;
		if ( edd_discount_is_single_use( $code_id ) ) {
			$payments = array();

			if ( EDD()->customers->installed() ) {
				$customer = new \EDD_Customer( $user );

				$payments = explode( ',', $customer->payment_ids );
			} else {
				$user_found = false;

				if ( is_email( $user ) ) {
					$user_found = true; // All we need is the email
					$key        = '_edd_payment_user_email';
					$value      = $user;
				} else {
					$user_data = get_user_by( 'login', $user );

					if ( $user_data ) {
						$user_found = true;
						$key        = '_edd_payment_user_id';
						$value      = $user_data->ID;
					}
				}

				if ( $user_found ) {
					$query_args = array(
						'post_type'  => 'edd_payment',
						'meta_query' => array(
							array(
								'key'     => $key,
								'value'   => $value,
								'compare' => '='
							)
						),
						'fields'     => 'ids'
					);

					$payments = get_posts( $query_args ); // Get all payments with matching email
				}
			}

			if ( $payments ) {
				foreach ( $payments as $payment ) {
					$payment = new \EDD_Payment( $payment );

					if ( $ignore_payment_id === $payment->ID ) {
						continue;
					}

					if ( empty( $payment->discounts ) || 'none' === $payment->discounts ) {
						continue;
					}

					if ( in_array( $payment->status, array( 'abandoned', 'failed' ) ) ) {
						continue;
					}

					$discounts = array_map( 'strtolower', explode( ',', $payment->discounts ) );

					if ( is_array( $discounts ) ) {
						if ( in_array( strtolower( $code ), $discounts ) ) {
							$result = true;
							break;
						}
					}
				}
			}
		}

		/**
		 * Filters if the discount is used or not.
		 *
		 * @since 2.7
		 *
		 * @param bool $return If the discount is used or not.
		 * @param int $ID Discount ID.
		 * @param string $user User info.
		 */
		return apply_filters( 'edd_is_discount_used', $result, $code_id, $user );
	}

	/**
	 * @param int $payment_id
	 * @param mixed $data
	 * @param string $title
	 * @param bool $debug_only
	 */
	public function add_payment_note( $payment_id, $data, $title = '', $debug_only = false ) {
		global $edd_options;

		if ( $debug_only && ( empty( $edd_options[ 'payu_enable_debug' ] ) || '-1' === $edd_options[ 'payu_enable_debug' ] ) ) {
			return;
		}

		if ( is_string( $data ) ) {
			$note = $data;
		} else if ( defined( 'JSON_PRETTY_PRINT' ) ) {
			$note = json_encode( $data, JSON_PRETTY_PRINT );
		} else {
			$note = json_decode( $data );
		}
		if ( $title ) {
			$note = $title . ': ' . $note;
		}
		$note_id = edd_insert_payment_note( $payment_id, $note );
		if ( $debug_only ) {
			update_comment_meta( $note_id, 'payu_debug', 1 );
		}
	}

	/**
	 * @param int $payment_id
	 * @param mixed $data
	 * @param string $title
	 */
	public function add_debug_note( $payment_id, $data, $title = '' ) {
		$this->add_payment_note( $payment_id, $data, $title, true );
	}
	
	public function log(string $message) {
	    if ( !defined('BPMJ_LOGS_PAYU') || !BPMJ_LOGS_PAYU ) {
	        return;
	    }
	    $log_dir = dirname(__FILE__) . '/../../../../../../' . Assets_Dir::EXTERNAL_DIR_NAME . '/logs/';
	    if( !file_exists($log_dir) ) {
	        mkdir($log_dir, 0700);
	    }
	    if( !substr_compare($message, "\n", -1) == 0 ) {
	        $message .= "\n";
	    }
	    error_log(date('Y-m-d H:i:s ') . $message, 3, $log_dir . 'payu_' . date('Y_m') . '.log');
	}

}