<?php


/**
 * Dodaje formularz z opcjami tpay.com w ustawieniach EDD
 ** */
function bpmjd_tra_edd_add_settings( $edd_gw_settings ) {

	$tpay_gateway_settings = array(
		array(
			'id'   => 'tpay_gateway_settings',
			'name' => '<strong>' . __( 'Ustawienia tpay.com', 'edd-tpay' ) . '</strong>',
			'desc' => __( 'Zarządzaj ustawieniami bramki tpay.com', 'edd-tpay' ),
			'type' => 'header',
		),
		array(
			'id'   => 'tpay_id',
			'name' => __( 'Identyfikator tpay.com', 'edd-tpay' ),
			'desc' => __( 'Wprowadź Twój identyfikator z serwisu tpay.com', 'edd-tpay' ),
			'type' => 'text',
			'size' => 'regular',
		),
		array(
			'id'   => 'tpay_pin',
			'name' => __( 'Kod bezpieczeństwa tpay.com', 'edd-tpay' ),
			'desc' => __( 'Wprowadź Twój kod bezpieczeństwa (potwierdzający)', 'edd-tpay' ),
			'type' => 'text',
			'size' => 'regular',
		),
		array(
			'id'   => 'tpay_cards_api_key',
			'name' => __( 'Klucz API dla kart płatniczych (opcjonalnie)', 'edd-tpay' ),
			'desc' => __( 'Wprowadź Twój klucz API dla kart płatniczych. Wprowadzenie klucza i hasła umożliwia włączenie płatności cyklicznych (subskrypcyjnych)', 'edd-tpay' ),
			'type' => 'text',
			'size' => 'regular',
		),
		array(
			'id'   => 'tpay_cards_api_password',
			'name' => __( 'Hasło do API dla kart płatniczych (opcjonalnie)', 'edd-tpay' ),
			'desc' => __( 'Wprowadź Twoje hasło do API dla kart płatniczych', 'edd-tpay' ),
			'type' => 'text',
			'size' => 'regular',
		),
		array(
			'id'   => 'tpay_cards_verification_code',
			'name' => __( 'Kod weryfikacyjny dla API kart płatniczych', 'edd-tpay' ),
			'desc' => __( 'Wprowadź Twój kod weryfikacyjny do API dla kart płatniczych', 'edd-tpay' ),
			'type' => 'text',
			'size' => 'regular',
		),
		array(
			'id'   => 'tpay_recurrence_allow_standard_payments',
			'name' => __( 'Włącz standardowe sposoby płatności dla zakupów cyklicznych', 'edd-tpay' ),
			'desc' => __( 'Po zaznaczeniu tej opcji kupujący będzie miał możliwość wyboru standardowych kanałów płatności'
			              . ' do dokonania zapłaty za produkty cykliczne. System automatycznie wygeneruje płatności na'
			              . ' kolejne okresy rozliczeniowe, ale klient będzie musiał zostać poinformowany i dokonać płatności'
			              . ' ręcznie. Automatyczne obciążenie konta klienta jest możliwe tylko przy płatności kartą kredytową.', 'edd-tpay' ),
			'type' => 'checkbox',
		),
	);

	$edd_gw_settings[ 'tpay' ] = $tpay_gateway_settings;

	return $edd_gw_settings;
}

add_filter( 'edd_settings_gateways', 'bpmjd_tra_edd_add_settings' );

/**
 * @param array $sections
 *
 * @return array
 */
function bpmjd_tra_edd_add_settings_sections( $sections ) {
	$sections[ 'tpay' ] = __( 'Tpay', 'edd-tpay' );

	return $sections;
}

add_filter( 'edd_settings_sections_gateways', 'bpmjd_tra_edd_add_settings_sections' );
