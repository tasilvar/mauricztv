<?php

/*
 * Tutaj wykonują się operacje związane z wysyłką i odbiorem płatności do tpay.com
 */

use bpmj\wp\eddtpay\gateways\TpayRecurrence;

/**
 *  Funkcja zapisuje dane płatności. Następnie przekierowuje na https://secure.tpay.com
 *
 */
function bpmjd_tra_edd_process_payment( $purchase_data ) {
	global $edd_options;

	// Dane zamówienia do zapisu
	$payment_data = array(
		'price'        => $purchase_data[ 'price' ],
		'date'         => $purchase_data[ 'date' ],
		'user_email'   => $purchase_data[ 'user_email' ],
		'purchase_key' => $purchase_data[ 'purchase_key' ],
		'currency'     => $edd_options[ 'currency' ],
		'downloads'    => $purchase_data[ 'downloads' ],
		'cart_details' => $purchase_data[ 'cart_details' ],
		'user_info'    => $purchase_data[ 'user_info' ],
		'status'       => 'pending'
	);

	$payment = edd_insert_payment( $payment_data );

	// Sprawdza, czy dane zostały zapisane
	if ( ! $payment ) {
		// Wyświetla informacje o błędzie
		edd_record_gateway_error( __( 'Błąd płatności', 'edd-tpay' ), sprintf( __( 'Błąd płatności przed wysłaniem ich do tpay.com.  Data: %s', 'edd-tpay' ), json_encode( $payment_data ) ), $payment );
		// Powraca na stronę płatności transfeuj.pl
		edd_send_back_to_checkout( '?payment-mode=' . $purchase_data[ 'post_data' ][ 'edd-gateway' ] );
	} else {
		$tpay_redirect = bpmjd_tra_prepare_tpay_payment( $payment, $purchase_data );
		wp_redirect( $tpay_redirect );
		exit;
	}
}

function bpmjd_tra_prepare_tpay_payment( $payment_id, $purchase_data ) {
	global $edd_options;

	// Adresy przekierowań
	if ( edd_is_test_mode() ) {
		$tpay_redirect = 'https://secure.tpay.com/?';
	} else {
		$tpay_redirect = 'https://secure.tpay.com/?';
	}

	// URL powrotny klienta
	$return_url = add_query_arg( 'payment-confirmation', 'tpay', get_permalink( $edd_options[ 'success_page' ] ) );

	// wyn_url - wynikowy adres zwrotny URL, na który tpay wyślę tablice POST
	$listener_url = add_query_arg( 'payment-status', 'tpay', home_url('/') );

	//Kwota do zapłaty
	$cart_summary = number_format( round( $purchase_data[ 'price' ], 2 ), 2, '.', '' );

	// Ciąg znaków kontrolny "crc". Wygenerowany dla każdego zamówienia przez EDD
	$tpay_control = $purchase_data[ 'purchase_key' ];


	/*
	 * Generujemy sumę kontrolną md5sum do wysłania
	 *
	 * Wzór - MD5(id + kwota + crc + kod potwierdzający sprzedawcy)
	 */

	$pin_tpay = html_entity_decode($edd_options[ 'tpay_pin' ]);
	$id_tpay  = $edd_options[ 'tpay_id' ];

	$md5sum_str  = $id_tpay . '&' . $cart_summary . '&' . $tpay_control . '&' . $pin_tpay;
	$tpay_md5sum = md5( $md5sum_str );


	// Przygotowanie tablicy danych do przesłania
	$tpay_args = array(
		'id'       => $edd_options[ 'tpay_id' ],
		'kwota'    => $cart_summary,
		'opis'     => get_bloginfo( 'name' ) . ' Zamówienie nr ' . $payment_id,
		'crc'      => $tpay_control,
		'wyn_url'  => $listener_url,
		'pow_url'  => $return_url,
		'md5sum'   => $tpay_md5sum,
		'jezyk'    => 'pl',
		'imie'     => $purchase_data[ 'user_info' ][ 'first_name' ],
		'nazwisko' => $purchase_data[ 'user_info' ][ 'last_name' ],
		'email'    => $purchase_data[ 'user_info' ][ 'email' ],
	);


	$tpay_args = apply_filters( 'bpmjd_tra_edd_redirect_args', $tpay_args, $purchase_data );


	// Budowanie zapytania http
	$tpay_redirect .= http_build_query( $tpay_args );

	// Zamiana znaków specjalnych na encje
	$tpay_redirect = str_replace( '&amp;', '&', $tpay_redirect );

	// Czyszczenie koszyka
	edd_empty_cart();

	return $tpay_redirect;
}

add_action( 'edd_gateway_tpay_gateway', 'bpmjd_tra_edd_process_payment' );

/**
 * Nasłuchiwanie połączenia zwrotnego z tpay.com po dokonaniu transakcji
 *
 */
function bpmjd_tra_edd_listen_for_tpay() {

	// sprawdzenie adresu IP oraz występowania zmiennych POST
	$ip_table = array(
		'195.149.229.109',
		'148.251.96.163',
		'178.32.201.77',
		'46.248.167.59',
		'46.29.19.106',
		'176.119.38.175',
	);

	if ( WP_DEBUG ) {
		$edd_tpay_data                       = get_transient( '_edd_tpay_input' ) ?: array();
		$edd_tpay_data_new_row               = $_POST;
		$edd_tpay_data_new_row[ '_ip' ]      = $_SERVER[ 'REMOTE_ADDR' ] ?? null;
		$edd_tpay_data_new_row[ '_reached' ] = array();
	}
	
	$ip_verified = false;
	if (isset($_SERVER['REMOTE_ADDR']) && in_array( $_SERVER[ 'REMOTE_ADDR' ], $ip_table )) {
        $ip_verified = true;
	}
	else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && in_array( $_SERVER[ 'HTTP_X_FORWARDED_FOR' ], $ip_table )) {
        $ip_verified = true;
	}
	else if (isset($_SERVER['HTTP_CF_CONNECTING_IP']) && in_array( $_SERVER[ 'HTTP_CF_CONNECTING_IP' ], $ip_table )) {
        $ip_verified = true;
	}
	
	if ( $ip_verified && ! empty( $_POST ) ) {
		if ( WP_DEBUG ) {
			$edd_tpay_data_new_row[ '_reached' ][] = 'ip match';
		}

		if ( ! empty( $_POST[ 'type' ] ) && ! empty( $_POST[ 'sign' ] ) ) {
			if ( WP_DEBUG ) {
				$edd_tpay_data_new_row[ '_reached' ][] = 'bpmjd_tra_edd_verify_tpay_cards';
			}
			// Looks like a Cards API response
			do_action( 'bpmjd_tra_edd_verify_tpay_cards' );
		} else {
			if ( WP_DEBUG ) {
				$edd_tpay_data_new_row[ '_reached' ][] = 'bpmjd_tra_edd_verify_tpay';
			}
			do_action( 'bpmjd_tra_edd_verify_tpay' );
		}
	}
	if ( ! empty( $_POST ) && WP_DEBUG ) {
		$edd_tpay_data[] = $edd_tpay_data_new_row;
		if ( count( $edd_tpay_data ) > 10 ) {
			$edd_tpay_data = array_slice( $edd_tpay_data, - 10 );
		}
		set_transient( '_edd_tpay_input', $edd_tpay_data );
	}
}

add_action( 'init', 'bpmjd_tra_edd_listen_for_tpay' );

/**
 * Weryfikacja danych przesłanych od tpay.com
 *
 * Po udanej walidacji danych otrzymanych od tpay.com następuje zmiana statusu płatności
 * na completed, co jest równoznaczne z udostępnieniem pliku dla klienta.
 */
function bpmjd_tra_edd_check_tpay() {
	global $edd_options;
	// Sprawdzenie, czy odpowiedź jest przesłana metoda POST
	if ( isset( $_SERVER[ 'REQUEST_METHOD' ] ) && $_SERVER[ 'REQUEST_METHOD' ] != 'POST' ) {
		return;
	}


	// Sprawdzmy czy serwer odesłał wymagane zmienne w $_POST
	if ( ! empty( $_POST[ 'id' ] ) AND ! empty( $_POST[ 'tr_id' ] ) AND ! empty( $_POST[ 'tr_date' ] ) AND ! empty( $_POST[ 'tr_crc' ] ) AND ! empty( $_POST[ 'tr_amount' ] ) AND ! empty( $_POST[ 'tr_paid' ] ) AND ! empty( $_POST[ 'tr_desc' ] ) AND ! empty( $_POST[ 'tr_email' ] ) AND ! empty( $_POST[ 'md5sum' ] ) AND ! empty( $_POST[ 'tr_status' ] )
	) {
		// Tutaj jesteśmy pewni, że jest do tpay.com
		// Odebranie zmiennych z tablicy POST

		$id_owner   = trim( $_POST[ 'id' ] );
		$tr_id      = trim( $_POST[ 'tr_id' ] );
		$crc        = trim( $_POST[ 'tr_crc' ] );
		$kwota      = trim( $_POST[ 'tr_amount' ] );
		$kwota_zapl = trim( $_POST[ 'tr_paid' ] );
		$status     = trim( $_POST[ 'tr_status' ] );
		//  $error = trim($_POST['tr_error']);
		$md5sum = trim( $_POST[ 'md5sum' ] );

		// Sprawdzamy parametry tr_status. Pożądana jest wartośc TRUE
		if ( $status !== 'TRUE' ) {
			exit( "Wartość parametru tr_status jest nieprawidłowa. Otrzymany tr_status to: " . $status );
		}


		// Pobieramy ID zamówienia
		$purchase_id = edd_get_purchase_id_by_key( $crc );

		// jesli nie ma takiego order id, przerwij
		if ( empty( $purchase_id ) ) {
			exit( "Nie ma zamówienia z takim numerem control: " . $crc );
		} else {

			$amont_edd = edd_get_payment_amount( $purchase_id );
			$amont_edd = number_format( $amont_edd, 2, '.', '' );


			/*
			 * Sprawdzamy zgodność kwoty zamówienia
			 * 
			 * Jeżeli zapłacił za mało skrypt się przerywa. 
			 * Jeżeli tyle ile trzeba, lub za dużo, dkrypt przechodzi dalej
			 */
			$nadplata = 0;
			if ( $kwota_zapl < $amont_edd ) {
				exit( $purchase_id . ' Klient zapłacił za mało. Powinien zapłacić: ' . $amont_edd . '. Zapłacił: ' . $kwota_zapl );
			} elseif ( $kwota_zapl > $amont_edd ) {
				$nadplata = $kwota_zapl - $amont_edd;
			}


			//Przygotowanie zmiennych do wygenerowania ciągu MD5
			// Wzór - MD5(id +tr_id+tr_amount+tr_crc +kodbezpieczeństwa)
			$pin_home      = html_entity_decode($edd_options[ 'tpay_pin' ]);
			$id_owner_home = $edd_options[ 'tpay_id' ];

			$str_to_md5 = $id_owner_home . $tr_id . $kwota . $crc . $pin_home;
			$home_md5   = md5( $str_to_md5 );


			// Sprawdzanie zgodności MD5
			if ( $md5sum != $home_md5 ) {

				exit( $id_owner . 'md5 sie nie zgadzaja. ciag do md5: ' . $str_to_md5 . ", wynik: " . $home_md5 );
			}


			// Jeśli wszystko się zgadza, zaktualizuj status płatności

			$payment = get_post( $purchase_id );
			$status  = $payment->post_status;
			if ( $status != 'publish' ) {
				if ( '1' === get_post_meta( $purchase_id, '_tpay_setup_recurrence', true ) ) {
					delete_post_meta( $purchase_id, '_tpay_setup_recurrence' );
					do_action( 'bpmjd_tra_recurrence_create_future_payments', $purchase_id );
				}
				if ( $nadplata != 0 ) {
					edd_insert_payment_note( $purchase_id, 'Odnotowano nadpłatę w wysokości ' . $nadplata . ' zł' );
				}
				edd_insert_payment_note( $purchase_id, 'Płatność zrealizowana pomyślnie' );
				$new_post_date = current_time( 'mysql' );
				wp_update_post( array(
					'ID'            => $purchase_id,
					'post_date'     => $new_post_date,
					'post_date_gmt' => get_gmt_from_date( $new_post_date ),
				) );
				edd_update_payment_status( $purchase_id, 'completed' );
			}

			exit( 'TRUE' );
		}
	} else {
		exit( 'Brak parametrów post' );
	}
}

add_action( 'bpmjd_tra_edd_verify_tpay', 'bpmjd_tra_edd_check_tpay' );

function bpmj_tra_edd_tpay_gateway_cc_form() {

}

add_action( 'edd_tpay_gateway_cc_form', 'bpmj_tra_edd_tpay_gateway_cc_form' );

function bpmj_tra_edd_tpay_load_recurrence_handler() {
	global $edd_options;

	if ( ! empty( $edd_options[ 'tpay_cards_api_key' ] ) && ! empty( $edd_options[ 'tpay_cards_api_password' ] ) ) {
		include_once __DIR__ . '/tpay-recurrence.php';
		TpayRecurrence::instance()->bootstrap(
			$edd_options[ 'tpay_cards_api_key' ],
			$edd_options[ 'tpay_cards_api_password' ],
			isset( $edd_options[ 'tpay_cards_verification_code' ] ) ? $edd_options[ 'tpay_cards_verification_code' ] : '',
			! empty( $edd_options[ 'tpay_recurrence_allow_standard_payments' ] ) && '1' == $edd_options[ 'tpay_recurrence_allow_standard_payments' ]
		);
	}
}

add_action( 'plugins_loaded', 'bpmj_tra_edd_tpay_load_recurrence_handler' );

/**
 *
 */
function bpmj_tra_edd_tpay_upgrade_settings() {
	global $edd_options;
	$update = false;

	if ( ! empty( $edd_options[ 'transferuj_id' ] )
	     && ! empty( $edd_options[ 'transferuj_pin' ] )
	     && empty( $edd_options[ 'tpay_id' ] )
	     && empty( $edd_options[ 'tpay_pin' ] )
	) {
		$edd_options[ 'tpay_id' ]  = $edd_options[ 'transferuj_id' ];
		$edd_options[ 'tpay_pin' ] = $edd_options[ 'transferuj_pin' ];
		$update                    = true;
	}

	$enabled_gateways = ! empty( $edd_options[ 'gateways' ] ) && is_array( $edd_options[ 'gateways' ] ) ? $edd_options[ 'gateways' ] : array();

	if ( key_exists( 'transferuj_gateway', $enabled_gateways ) ) {
		$enabled_gateways[ 'tpay_gateway' ] = '1';
		unset( $enabled_gateways[ 'transferuj_gateway' ] );

		$update                    = true;
		$edd_options[ 'gateways' ] = $enabled_gateways;
	}

	if ( isset( $edd_options[ 'default_gateway' ] ) && 'transferuj_gateway' === $edd_options[ 'default_gateway' ] ) {
		$update                           = true;
		$edd_options[ 'default_gateway' ] = 'tpay_gateway';
	}

	if ( $update ) {
		update_option( 'edd_settings', $edd_options );
	}
}

add_action( 'plugins_loaded', 'bpmj_tra_edd_tpay_upgrade_settings' );

add_filter( 'edd_download_is_recurring', function ( $recurring, $download_id ) {
	if ( strpos( get_the_title( $download_id ), 'recurring' ) !== false ) {
		return true;
	}

	return $recurring;
}, 10, 2 );