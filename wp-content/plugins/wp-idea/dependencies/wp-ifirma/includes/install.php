<?php
/**
 * Funkcje wywoływane podczas aktywacji wtyczki
*/

// Zakoncz, jeżeli plik jest załadowany bezpośrednio
if (!defined('ABSPATH'))
    exit;

register_activation_hook( BPMJ_WPIFIRMA_FILE, 'bpmj_wpifirma_install' );

function bpmj_wpifirma_install() {
	global $bpmj_wpifirma_settings;


	// Zdefiniuj domyślne ustawienia
	$options = array();

	// Zapisanie domyślnych wartości dla opcji
	foreach( bpmj_wpifirma_get_registered_settings() as $tab => $settings ) {

		foreach ( $settings as $option ) {

			if( 'checkbox' == $option['type'] && ! empty( $option['std'] ) ) {
				$options[ $option['id'] ] = '1';
			}
                        
                        if( 'license_key' == $option['type'] && ! empty( $option['std'] ) ) {
				$options[ $option['id'] ] = '';
			}
                        
                        if( 'text' == $option['type'] && ! empty( $option['std'] ) ) {
				$options[ $option['id'] ] = $option['std'];
			}
                        
                        if( 'textarea' == $option['type'] && ! empty( $option['std'] ) ) {
				$options[ $option['id'] ] = $option['std'];
			}

		}

	}

	update_option( 'bpmj_wpifirma_settings', array_merge( $bpmj_wpifirma_settings, $options ) );
        
        // Aktualna wersja wtyczki
	update_option( 'bpmj_wpifirma_version', BPMJ_WPIFIRMA_VERSION );
        
        
        // CRON
        if (!wp_next_scheduled('bpmj_wpifirma_cron')) {
            wp_schedule_event(time(), 'bpmj_wpifirma_min', 'bpmj_wpifirma_cron');
        }
        
        
        
	
}


