<?php

/**
 * Dodaje formularz z opcjami Przelewy24.pl w ustawieniach EDD
 * * */
function bpmj_p24_edd_add_settings( $settings ) {

	$przelewy24_gateway_settings = array(
		array(
			'id'	 => 'przelewy24_gateway_settings',
			'name'	 => '<strong id="przelewy24_area" style="margin-top:40px;display:inline-block;font-size:17px;">' . __( 'Ustawienia Przelewy24.pl', 'bpmj_p24_edd' ) . '<hr /></strong>',
			'desc'	 => __( 'Zarządzaj ustawieniami bramki Przelewy24.pl', 'bpmj_p24_edd' ),
			'type'	 => 'header'
		),
		array(
			'id'	 => 'przelewy24_id',
			'name'	 => __( 'Identyfikator Przelewy24.pl', 'bpmj_p24_edd' ),
			'desc'	 => __( 'Wprowadź Twój identyfikator z serwisu Przelewy24.pl', 'bpmj_p24_edd' ),
			'type'	 => 'text',
			'size'	 => 'regular'
		),
		array(
			'id'	 => 'przelewy24_pin',
			'name'	 => __( 'Kod bezpieczeństwa CRC Przelewy24.pl', 'bpmj_p24_edd' ),
			'desc'	 => __( 'Wprowadź Twój kod bezpieczeństwa CRC', 'bpmj_p24_edd' ),
			'type'	 => 'text',
			'size'	 => 'regular'
		)
	);

	return array_merge( $settings, $przelewy24_gateway_settings );
}

add_filter( 'edd_settings_gateways', 'bpmj_p24_edd_add_settings' );

/**
 * Funckja ładuje skrypt odpowiedzialny za ukrywanie i pokazywanie ustawień Przelewy24.pl
 * 
 */
function bpmj_p24_edd_load_admin_scripts() {
	if ( isset( $_GET[ 'tab' ] ) && $_GET[ 'tab' ] == 'gateways' )
		wp_enqueue_script( 'bpmj_p24_edd_setting_scripts', BPMJ_P24_EDD_URL . 'assets/js/admin-settings.js' );
}

add_action( 'admin_enqueue_scripts', 'bpmj_p24_edd_load_admin_scripts' );
?>