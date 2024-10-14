<?php
/**
 * Funkcje wywoływane podczas aktywacji wtyczki
 */

// Zakoncz, jeżeli plik jest załadowany bezpośrednio
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

register_activation_hook( BPMJ_WPTAXE_FILE, 'bpmj_wptaxe_install' );

function bpmj_wptaxe_install() {
	global $bpmj_wptaxe_settings;


	// Zdefiniuj domyślne ustawienia
	$options = array();

	// Zapisanie domyślnych wartości dla opcji
	foreach ( bpmj_wptaxe_get_registered_settings() as $tab => $settings ) {

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

	update_option( 'bpmj_wptaxe_settings', array_merge( $bpmj_wptaxe_settings, $options ) );

	// Aktualna wersja wtyczki
	update_option( 'bpmj_wptaxe_version', BPMJ_WPTAXE_VERSION );


	// CRON
	if ( ! wp_next_scheduled( 'bpmj_wptaxe_cron' ) ) {
		wp_schedule_event( time(), 'bpmj_wptaxe_min', 'bpmj_wptaxe_cron' );
	}


}


