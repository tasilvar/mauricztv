<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//add_action('woocommerce_payment_complete', 'bpmj_wpfa_on_woo_complete_purchase'); // nie działa z bramką transferuj.pl (nie wywyołują payment_complete)
add_action( 'woocommerce_order_status_completed', 'bpmj_wpfa_on_woo_complete_purchase' ); // alternatywa dla powyższego - do ustawień?
//add_action( 'wp_loaded', 'bpmj_wpfa_on_woo_tst' );

function bpmj_wpfa_on_woo_tst() {
	bpmj_wpfa_on_woo_complete_purchase( 169 );
}

/**
 * @param $order_id
 */
function bpmj_wpfa_on_woo_complete_purchase( $order_id ) {
	global $bpmj_wpfa_settings;

	$order = new WC_Order( $order_id );

	$shipping = is_callable( array(
		$order,
		'get_shipping_total'
	) ) ? $order->get_shipping_total() : $order->get_total_shipping();
	$total    = round( $order->get_total() - $shipping, 2 );

	// Jeśli kwota do zapłaty wynosi 0 - koniec
	if ( 0 == $total ) {
		return;
	}

	$invoice_object = new BPMJ_WP_Fakturownia();
	$invoice_object->set_fakturownia_product_id_resolver( function ( BPMJ_Base_Invoice_Item $item, BPMJ_WP_Fakturownia $invoice_object ) {
		$product_id = $item->get_additional_data( 'woo_product_id' );
		$product    = wc_get_product( $product_id );

		if ( $product instanceof WC_Product ) {
			$product_id             = version_compare( constant( 'WC_VERSION' ), '2.7', '<' ) && $product->get_variation_id() ? $product->get_variation_id() : $product->get_id();
			$sku                    = get_post_meta( $product_id, '_sku', true );
			$fakturownia_product_id = get_post_meta( $product_id, '_bpmj_wpfa_product_id', true );
			$fakturownia_product_id = $invoice_object->create_modify_product( $item->get_name(), $sku, $item->get_gross_unit_price_after_discount(), $item->get_tax_rate(), $fakturownia_product_id );
			if ( $fakturownia_product_id ) {
				update_post_meta( $product_id, '_bpmj_wpfa_product_id', $fakturownia_product_id );
				update_post_meta( $product_id, '_bpmj_wpfa_update_product', 'yes' );
				delete_transient( 'bpmj_wpfa_products_options' );
			}

			return $fakturownia_product_id;
		}

		return null;
	} );
	$invoice_factory = BPMJ_Invoice_Factory_Woocommerce::factory( $order, $bpmj_wpfa_settings, $invoice_object );
	if ( false === $invoice_factory->create_invoice_object() ) {
		return;
	}

	$invoice_factory->store_invoice( array(
		'payment_type' => strip_tags( get_post_meta( $order_id, '_payment_method_title', true ) ),
	) );
}

/**
 * Zwraca stawkę VAT dla podanego produktu
 *
 * @param WC_Product $product
 *
 * @return string
 */
function bpmj_wpfa_woo_get_tax_rate( WC_Product $product ) {
	global $bpmj_wpfa_settings;

	$default_vat = isset( $bpmj_wpfa_settings[ 'default_vat' ] ) && ! empty( $bpmj_wpfa_settings[ 'default_vat' ] ) ? $bpmj_wpfa_settings[ 'default_vat' ] : 23;
	$inv_type    = $bpmj_wpfa_settings[ 'invoice_type' ];

	if ( 'faktura-vat' === $inv_type ) { //FAKTURA VAT
		if ( 'yes' === get_option( 'woocommerce_calc_taxes' ) ) {
			$tax_info = array_shift( WC_Tax::get_rates( $product->get_tax_class() ) );
			if ( isset( $tax_info[ 'rate' ] ) ) {
				$VAT = (int) $tax_info[ 'rate' ];
			}
		}

		// Pobiera stawkę VAT z woo
		if ( ! isset( $VAT ) || ( empty( $VAT ) && $VAT != 0 ) ) {
			$tax_rate = $default_vat; // Jeżeli nie podano w woo, ustal domyślną
		} else {
			$tax_rate = $VAT; // Jeżeli zdefiniowano w woo, pobierz wartość
		}
	} elseif ( 'rachunek' === $inv_type ) { //RACHUNEK
		$tax_rate = 'zw'; // Przy rachunku podatek VAT = zw
	} else {
		$tax_rate = $default_vat;
	}

	return $tax_rate;
}
