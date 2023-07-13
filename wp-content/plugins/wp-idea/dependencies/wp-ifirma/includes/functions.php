<?php

/*
 * Funkcje związane z kursami
 */

// Zakoncz, jeżeli plik jest załadowany bezpośrednio
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * Zwraca wartość i typ rabatu, jeżeli został ustanowiony
 */

function bpmj_wpifirma_get_discount( $payment_id ) {

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

// usunięcie "krzaków"
function bpmj_wpifirma_remove_ent( $in, $replace ) {
	$entArr = array( '&#8222;', '&#8221;', '&quot;' );

	return html_entity_decode( str_replace( $entArr, $replace, $in ), ENT_COMPAT | ENT_HTML401, get_bloginfo( 'charset' ) );
}

// kodowanie html
function bpmj_wpifirma_htmlentities( $in ) {
	return htmlentities( $in, ENT_COMPAT | ENT_HTML401, get_bloginfo( 'charset' ) );
}