<?php

// Zakoncz, jeżeli plik jest załadowany bezpośrednio
if ( !defined( 'ABSPATH' ) )
	exit;

/**
 * @return array
 */
function bpmj_wpfa_get_products_as_options() {
	$options = get_transient( 'bpmj_wpfa_products_options' );
	if ( false !== $options ) {
		return $options;
	}

	$fakturownia = new BPMJ_WP_Fakturownia();
	$products    = $fakturownia->get_products( );
	$options     = array( '' => '-- nowy produkt --' );
	if ( false !== $products ) {
		foreach ( $products as $product ) {
			$options[ $product->id ] = $product->name;
		}
	}

	set_transient( 'bpmj_wpfa_products_options', $options, 60 * 60 /* 1 hour */ );

	return $options;
}

/**
 * Zapisuje metadane produktu i ew. tworzy/aktualizuje produkt w serwisie Fakturownia.pl
 *
 * @param int $post_id
 * @param string $update_product_input wartość z $_POST, np. $_POST['_bpmj_wpfa_update_product']
 * @param string $product_id_input wartość z $_POST, np. $_POST['_bpmj_wpfa_product_id']
 * @param float $gross_unit_price
 * @param int $tax_rate
 * @param string $product_code
 */
function bpmj_wpfa_save_product_meta($post_id, $update_product_input, $product_id_input, $gross_unit_price, $tax_rate, $product_code = '') {
	$update_product = false;

	update_post_meta( $post_id, '_bpmj_wpfa_update_product', $update_product_input );
	$update_product = 'yes' === $update_product_input;

	$product_post = get_post( $post_id );

	if ( false !== $product_id_input ) {
		$fakturownia_product_id = $product_id_input;
		if ( ! $fakturownia_product_id || $update_product ) {
			$fakturownia            = new BPMJ_WP_Fakturownia();
			$fakturownia_product_id = $fakturownia->create_modify_product( $product_post->post_title, $product_code, $gross_unit_price, $tax_rate, $fakturownia_product_id );
		}
		update_post_meta( $post_id, '_bpmj_wpfa_product_id', $fakturownia_product_id );
	}

	delete_transient( 'bpmj_wpfa_products_options' );
}

/**
 * @param $string
 *
 * @return string
 */
function bpmj_wpfa_normalize_string_for_comparison( $string ) {
	return mb_strtolower( preg_replace( '/[^\w0-9]+/u', '',
		preg_replace( '/&#[0-9]+;/', '',
			$string ) ) );
}
