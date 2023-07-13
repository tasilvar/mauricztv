<?php

/*
  Plugin Name: Easy Digital Downloads - Guest Buying Patch
  Plugin URI:
  Description:
  Version: 1.0
  Author: upSell.pl & Better Profits
  Author URI: http://upsell.pl
 */

if ( ! defined( 'BPMJ_UPSELL_STORE_URL' ) ) {
	define( 'BPMJ_UPSELL_STORE_URL', 'http://upsell.pl' );
}
define( 'BPMJ_GBP_EDD_NAME', 'Easy Digital Downloads - Guest Buying Patch' );


/*
 * Podmiana ID Gościa na ID użytkownika w tablicy $payment_meta
 */

add_filter( 'edd_payment_meta', 'bpmj_gbp_edd_mod_user_info', 10, 1 );

function bpmj_gbp_edd_mod_user_info( $payment_meta ) {

	// Wykonaj kod gdy użytkownik nie jest zalogowany
	if ( ! is_user_logged_in() ) {

		// E-mail podany w formularzu
		$user_email = '';
		if ( ! empty( $payment_meta[ 'email' ] ) ) {
			$user_email = $payment_meta[ 'email' ];
		} else if ( ! empty( $payment_meta[ 'user_info' ] ) && ! empty( $payment_meta[ 'user_info' ][ 'email' ] ) ) {
			$user_email = $payment_meta[ 'user_info' ][ "email" ];
		}

		if ( $user_email ) {
			// Pobiera użytkownika z bazy na podstawie powyższego e-maila
			$user = get_user_by( 'email', $user_email );

			// Sprawdż, czy użytkownik istnieje
			if ( $user !== false ) {
				$payment_meta[ 'user_info' ][ "id" ] = (int) $user->ID;
			}
		}
	}

	return $payment_meta;
}

/*
 * Nadpisanie ID użytkownika w post_meta ( _edd_payment_user_id )
 */

add_action( 'edd_insert_payment', 'bpmj_gbp_edd_update_user_id', 10, 2 );

function bpmj_gbp_edd_update_user_id( $payment, $payment_data ) {

	// Wykonaj kod gdy użytkownik nie jest zalogowany
	if ( ! is_user_logged_in() ) {

		// E-mail podany w formularzu
		$user_email = $payment_data[ 'user_email' ];

		// Pobiera użytkownika z bazy na podstawie powyższego e-maila
		$user = get_user_by( 'email', $user_email );

		// Sprawdż, czy użytkownik istnieje
		if ( $user !== false ) {
			// Nadpisz ID użytkownika
			update_post_meta( $payment, '_edd_payment_user_id', (int) $user->ID );
		}
	}
}

/*
 * FIX - ( Wyświetlał się komunikat "Sorry, trouble retrieving payment receipt." zamiast strony podsumowującej zamówienie ) 
 * 
 * PRZYCZYNA BŁĘDU
 * Domyślnie EDD zabezpiecza wyświetlenie strony końcowej dla niepożądanego użytkownika. 
 * Niezalogowanemu użytkownikowi zostanie wyświetlona strona, jeżeli zostanie mu nadane ID "0" lub "-1" - ID Gościa
 * Wtyczka EDD Guest Buying Patch nadpisuje ID Gościa, gdy skojarzy e-mail z istniejącym użytkownikiem.
 * Przez to nie został spełniony warunek logiczny:
 * ( $customer_id == 0 || $customer_id == '-1' ) && ! is_user_logged_in() && edd_get_purchase_session() )
 * Aby naprawić ten problem, w filtrze dopisywany jest nowy warunek logiczny, uwzględniający inne ID niż "0" lub "-1".
 */

add_filter( 'edd_user_can_view_receipt', 'bpmj_gbp_edd_user_can_view_receipt', 10, 1 );

function bpmj_gbp_edd_user_can_view_receipt( $user_can_view ) {

	// Pobieranie klucza płatności
	$session     = edd_get_purchase_session();
	$payment_key = $session[ 'purchase_key' ] ?? null;

	// Nie znaleziono klucza, zwróć zmienną bez modyfikacji
	if ( ! isset( $payment_key ) ) {
		return $user_can_view;
	}

	// ID zamówienia
	$purchase_id = edd_get_purchase_id_by_key( $payment_key );

	// ID użytkownika
	$customer_id = edd_get_payment_user_id( $purchase_id );


	// Dodaje nowe warunki logiczne, które umożliwią wyświetlenie strony podsumowującej zamówienie.
	$user_can_view = $user_can_view || ( ( $customer_id > 0 ) && ! is_user_logged_in() && edd_get_purchase_session() );

	return $user_can_view;
}
