<?php

/**
 *  Funkcja zapisuje dane płatności. Następnie przekierowuje na Dotpay.pl
 *  
 */
function bpmj_dot_edd_process_payment( $purchase_data ) {

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
		edd_record_gateway_error( __( 'Błąd płatności', 'bpmj_dot_edd' ), sprintf( __( 'Błąd płatności przed wysłaniem ich do Dotpay.  Data: %s', 'bpmj_dot_edd' ), json_encode( $payment_data ) ), $payment );
		// Powraca na stronę płatności dotpay
		edd_send_back_to_checkout( '?payment-mode=' . $purchase_data[ 'post_data' ][ 'edd-gateway' ] );
	} else {

		// Adresy przekierowań
		if ( edd_is_test_mode() ) {
			$dotpay_redirect = 'https://ssl.dotpay.pl/?';
		} else {
			$dotpay_redirect = 'https://ssl.dotpay.pl/?';
		}

		// URL powrotny klienta
		$return_url = add_query_arg( 'payment-confirmation', 'dotpay', get_permalink( $edd_options[ 'success_page' ] ) );

		// URLC - URL, na który dotpay wysyła wiadomość zwrotną
		$listener_url = home_url( '/' );

		// Sprawdz, czy użytwkonik wybrał księgowanie tylko w czasie rzeczywistym
		if ( isset( $edd_options[ 'dotpay_onlinetransfer' ] ) && $edd_options[ 'dotpay_onlinetransfer' ] == 1 ) {
			$onlinetransfer = 1;
		} else {
			$onlinetransfer = 0;
		}

		// Ciąg znaków kontrolnych - EDD generuje taki ciąg dla kazdego zamówienia
		$dotpay_control = $purchase_data[ 'purchase_key' ];

		$dotpay_args = array(
			'id'			 => $edd_options[ 'dotpay_id' ],
			'amount'		 => number_format( round( $purchase_data[ 'price' ], 2 ), 2, '.', '' ),
			'currency'		 => $edd_options[ 'currency' ],
			'description'	 => get_bloginfo( 'name' ) . ' Zamówienie nr ' . edd_get_payment_number( $payment ),
			'URL'			 => $return_url,
			'lang'			 => 'pl',
			'onlinetransfer' => $onlinetransfer,
			'typ'			 => '4',
			'ignore_last_payment_channel' => true,
			'URLC'			 => $listener_url,
			'control'		 => $dotpay_control,
			'firstname'		 => $purchase_data[ 'user_info' ][ 'first_name' ],
			'lastname'		 => $purchase_data[ 'user_info' ][ 'last_name' ],
			'email'			 => $purchase_data[ 'user_info' ][ 'email' ],
		);


		$dotpay_args = apply_filters( 'bpmj_dot_edd_redirect_args', $dotpay_args, $purchase_data );

		// Budowanie zapytania http
		$dotpay_redirect .= http_build_query( $dotpay_args );

		// Zamiana znaków specjalnych na encje
		$dotpay_redirect = str_replace( '&amp;', '&', $dotpay_redirect );

		// Czyszczenie koszyka
		edd_empty_cart();

		// Przekierowanie na Dotpay
		wp_redirect( $dotpay_redirect );
		exit;
	}
}

add_action( 'edd_gateway_dotpay_gateway', 'bpmj_dot_edd_process_payment' );

/**
 * Nasłuchiwanie połączenia zwrotnego z dotpay po dokonaniu transakcji
 *
 */
function bpmj_dot_edd_listen_for_dotpay() {

	// Sprawdzamy, czy połączenie jest z IP serwerów Dotpay
	//if ( $_SERVER[ 'REMOTE_ADDR' ] == '217.17.41.5' OR $_SERVER[ 'REMOTE_ADDR' ] == '195.150.9.37' ) {
	if ( !empty( $_POST[ 'id' ] ) AND ! empty( $_POST[ 't_id' ] ) AND ! empty( $_POST[ 'control' ] ) AND ! empty( $_POST[ 't_status' ] ) AND ! empty( $_POST[ 'email' ] ) AND ! empty( $_POST[ 'amount' ] )
	) {

		do_action( 'bpmj_dot_edd_verify_dotpay' );
	}
}

add_action( 'init', 'bpmj_dot_edd_listen_for_dotpay' );

/**
 * Weryfikacja danych przesłanych od Dotpay
 *
 * Po udanej walidacji danych otrzymanych od Dotpey następuje zmiana statusu płatności
 * na co completed, co jest równoznaczne z udostępnieniem pliku na klienta.
 */
function bpmj_dot_edd_check_dotpay() {
	global $edd_options;
	// Sprawdzenie, czy odpowiedź jest przesłana metoda POST
	if ( isset( $_SERVER[ 'REQUEST_METHOD' ] ) && $_SERVER[ 'REQUEST_METHOD' ] != 'POST' ) {
		return;
	}


	// Sprawdzmy czy serwer odesłał wymagane zmienne w $_POST
	if ( !empty( $_POST[ 'id' ] ) AND ! empty( $_POST[ 't_id' ] ) AND ! empty( $_POST[ 'control' ] ) AND ! empty( $_POST[ 't_status' ] ) AND ! empty( $_POST[ 'email' ] ) AND ! empty( $_POST[ 'amount' ] )
	) {
		// Tutaj jesteśmy pewni, że jest do Dotpay
		// Odebranie zmiennych z tablicy POST
		$control_dotpay	 = trim( $_POST[ 'control' ] );
		$email_dotpay	 = trim( $_POST[ 'email' ] );
		$amount_dotpay	 = trim( $_POST[ 'amount' ] );
		$status_dotpay	 = trim( $_POST[ 't_status' ] );
		$md5_dotpay		 = trim( $_POST[ 'md5' ] );

		// Pobieramy ID zamówienia
		$purchase_id = edd_get_purchase_id_by_key( $control_dotpay );
		$payment	 = edd_get_payment_by( 'id', $purchase_id );

		// czy zamówienie już zreazlizowane?
		$completed = 'publish' === $payment->post_status;

		// Sprawdzamy parametry t_status. Pożądana jest wartośc 2 oznaczająca "WYKONANA"
		if ( $status_dotpay == '2' && !$completed ) {

			// jesli nie ma takiego order id, przerwij
			if ( empty( $purchase_id ) ) {
				exit( "Nie ma zamówienia z takim numerem control " . $control_dotpay );
			} else {

				$amont_edd	 = edd_get_payment_amount( $purchase_id );
				$amont_edd	 = number_format( $amont_edd, 2, '.', '' );
				// Sprawdzamy zgodność kwoty zamówienia
				if ( $amount_dotpay != $amont_edd ) {
					exit( $purchase_id . ' Różna wartośc zamówień. Przesłana z Dotpay to: ' . $amount_dotpay . '. Otrzymana z bazy danych to: ' . $amont_edd );
				}

				//Przygotowanie zmiennych do wygenerowania ciągu MD5
				// Wzór - PIN:id:control:t_id:amount:email:service:code:username:password:t_status
				$pin		 = $edd_options[ 'dotpay_pin' ];
				$dotpay_id	 = $edd_options[ 'dotpay_id' ];
				$t_id		 = trim( $_POST[ 't_id' ] );
				$user_email	 = edd_get_payment_user_email( $purchase_id );

				$str_to_md5	 = $pin . ":" . $dotpay_id . ":" . $control_dotpay . ":" . $t_id . ":" . $amont_edd . ":" .
				$email_dotpay . ":::::" . $status_dotpay;
				$home_md5	 = md5( $str_to_md5 );



				// Sprawdzanie zgodności MD5
				if ( $md5_dotpay != $home_md5 ) {

					exit( 'md5 sie nie zgadzaja. ciag do md5: ' . $str_to_md5 . ", wynik: " . $home_md5 );
				}


				// Jeśli wszystko się zgadza, zaktualizuj status płatności

				$payment = get_post( $purchase_id );
				$status	 = $payment->post_status;
				if ( $status != 'publish' ) {
					edd_insert_payment_note( $purchase_id, 'Płatność zrealizowana pomyślnie' );
					edd_update_payment_status( $purchase_id, 'completed' );
				}
			}

			// Jeżeli status jest równy 3 – płatność nie powiodła się
		} else if ( $status_dotpay == '3' && !$completed ) {

			edd_insert_payment_note( $purchase_id, 'Płatność nie powiodła się' );
			edd_update_payment_status( $purchase_id, 'failed' );
		} else if ( !$completed ) {

			edd_update_payment_status( $purchase_id, 'failed' );

			// zwrot lub reklamacja
		} else if ( $status_dotpay == '4' || $status_dotpay == '5' ) {

			edd_insert_payment_note( $purchase_id, 'Otrzymano informacje o zwrocie lub reklamacji z Dotpay' );
			edd_update_payment_status( $purchase_id, 'refunded' );
		}

		// odebrano prawidłowy komunikat od Dotpay
		exit( "OK" );
	} else {

		exit( 'Brak parametrów post' );
	}

	exit;
}

add_action( 'bpmj_dot_edd_verify_dotpay', 'bpmj_dot_edd_check_dotpay' );

function bpmj_dot_edd_dotpay_gateway_cc_form() {
	
}

add_action( 'edd_dotpay_gateway_cc_form', 'bpmj_dot_edd_dotpay_gateway_cc_form' );
?>