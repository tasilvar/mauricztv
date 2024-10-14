<?php

/**
 * Funkcje wywoływane podczas aktywacji wtyczki
 */
// Zakoncz, jeżeli plik jest załadowany bezpośrednio
if ( !defined( 'ABSPATH' ) )
	exit;

register_activation_hook( BPMJ_WPWF_FILE, 'bpmj_wpwf_install' );

function bpmj_wpwf_install() {
	global $bpmj_wpwf_settings;


	// Zdefiniuj domyślne ustawienia
	$options = array();

	// Zapisanie domyślnych wartości dla opcji
	foreach ( bpmj_wpwf_get_registered_settings() as $tab => $settings ) {

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

	update_option( 'bpmj_wpwf_settings', array_merge( $bpmj_wpwf_settings, $options ) );

	// Aktualna wersja wtyczki
	update_option( 'bpmj_wpwf_version', BPMJ_WPWF_VERSION );


	// CRON
	if ( !wp_next_scheduled( 'bpmj_wpwf_cron' ) ) {
		wp_schedule_event( time(), 'bpmj_wpwf_min', 'bpmj_wpwf_cron' );
	}
}
