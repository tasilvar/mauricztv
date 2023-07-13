<?php

/*
 * Tworzy JSON z danymi do wysyłki do fakturownia.pl po złożeniu zamówienia w EDD
 */

// Zakoncz, jeżeli plik jest załadowany bezpośrednio
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Zwraca wartość i typ rabatu, jeżeli został ustanowiony
 *
 * @param int $payment_id
 *
 * @return array|bool
 */
function bpmj_wpfa_edd_get_discount( $payment_id ) {

	$payment_meta = edd_get_payment_meta( $payment_id );

	if ( is_array( $payment_meta[ 'user_info' ] ) ) {
		$user_info = $payment_meta[ 'user_info' ][ 'discount' ];

		$code = $user_info;
	} else {
		$user_info = unserialize( $payment_meta[ 'user_info' ] );

		$code = $user_info[ 'discount' ];
	}


	if ( $code != 'none' ) {

		// ID kodu rabatowego
		$discount_id = edd_get_discount_id_by_code( $code );

		// Typ rabatu ( procentowy, czy całkowity)
		$discount_type = edd_get_discount_type( $discount_id );

		// Wartość rabatu
		$discount_amount = edd_get_discount_amount( $discount_id );

		$discount = array(
			'discount_type'   => $discount_type,
			'discount_amount' => $discount_amount
		);

		return $discount;
	} else {
		return false;
	}
}

add_action( 'edd_complete_purchase', 'bpmj_wpfa_on_edd_complete_purchase' );

/**
 * @param $payment_id
 */
function bpmj_wpfa_on_edd_complete_purchase( $payment_id ) {
	bpmj_wpfa_on_edd_complete_purchase_core( $payment_id, $payment_id );
}

/**
 * @param $payment_id
 * @param $parent_id
 */
function bpmj_wpfa_on_edd_complete_purchase_core( $payment_id, $parent_id ) {
	global $bpmj_wpfa_settings;

	// Jeśli kwota do zapłaty wynosi 0 - koniec
	if ( edd_get_payment_amount( $payment_id ) == '0.00' ) {
		return;
	}

	$invoice_object = new BPMJ_WP_Fakturownia();
	$invoice_object->set_fakturownia_product_id_resolver( function ( BPMJ_Base_Invoice_Item $item, BPMJ_WP_Fakturownia $invoice_object ) {
		$product_id = $item->get_additional_data( 'edd_product_id' );
		$price_id   = $item->get_additional_data( 'edd_price_id' );
		if ( ! $product_id ) {
			return null;
		}

		if ( $price_id ) {
			$prices = get_post_meta( $product_id, 'edd_variable_prices', true );
			if ( $prices && is_array( $prices ) ) {
				if ( isset( $prices[ $price_id ] ) ) {
					$fakturownia_product_id = $prices[ $price_id ][ '_bpmj_wpfa_product_id' ];
					$fakturownia_product_id = $invoice_object->create_modify_product( $item->get_name(), '', $item->get_gross_unit_price_after_discount(), $item->get_tax_rate(), $fakturownia_product_id );
					if ( $fakturownia_product_id ) {
						$prices[ $price_id ][ '_bpmj_wpfa_product_id' ] = $fakturownia_product_id;
						update_post_meta( $product_id, 'edd_variable_prices', $prices );
						delete_transient( 'bpmj_wpfa_products_options' );
					}

					return $fakturownia_product_id;
				}
			}
		} else {
			$fakturownia_product_id = get_post_meta( $product_id, '_bpmj_wpfa_product_id', true );
			$fakturownia_product_id = $invoice_object->create_modify_product( $item->get_name(), '', $item->get_gross_unit_price_after_discount(), $item->get_tax_rate(), $fakturownia_product_id );
			if ( $fakturownia_product_id ) {
				update_post_meta( $product_id, '_bpmj_wpfa_product_id', $fakturownia_product_id );
				update_post_meta( $product_id, '_bpmj_wpfa_update_product', 'yes' );
				delete_transient( 'bpmj_wpfa_products_options' );
			}

			return $fakturownia_product_id;
		}

		return null;
	} );
	$invoice_object->set_fakturownia_additional_info_resolver( function ( BPMJ_Base_Invoice_Item $item, BPMJ_WP_Fakturownia $invoice_object ) {
		$product_id = $item->get_additional_data( 'edd_product_id' );

		return $product_id ? get_post_meta( $product_id, 'bpmj_wpfa_add_info', true ) : null;
	} );
	$invoice_factory = BPMJ_Invoice_Factory_Edd::factory( $payment_id, $bpmj_wpfa_settings, $invoice_object );
	if ( false === $invoice_factory->create_invoice_object() ) {
		return;
	}

	$invoice_factory->store_invoice( array(
		'payment_type' => edd_get_gateway_checkout_label( edd_get_payment_gateway( $payment_id ) ),
	) );
}

/**
 * Integracja z Recurring Payments
 *
 * @param $payment
 * @param $parent_id
 * @param $amount
 * @param $txn_id
 * @param $unique_key
 */
function bpmj_wpfa_recurring_payment_received_notice( $payment, $parent_id, $amount, $txn_id, $unique_key ) {
	bpmj_wpfa_on_edd_complete_purchase_core( $payment, $parent_id );
}

add_action( 'edd_recurring_record_payment', 'bpmj_wpfa_recurring_payment_received_notice', 10, 5 );

/**
 * @param $download_id
 *
 * @return float|int|mixed|string
 */
function bpmj_wpfa_edd_get_price( $download_id ) {
	if ( edd_has_variable_prices( $download_id ) ) {
		$price = edd_get_lowest_price_option( $download_id );
		$price = edd_sanitize_amount( $price );
	} else {
		$price = edd_get_download_price( $download_id );
	}

	return $price;
}

/**
 * @param array $src
 */
function bpmj_wpfa_after_invoice_sent_to_customer( array $src ) {
	if ( isset( $src[ 'src' ] ) && 'edd' === $src[ 'src' ] && ! empty( $src[ 'id' ] ) ) {
		edd_insert_payment_note( $src[ 'id' ], sprintf( __( '%1$s: wysłano dokument do klienta', 'bpmj_wpfa' ), BPMJ_WPFA_NAME ) );
	}
}

add_action( 'bpmj_wpfa_after_invoice_sent_to_customer', 'bpmj_wpfa_after_invoice_sent_to_customer' );

/**
 * @param array $src
 * @param string $remote_invoice_number
 */
function bpmj_wpfa_after_invoice_created( array $src, $remote_invoice_number ) {
	if ( isset( $src[ 'src' ] ) && 'edd' === $src[ 'src' ] && ! empty( $src[ 'id' ] ) ) {
		edd_insert_payment_note( $src[ 'id' ], sprintf( __( '%1$s: wystawiono dokument o numerze %2$s', 'bpmj_wpfa' ), BPMJ_WPFA_NAME, $remote_invoice_number ) );
	}
}

add_action( 'bpmj_wpfa_after_invoice_created', 'bpmj_wpfa_after_invoice_created', 10, 2 );

/**
 * Zwraca stawkę VAT dla podanego produktu
 *
 * @param int $product_post_id
 *
 * @return string
 */
function bpmj_wpfa_edd_get_tax_rate( $product_post_id ) {
	global $bpmj_wpfa_settings;

	$default_vat = isset( $bpmj_wpfa_settings[ 'default_vat' ] ) && ! empty( $bpmj_wpfa_settings[ 'default_vat' ] ) ? $bpmj_wpfa_settings[ 'default_vat' ] : 23;
	$inv_type    = $bpmj_wpfa_settings[ 'invoice_type' ];

	if ( 'faktura-vat' === $inv_type ) { //FAKTURA VAT
		$VAT = get_post_meta( $product_post_id, 'bpmj_wpfa_vat', true );

		// Pobiera stawkę VAT z metaboxa
		if ( empty( $VAT ) && $VAT != 0 ) {
			$tax_rate = $default_vat; // Jeżeli nie podano w metaboxie, ustal domyślną
		} else {
			$tax_rate = $VAT; // Jeżeli zdefiniowano w metaboxie, pobierz wartość
		}
	} elseif ( 'rachunek' === $inv_type ) { //RACHUNEK
		$tax_rate = 'zw'; // Przy rachunku podatek VAT = zw
	} else {
		$tax_rate = $default_vat;
	}

	return $tax_rate;
}