<?php

use bpmj\wpidea\helpers\Price_Formatting;

/*
 * Tutaj wykonują się operacje związane z wysyłką i odbiorem płatności do Przelewy24.pl
 */

/**
 * Weryfikacja danych przesłanych od przelewy24.pl
 *
 * Po udanej walidacji danych otrzymanych od przelewy24.pl następuje zmiana statusu płatności
 * na completed, co jest równoznaczne z udostępnieniem pliku dla klienta.
 */
function bpmj_p24_edd_check_przelewy24() {

	global $edd_options;
	// Sprawdzenie, czy odpowiedź jest przesłana metoda POST
	if ( isset( $_SERVER[ 'REQUEST_METHOD' ] ) && $_SERVER[ 'REQUEST_METHOD' ] != 'POST' ) {
		return;
	}


	// Sprawdzmy czy serwer odesłał wymagane zmienne w $_POST
	if ( !empty( $_POST[ 'p24_merchant_id' ] ) AND ! empty( $_POST[ 'p24_pos_id' ] ) AND ! empty( $_POST[ 'p24_session_id' ] ) AND ! empty( $_POST[ 'p24_amount' ] ) AND ! empty( $_POST[ 'p24_currency' ] ) AND ! empty( $_POST[ 'p24_order_id' ] ) AND ! empty( $_POST[ 'p24_method' ] ) AND ! empty( $_POST[ 'p24_statement' ] ) AND ! empty( $_POST[ 'p24_sign' ] ) ) {

		// Tutaj jesteśmy pewni, że jest do przelewy24.pl
		// Odebranie zmiennych z tablicy POST
		// Sprawdzenie czy Przelewy24.pl działają w trybie testowym
		if ( edd_is_test_mode() ) {
			$p24_testmode = true;
		} else {
			$p24_testmode = false;
		}

		$p24_crc = $edd_options[ 'przelewy24_pin' ];

		$P24 = new Przelewy24( $_POST[ 'p24_merchant_id' ], $_POST[ 'p24_pos_id' ], $p24_crc, $p24_testmode );

		foreach ( $_POST as $k => $v ) {
			$P24->addValue( $k, $v );
		}

		$res = $P24->trnVerify();

		$p24_session_id = trim( $_POST[ 'p24_session_id' ] );

		// Pobieramy ID zamówienia
		$purchase_id = edd_get_purchase_id_by_key( $p24_session_id );

		// jesli nie ma takiego order id, przerwij
		if ( empty( $purchase_id ) ) {
			exit( "Nie ma zamówienia z takim numerem control: " . $p24_session_id );
		} else if ( $res[ "error" ] == 0 ) {
			edd_insert_payment_note( $purchase_id, 'Płatność zrealizowana pomyślnie' );
			edd_update_payment_status( $purchase_id, 'completed' );
		} else {
			edd_insert_payment_note( $purchase_id, 'Błędna weryfikacja transakcji' );
			exit( 'Błędna weryfikacja transakcji' );
		}
	}
}

add_action( 'init', 'bpmj_p24_edd_check_przelewy24' );

/**
 *  Funkcja zapisuje dane płatności. Następnie przekierowuje na https://secure.przelewy24.pl
 *  
 */
function bpmj_p24_edd_process_payment( $purchase_data ) {

	global $edd_options;

	// Dane zamówienia do zapisu
	$payment_data = array(
		'price'			 => $purchase_data[ 'price' ],
		'date'			 => $purchase_data[ 'date' ],
		'user_email'	 => $purchase_data[ 'user_email' ],
		'purchase_key'	 => $purchase_data[ 'purchase_key' ],
		'currency'		 => $edd_options[ 'currency' ],
		'downloads'		 => $purchase_data[ 'downloads' ],
		'cart_details'	 => $purchase_data[ 'cart_details' ],
		'user_info'		 => $purchase_data[ 'user_info' ],
		'status'		 => 'pending'
	);

	$payment = edd_insert_payment( $payment_data );

	// Sprawdza, czy dane zostały zapisane
	if ( !$payment ) {
		// Wyświetla informacje o błędzie
		edd_record_gateway_error( __( 'Błąd płatności', 'bpmj_p24_edd' ), sprintf( __( 'Błąd płatności przed wysłaniem ich do Przelewy24.pl.  Data: %s', 'bpmj_p24_edd' ), json_encode( $payment_data ) ), $payment );
		// Powraca na stronę płatności
		edd_send_back_to_checkout( '?payment-mode=' . $purchase_data[ 'post_data' ][ 'edd-gateway' ] );
	} else {

		// Sprawdzenie czy Przelewy24.pl działają w trybie testowym
		if ( edd_is_test_mode() ) {
			$p24_testmode = true;
		} else {
			$p24_testmode = false;
		}

		// URL powrotny klienta
		$return_url = add_query_arg( 'payment-confirmation', 'przelewy24', get_permalink( $edd_options[ 'success_page' ] ) );

		// wyn_url - wynikowy adres zwrotny URL, na który Przelewy24 wyślę tablice POST
		$listener_url = add_query_arg( 'payment-status', 'przelewy24', home_url('/') );

		//Kwota do zapłaty
        $cart_summary = Price_Formatting::round_and_format_to_int($purchase_data[ 'price' ], Price_Formatting::MULTIPLY_BY_100);

		$p24_crc		 = $edd_options[ 'przelewy24_pin' ];
		$p24_shopid		 = intval( $edd_options[ 'przelewy24_id' ] );
		$p24_session_id	 = $purchase_data[ 'purchase_key' ];
		
		$name = $purchase_data[ 'user_info' ][ 'first_name' ];
		if( !empty( $purchase_data[ 'user_info' ][ 'last_name' ] ) ) {
			$name .= ' ' . $purchase_data[ 'user_info' ][ 'last_name' ];
		}

		// Przygotowanie tablicy danych do przesłania 
		$przelewy24_args = array(
			'p24_merchant_id'	 => $p24_shopid,
			'p24_pos_id'		 => $p24_shopid,
			'p24_session_id'	 => $p24_session_id,
			'p24_amount'		 => $cart_summary,
			'p24_currency'		 => $payment_data[ 'currency' ],
			'p24_description'	 => apply_filters( 'bpmj_edd_p24_gateway_args_description', get_bloginfo( 'name' ) . ' - opłata za zamówienie nr ' . $payment, $payment ),
			'p24_email'			 => $payment_data[ 'user_email' ],
			'p24_client'		 => $name,
			'p24_language'		 => 'pl',
			'p24_country'		 => 'PL',
			'p24_url_return'	 => $return_url,
			'p24_url_status'	 => $listener_url,
			'p24_encoding'		 => 'UTF-8',
			'p24_api_version'	 => P24_VERSION
		);

		$przelewy24_args = apply_filters( 'bpmj_edd_p24_gateway_args', $przelewy24_args, $purchase_data );

		$P24 = new Przelewy24( $p24_shopid, $p24_shopid, $p24_crc, $p24_testmode );

		foreach ( $przelewy24_args as $k => $v ) {
			$P24->addValue( $k, $v );
		}

		$res = $P24->trnRegister( true );

		echo '<pre>RESPONSE:' . print_r( $res, true ) . '</pre>';

		if ( $res[ "error" ] == "0" ) {

			$przelewy24_redirect = $P24->getHost() . "trnRequest/" . $res[ "token" ];

			// Zamiana znaków specjalnych na encje
			$przelewy24_redirect = str_replace( '&amp;', '&', $przelewy24_redirect );

			// Czyszczenie koszyka
			edd_empty_cart();

			// Przekierowanie na Przelewy24.pl
			echo '<br/><a href="' . $P24->getHost() . "trnRequest/" . $res[ "token" ] . '">' . $P24->getHost() . "trnRequest/" . $res[ "token" ] . '</a>';
		}

		exit;
	}
}

add_action( 'edd_gateway_przelewy24_gateway', 'bpmj_p24_edd_process_payment' );

function bpmj_p24_edd_przelewy24_gateway_cc_form() {
	
}

add_action( 'edd_przelewy24_gateway_cc_form', 'bpmj_p24_edd_przelewy24_gateway_cc_form' );
?>