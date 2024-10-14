<?php

/**
 * Funkcje wywoływane podczas aktywacji wtyczki
 */
// Zakoncz, jeżeli plik jest załadowany bezpośrednio
if ( !defined( 'ABSPATH' ) )
	exit;

register_activation_hook( BPMJ_WPFA_FILE, 'bpmj_wpfa_install' );

function bpmj_wpfa_install() {
	global $bpmj_wpfa_settings;


	// Zdefiniuj domyślne ustawienia
	$options = array();

	// Zapisanie domyślnych wartości dla opcji
	foreach ( bpmj_wpfa_get_registered_settings() as $tab => $settings ) {

		foreach ( $settings as $option ) {

			if ( 'checkbox' == $option[ 'type' ] && !empty( $option[ 'std' ] ) ) {
				$options[ $option[ 'id' ] ] = '1';
			}

			if ( 'license_key' == $option[ 'type' ] && !empty( $option[ 'std' ] ) ) {
				$options[ $option[ 'id' ] ] = '';
			}

			if ( 'text' == $option[ 'type' ] && !empty( $option[ 'std' ] ) ) {
				$options[ $option[ 'id' ] ] = $option[ 'std' ];
			}

			if ( 'textarea' == $option[ 'type' ] && !empty( $option[ 'std' ] ) ) {
				$options[ $option[ 'id' ] ] = $option[ 'std' ];
			}
		}
	}

	update_option( 'bpmj_wpfa_settings', array_merge( $bpmj_wpfa_settings, $options ) );

	// Aktualna wersja wtyczki
	update_option( 'bpmj_wpfa_version', BPMJ_WPFA_VERSION );


	// CRON
	if ( !wp_next_scheduled( 'bpmj_wpfa_fakturownia' ) ) {
		wp_schedule_event( time(), 'bpmj_wpfa_min', 'bpmj_wpfa_fakturownia' );
	}
}
