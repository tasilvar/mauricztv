<?php

namespace bpmj\wp\eddtpay\service;

class EddExtensions {
	/**
	 * @var EddExtensions
	 */
	private static $instance;

	private function __construct() {
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
			edd_set_error( 'edd-cannot-have-other-discount-codes', __( 'You cannot use other discount codes when AUTODSCNT code is active during checkout. Remove AUTODSCNT code or all other discount codes to continue.', 'edd-tpay' ) );
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
		$discount_details = get_post_meta( $payment_id, '_tpay_discount_details', true );

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
}