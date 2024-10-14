<?php
/**
 * Funkcje wywoływane podczas aktywacji wtyczki
 */

// Zakoncz, jeżeli plik jest załadowany bezpośrednio
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

register_activation_hook( BPMJ_WPINFAKT_FILE, 'bpmj_wpinfakt_install' );

function bpmj_wpinfakt_install() {
	global $bpmj_wpinfakt_settings;


	// Zdefiniuj domyślne ustawienia
	$options = array();

	// Zapisanie domyślnych wartości dla opcji
	foreach ( bpmj_wpinfakt_get_registered_settings() as $tab => $settings ) {

		foreach ( $settings as $option ) {

			if ( 'checkbox' == $option[ 'type' ] && ! empty( $option[ 'std' ] ) ) {
				$options[ $option[ 'id' ] ] = '1';
			}

			if ( 'license_key' == $option[ 'type' ] && ! empty( $option[ 'std' ] ) ) {
				$options[ $option[ 'id' ] ] = '';
			}

			if ( 'text' == $option[ 'type' ] && ! empty( $option[ 'std' ] ) ) {
				$options[ $option[ 'id' ] ] = $option[ 'std' ];
			}

			if ( 'textarea' == $option[ 'type' ] && ! empty( $option[ 'std' ] ) ) {
				$options[ $option[ 'id' ] ] = $option[ 'std' ];
			}

		}

	}

	update_option( 'bpmj_wpinfakt_settings', array_merge( $bpmj_wpinfakt_settings, $options ) );

	// Aktualna wersja wtyczki
	update_option( 'bpmj_wpinfakt_version', BPMJ_WPINFAKT_VERSION );


	// CRON
	if ( ! wp_next_scheduled( 'bpmj_wpinfakt_cron' ) ) {
		wp_schedule_event( time(), 'bpmj_wpinfakt_min', 'bpmj_wpinfakt_cron' );
	}


}


