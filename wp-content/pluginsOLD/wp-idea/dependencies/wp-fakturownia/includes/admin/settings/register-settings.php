<?php

/*
 * Rejestracja ustawień z wykorzystaniem WordPress Settings API
 */


// Zakoncz, jeżeli plik jest załadowany bezpośrednio
if ( !defined( 'ABSPATH' ) )
	exit;

/**
 * Dodaje sekcje i pola dla ustawień
 */
function bpmj_wpfa_register_settings() {

	if ( false == get_option( 'bpmj_wpfa_settings' ) ) {
		add_option( 'bpmj_wpfa_settings' );
	}

	foreach ( bpmj_wpfa_get_registered_settings() as $section => $settings ) {

		add_settings_section(
		'bpmj_wpfa_settings_' . $section, __return_null(), '__return_false', 'bpmj_wpfa_settings_' . $section
		);

		foreach ( $settings as $option ) {

			$name = isset( $option[ 'name' ] ) ? $option[ 'name' ] : '';

			add_settings_field(
			'bpmj_wpfa_settings[' . $option[ 'id' ] . ']', $name, function_exists( 'bpmj_wpfa_' . $option[ 'type' ] . '_callback' ) ? 'bpmj_wpfa_' . $option[ 'type' ] . '_callback' : 'bpmj_wpfa_missing_callback', 'bpmj_wpfa_settings_' . $section, 'bpmj_wpfa_settings_' . $section, array(
				'id'		 => isset( $option[ 'id' ] ) ? $option[ 'id' ] : null,
				'desc'		 => !empty( $option[ 'desc' ] ) ? $option[ 'desc' ] : '',
				'name'		 => isset( $option[ 'name' ] ) ? $option[ 'name' ] : null,
				'section'	 => $section,
				'size'		 => isset( $option[ 'size' ] ) ? $option[ 'size' ] : null,
				'options'	 => isset( $option[ 'options' ] ) ? $option[ 'options' ] : '',
				'std'		 => isset( $option[ 'std' ] ) ? $option[ 'std' ] : ''
			)
			);
		}
	}

	// Creates our settings in the options table
	register_setting( 'bpmj_wpfa_settings', 'bpmj_wpfa_settings', 'bpmj_wpfa_settings_validation' );
}

add_action( 'admin_init', 'bpmj_wpfa_register_settings' );


/*
 * Domyślne opcje wtyczki.
 * Filtry umożliwiają dodanie nowych ustawień inną wtyczką.
 */

function bpmj_wpfa_get_registered_settings() {

	$bpmj_wpfa_settings = array(
		// Generalne ustawienia
		'general' => apply_filters( 'bpmj_wpfa_settings_general', array(
			'general_header'	 => array(
				'id'	 => 'general_header',
				'name'	 => '<strong>' . __( 'Ustawienia generalne', 'bpmj_wpfa' ) . '</strong>',
				'type'	 => 'header'
			),
			'license_key'		 => array(
				'id'	 => 'license_key',
				'name'	 => __( 'Klucz licencyjny', 'bpmj_wpfa' ),
				'type'	 => 'license_key'
			),
			'apikey'			 => array(
				'id'	 => 'apikey',
				'name'	 => __( 'Klucz API / Nazwa konta', 'bpmj_wpfa' ),
				'type'	 => 'text',
				'desc'	 => __( 'Fakturownia.pl -> Ustawienia konta -> Integracja -> Kod autoryzacyjny API / Fakturownia.pl -> Ustawienia konta -> Nazwa konta / firmy', 'bpmj_wpfa' ),
			),
			'departments_id'	 => array(
				'id'	 => 'departments_id',
				'name'	 => __( 'ID firmy', 'bpmj_wpfa' ),
				'type'	 => 'text',
				'size'	 => 'small',
				'desc'	 => __( 'W Fakturownia.pl -> Ustawienia -> Dane firmy należy kliknąć na firmę / dział i ID działu pojawi się w URL. Jeśli to pole pozostanie puste, wtedy będą wstawione domyślne dane Twojej firmy', 'bpmj_wpfa' ),
			),
			'receipt'			 => array(
				'id'	 => 'receipt',
				'name'	 => __( 'Wystawiaj też paragony', 'bpmj_wpfa' ),
				'type'	 => 'checkbox',
			),
			'bill_note' => array(
				'id'   => 'bill_note',
				'name' => __( 'Tekst do wstawienia na rachunku', 'bpmj_wpfa' ),
				'type' => 'textarea',
				'std'  => __( 'SPRZEDAWCA ZWOLNIONY PODMIOTOWO Z PODATKU OD TOWARU I USŁUG (dostawa towarów lub świadczenie usług zwolnione na podstawie art. 113 ust 1 (albo ust. 9) ustawy z dnia 11 marca 2004 r. o podatku od towarów i usług (Dz. U. z 2011 r Nr 177, poz. 1054 z późn. zm.)).', 'bpmj_wpfa' ),
			),
			'auto_sent'			 => array(
				'id'	 => 'auto_sent',
				'name'	 => __( 'Automatyczna wysyłka faktur', 'bpmj_wpfa' ),
				'type'	 => 'checkbox',
				'desc'	 => __( 'Zaznacz, jeżeli faktury maja być wysyłane automatycznie e-mailem do klienta. Wymagana pełna aktywacja systemu Fakturownia.pl.', 'bpmj_wpfa' )
			),
			'auto_sent_receipt'	 => array(
				'id'	 => 'auto_sent_receipt',
				'name'	 => __( 'Automatyczna wysyłka paragonów', 'bpmj_wpfa' ),
				'type'	 => 'checkbox',
				'desc'	 => __( 'Zaznacz, jeżeli paragony maja być wysyłane automatycznie e-mailem do klienta. Wymagana pełna aktywacja systemu Fakturownia.pl.', 'bpmj_wpfa' )
			),
		)
		),
	/*
	  'prices' => apply_filters('bpmj_wpfa_settings_prices', array(
	  'prices_header' => array(
	  'id' => 'prices_header',
	  'name' => '<strong>' . __('Ustawienia cen i podatków', 'bpmj_wpfa') . '</strong>',
	  'type' => 'header'
	  )
	  )
	  )
	 */
	);

	return $bpmj_wpfa_settings;
}

/*
 * Walicaja pól formularza z ustawieniami
 */

function bpmj_wpfa_settings_validation( $input = array() ) {
	global $bpmj_wpfa_settings;

	if ( empty( $_POST[ '_wp_http_referer' ] ) ) {
		return $input;
	}

	// Odkodowanie _wp_http_referer do postaci zmiennych
	parse_str( $_POST[ '_wp_http_referer' ], $referrer );

	// Pobranie informacji o aktualnej zakładce
	$tab = isset( $referrer[ 'tab' ] ) ? $referrer[ 'tab' ] : 'general';

	// Pobranie białej listy wszystkich opcji utworzonych przez wtyczkę i rozszerzenia
	$settings = bpmj_wpfa_get_registered_settings();

	// W przypadku braku zmian wartości opcji, zwróci pustą tablice.
	$input = $input ? $input : array();


	// Pętla po wszytskich opcjach wtyczki.
	if ( !empty( $settings[ $tab ] ) ) {
		foreach ( $settings[ $tab ] as $key => $value ) {

			// Usuwa opcję z tablicy, gdy jest pusta. Przydaje się przy input typu checkbox
			if ( empty( $input[ $key ] ) ) {
				if ( isset( $bpmj_wpfa_settings[ $key ] ) ) {
					unset( $bpmj_wpfa_settings[ $key ] );
				}
			}
		}
	}

	$input[ 'license_key' ] = trim( $input[ 'license_key' ] ?? '' );

	// Łączy opcje zapisane w bazie z nowymi
	$output = array_merge( $bpmj_wpfa_settings, $input );

	return $output;
}

/**
 * Tworzy elementy menu typu TABS
 */
function bpmj_wpfa_get_settings_tabs() {

	$settings = bpmj_wpfa_get_registered_settings();

	$tabs				 = array();
	$tabs[ 'general' ]	 = __( 'Ogólne', 'bpmj_wpfa' );

	if ( !empty( $settings[ 'prices' ] ) ) {
		$tabs[ 'prices' ] = __( 'Ceny', 'bpmj_wpfa' );
	}

	return apply_filters( 'bpmj_wpfa_settings_tabs', $tabs );
}

/**
 * Input type text Callback
 * Przetwarza opcje typu input text
 */
function bpmj_wpfa_text_callback( $args ) {
	global $bpmj_wpfa_settings;

	if ( isset( $bpmj_wpfa_settings[ $args[ 'id' ] ] ) )
		$value	 = $bpmj_wpfa_settings[ $args[ 'id' ] ];
	else
		$value	 = isset( $args[ 'std' ] ) ? $args[ 'std' ] : '';

	$size	 = ( isset( $args[ 'size' ] ) && !is_null( $args[ 'size' ] ) ) ? $args[ 'size' ] : 'regular';
	$html	 = '<input type="text" class="' . $size . '-text" id="bpmj_wpfa_settings[' . $args[ 'id' ] . ']" name="bpmj_wpfa_settings[' . $args[ 'id' ] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
	$html .= '<label for="bpmj_wpfa_settings[' . $args[ 'id' ] . ']"> ' . $args[ 'desc' ] . '</label>';

	echo $html;
}

/**
 * Input type checkbox Callback
 * Przetwarza opcje typu input text
 */
function bpmj_wpfa_checkbox_callback( $args ) {
	global $bpmj_wpfa_settings;

	$checked = isset( $bpmj_wpfa_settings[ $args[ 'id' ] ] ) ? checked( 1, $bpmj_wpfa_settings[ $args[ 'id' ] ], false ) : '';
	$html	 = '<input type="checkbox" id="bpmj_wpfa_settings[' . $args[ 'id' ] . ']" name="bpmj_wpfa_settings[' . $args[ 'id' ] . ']" value="1" ' . $checked . '/>';
	$html .= '<label for="bpmj_wpfa_settings[' . $args[ 'id' ] . ']"> ' . $args[ 'desc' ] . '</label>';
	echo $html;
}

/**
 * Input type textarea Callback
 * Przetwarza opcje typu textarea
 */
function bpmj_wpfa_textarea_callback( $args ) {
	global $bpmj_wpfa_settings;

	if ( isset( $bpmj_wpfa_settings[ $args[ 'id' ] ] ) )
		$value	 = $bpmj_wpfa_settings[ $args[ 'id' ] ];
	else
		$value	 = isset( $args[ 'std' ] ) ? $args[ 'std' ] : '';

	$size	 = ( isset( $args[ 'size' ] ) && !is_null( $args[ 'size' ] ) ) ? $args[ 'size' ] : 'regular';
	$html	 = '<textarea class="large-text" cols="50" rows="5" id="bpmj_wpfa_settings[' . $args[ 'id' ] . ']" name="bpmj_wpfa_settings[' . $args[ 'id' ] . ']">' . esc_textarea( stripslashes( $value ) ) . '</textarea>';
	$html .= '<label for="bpmj_wpfa_settings[' . $args[ 'id' ] . ']"> ' . $args[ 'desc' ] . '</label>';

	echo $html;
}

/**
 * Select
 * Przetwarza opcje typu select
 */
function bpmj_wpfa_select_callback( $args ) {
	global $bpmj_wpfa_settings;

	if ( isset( $bpmj_wpfa_settings[ $args[ 'id' ] ] ) )
		$value	 = $bpmj_wpfa_settings[ $args[ 'id' ] ];
	else
		$value	 = isset( $args[ 'std' ] ) ? $args[ 'std' ] : '';

	$html = '<select id="bpmj_wpfa_settings[' . $args[ 'id' ] . ']" name="bpmj_wpfa_settings[' . $args[ 'id' ] . ']"/>';

	foreach ( $args[ 'options' ] as $option => $name ) :
		$selected = selected( $option, $value, false );
		$html .= '<option value="' . $option . '" ' . $selected . '>' . $name . '</option>';
	endforeach;

	$html .= '</select>';
	$html .= '<label for="bpmj_wpfa_settings[' . $args[ 'id' ] . ']"> ' . $args[ 'desc' ] . '</label>';

	echo $html;
}

/**
 * Przetwarza opcje typu header
 */
function bpmj_wpfa_header_callback( $args ) {
	echo '<hr/>';
}

function bpmj_wpfa_license_key_callback( $args ) {
	global $bpmj_wpfa_settings;

	$license_status = get_option( 'bpmj_wpfa_license_status' );
	$license_status = apply_filters( 'bpmj_wpfakturownia_license_status', $license_status );

	if ( isset( $bpmj_wpfa_settings[ $args[ 'id' ] ] ) )
		$value	 = $bpmj_wpfa_settings[ $args[ 'id' ] ];
	else
		$value	 = '';

	$size	 = ( isset( $args[ 'size' ] ) && !is_null( $args[ 'size' ] ) ) ? $args[ 'size' ] : 'regular';
	$html	 = '<input type="text" class="' . $size . '-text" id="bpmj_wpfa_settings[' . $args[ 'id' ] . ']" name="bpmj_wpfa_settings[' . $args[ 'id' ] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
	if ( $license_status == 'valid' )
		$desc	 = '<span style="color: green">Licencja aktywna</span>';
	else
		$desc	 = '<span style="color: red">Licencja nieaktywna</span>';
	$html .= '<label for="bpmj_wpfa_settings[' . $args[ 'id' ] . ']"> ' . $desc . '</label>';

	if( $license_status == 'valid-wps' ) {
		$html = '<span style="color: green">Licencja aktywna (WP Seller)</span>';
	}

	echo $html;
}

/**
 * Brak funkcji zwrotnej
 */
function bpmj_wpfa_missing_callback( $args ) {
	printf( __( 'Brak funkcji zwrotnej dla inputa <strong>%s</strong>', 'bpmj_wpfa' ), $args[ 'id' ] );
}

/**
 * @return string
 */
function bpmj_wpfa_get_bill_note() {
	global $bpmj_wpfa_settings;

	if ( isset( $bpmj_wpfa_settings[ 'bill_note' ] ) ) {
		return $bpmj_wpfa_settings[ 'bill_note' ];
	}

	$settings = bpmj_wpfa_get_registered_settings();

	return $settings[ 'general' ][ 'bill_note' ][ 'std' ];
}
