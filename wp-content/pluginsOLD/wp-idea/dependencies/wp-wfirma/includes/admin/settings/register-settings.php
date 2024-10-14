<?php

/*
 * Rejestracja ustawień z wykorzystaniem WordPress Settings API
 */


// Zakoncz, jeżeli plik jest załadowany bezpośrednio
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Dodaje sekcje i pola dla ustawień
 */
function bpmj_wpwf_register_settings() {

	if ( false == get_option( 'bpmj_wpwf_settings' ) ) {
		add_option( 'bpmj_wpwf_settings' );
	}

	foreach ( bpmj_wpwf_get_registered_settings() as $section => $settings ) {

		add_settings_section(
			'bpmj_wpwf_settings_' . $section, __return_null(), '__return_false', 'bpmj_wpwf_settings_' . $section
		);

		foreach ( $settings as $option ) {

			$name = isset( $option[ 'name' ] ) ? $option[ 'name' ] : '';

			add_settings_field(
				'bpmj_wpwf_settings[' . $option[ 'id' ] . ']', $name, function_exists( 'bpmj_wpwf_' . $option[ 'type' ] . '_callback' ) ? 'bpmj_wpwf_' . $option[ 'type' ] . '_callback' : 'bpmj_wpwf_missing_callback', 'bpmj_wpwf_settings_' . $section, 'bpmj_wpwf_settings_' . $section, array(
					'id'      => isset( $option[ 'id' ] ) ? $option[ 'id' ] : null,
					'desc'    => ! empty( $option[ 'desc' ] ) ? $option[ 'desc' ] : '',
					'name'    => isset( $option[ 'name' ] ) ? $option[ 'name' ] : null,
					'section' => $section,
					'size'    => isset( $option[ 'size' ] ) ? $option[ 'size' ] : null,
					'options' => isset( $option[ 'options' ] ) ? $option[ 'options' ] : '',
					'std'     => isset( $option[ 'std' ] ) ? $option[ 'std' ] : ''
				)
			);
		}
	}

	// Creates our settings in the options table
	register_setting( 'bpmj_wpwf_settings', 'bpmj_wpwf_settings', 'bpmj_wpwf_settings_validation' );
}

add_action( 'admin_init', 'bpmj_wpwf_register_settings' );


/*
 * Domyślne opcje wtyczki.
 * Filtry umożliwiają dodanie nowych ustawień inną wtyczką.
 */

function bpmj_wpwf_get_registered_settings() {

	$bpmj_wpwf_settings = array(
		// Generalne ustawienia
		'general' => apply_filters( 'bpmj_wpwf_settings_general', array(
				'general_header'    => array(
					'id'   => 'general_header',
					'name' => '<strong>' . __( 'Ustawienia generalne', 'bpmj_wpwf' ) . '</strong>',
					'type' => 'header'
				),
				'license_key'       => array(
					'id'   => 'license_key',
					'name' => __( 'Klucz licencyjny', 'bpmj_wpwf' ),
					'type' => 'license_key'
				),
				'wf_login'          => array(
					'id'   => 'wf_login',
					'name' => __( 'Login', 'bpmj_wpwf' ),
					'type' => 'text',
					'desc' => __( 'Login (adres email) do systemu wfirma.pl', 'bpmj_wpwf' ),
				),
				'wf_pass'           => array(
					'id'   => 'wf_pass',
					'name' => __( 'Hasło', 'bpmj_wpwf' ),
					'type' => 'password',
					'desc' => __( 'Hasło do systemu wfirma.pl', 'bpmj_wpwf' ),
				),
				'wf_company_id'     => array(
					'id'   => 'wf_company_id',
					'name' => __( 'Wybierz firmę', 'bpmj_wpwf' ),
					'type' => 'company_id',
				),
				'receipt'           => array(
					'id'   => 'receipt',
					'name' => __( 'Wystawiaj też paragony', 'bpmj_wpwf' ),
					'type' => 'checkbox',
				),
				'auto_sent'         => array(
					'id'   => 'auto_sent',
					'name' => __( 'Automatyczna wysyłka faktur', 'bpmj_wpwf' ),
					'type' => 'checkbox',
					'desc' => __( 'Zaznacz, jeżeli faktury maja być wysyłane automatycznie e-mailem do klienta.', 'bpmj_wpwf' )
				),
				'auto_sent_receipt' => array(
					'id'   => 'auto_sent_receipt',
					'name' => __( 'Automatyczna wysyłka paragonów', 'bpmj_wpwf' ),
					'type' => 'checkbox',
					'desc' => __( 'Zaznacz, jeżeli paragony maja być wysyłane automatycznie e-mailem do klienta.', 'bpmj_wpwf' )
				),
			)
		),
	);

	return $bpmj_wpwf_settings;
}

/*
 * Walicaja pól formularza z ustawieniami
 */

function bpmj_wpwf_settings_validation( $input = array() ) {
	global $bpmj_wpwf_settings;

	if ( empty( $_POST[ '_wp_http_referer' ] ) ) {
		return $input;
	}

	// Odkodowanie _wp_http_referer do postaci zmiennych
	parse_str( $_POST[ '_wp_http_referer' ], $referrer );

	// Pobranie informacji o aktualnej zakładce
	$tab = isset( $referrer[ 'tab' ] ) ? $referrer[ 'tab' ] : 'general';

	// Pobranie białej listy wszystkich opcji utworzonych przez wtyczkę i rozszerzenia
	$settings = bpmj_wpwf_get_registered_settings();

	// W przypadku braku zmian wartości opcji, zwróci pustą tablice.
	$input = $input ? $input : array();


	// Pętla po wszytskich opcjach wtyczki.
	if ( ! empty( $settings[ $tab ] ) ) {
		foreach ( $settings[ $tab ] as $key => $value ) {

			// Usuwa opcję z tablicy, gdy jest pusta. Przydaje się przy input typu checkbox
			if ( empty( $input[ $key ] ) ) {
				if ( isset( $bpmj_wpwf_settings[ $key ] ) ) {
					unset( $bpmj_wpwf_settings[ $key ] );
				}
			}
		}
	}

	$input[ 'license_key' ] = trim( $input[ 'license_key' ] ?? '');

	// Łączy opcje zapisane w bazie z nowymi
	$output = array_merge( $bpmj_wpwf_settings, $input );

	return $output;
}

/**
 * Tworzy elementy menu typu TABS
 */
function bpmj_wpwf_get_settings_tabs() {

	$settings = bpmj_wpwf_get_registered_settings();

	$tabs              = array();
	$tabs[ 'general' ] = __( 'Ogólne', 'bpmj_wpwf' );

	if ( ! empty( $settings[ 'prices' ] ) ) {
		$tabs[ 'prices' ] = __( 'Ceny', 'bpmj_wpwf' );
	}

	return apply_filters( 'bpmj_wpwf_settings_tabs', $tabs );
}

/**
 * Input type text Callback
 * Przetwarza opcje typu input text
 */
function bpmj_wpwf_text_callback( $args ) {
	global $bpmj_wpwf_settings;

	if ( isset( $bpmj_wpwf_settings[ $args[ 'id' ] ] ) ) {
		$value = $bpmj_wpwf_settings[ $args[ 'id' ] ];
	} else {
		$value = isset( $args[ 'std' ] ) ? $args[ 'std' ] : '';
	}

	$size = ( isset( $args[ 'size' ] ) && ! is_null( $args[ 'size' ] ) ) ? $args[ 'size' ] : 'regular';
	$html = '<input type="text" class="' . $size . '-text" id="bpmj_wpwf_settings[' . $args[ 'id' ] . ']" name="bpmj_wpwf_settings[' . $args[ 'id' ] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
	$html .= '<label for="bpmj_wpwf_settings[' . $args[ 'id' ] . ']"> ' . $args[ 'desc' ] . '</label>';

	echo $html;
}

/**
 * Przetwarza formatkę do wprowadzania ID firmy
 */
function bpmj_wpwf_company_id_callback( $args ) {
	global $bpmj_wpwf_settings;

	if ( isset( $bpmj_wpwf_settings[ $args[ 'id' ] ] ) ) {
		$value = $bpmj_wpwf_settings[ $args[ 'id' ] ];
	} else {
		$value = isset( $args[ 'std' ] ) ? $args[ 'std' ] : '';
	}

	$wf_login    = empty( $bpmj_wpwf_settings[ 'wf_login' ] ) ? '' : $bpmj_wpwf_settings[ 'wf_login' ];
	$wf_password = empty( $bpmj_wpwf_settings[ 'wf_password' ] ) ? '' : $bpmj_wpwf_settings[ 'wf_password' ];

	$text_provide_login_and_password = '<span style="color: #aaa">' . __( 'Podaj login i hasło do wFirmy, by pobrać listę firm', 'bpmj_wpwf' ) . '</span>';

	$html = '<input type="hidden" id="bpmj_wpwf_settings_' . $args[ 'id' ] . '_initial" name="bpmj_wpwf_settings[' . $args[ 'id' ] . ']" value="' . $value . '" />';
	$html .= '<div id="bpmj_wpwf_settings_' . $args[ 'id' ] . '_inputs">';
	if ( empty( $wf_login ) || empty( $wf_password ) ) {
		$html .= $text_provide_login_and_password;
	}
	$html .= '</div>';
	$html .= '<img style="display: none;" id="bpmj_wpwf_settings_' . $args[ 'id' ] . '_spinner" src="' . admin_url( '/images/wpspin_light.gif' ) . '" alt="" />';

	echo $html;
	?>
    <script type="text/javascript">
		jQuery( function ( $ ) {
			var element_id = '<?php echo $args[ 'id' ]; ?>';
			var text_provide_login_and_password = '<?php echo $text_provide_login_and_password; ?>'
			var $wf_login = $( '[id="bpmj_wpwf_settings[wf_login]"]' );
			var $wf_password = $( '[id="bpmj_wpwf_settings[wf_pass]"]' );
			var $initial = $( '#bpmj_wpwf_settings_' + element_id + '_initial' );
			var $spinner = $( '#bpmj_wpwf_settings_' + element_id + '_spinner' );
			var $inputs = $( '#bpmj_wpwf_settings_' + element_id + '_inputs' );
			var onchange = function () {
				var wf_login = $wf_login.val();
				var wf_password = $wf_password.val();

				if ( wf_login && wf_password ) {
					$spinner.show();
					$inputs.hide();
					var company_id = $inputs.find( 'input,select' ).val() || $initial.val();
					var data = {
						action: 'bpmj_wpwf_get_company_id_input',
						company_id: company_id,
                        login: wf_login,
                        password: wf_password,
                        input_id: element_id
					};
					$.post( ajaxurl, data, function ( response ) {
						$spinner.hide();
						$inputs.html(response);
						$inputs.show();
					} );
				} else {
					$inputs.html( text_provide_login_and_password );
					$spinner.hide();
				}
			};

			$wf_login.change( onchange );
			$wf_password.change( onchange );
			onchange();
		} );
    </script>
	<?php
}

/**
 * Input type text Callback
 * Przetwarza opcje typu input password
 */
function bpmj_wpwf_password_callback( $args ) {
	global $bpmj_wpwf_settings;

	if ( isset( $bpmj_wpwf_settings[ $args[ 'id' ] ] ) ) {
		$value = $bpmj_wpwf_settings[ $args[ 'id' ] ];
	} else {
		$value = isset( $args[ 'std' ] ) ? $args[ 'std' ] : '';
	}

	$size = ( isset( $args[ 'size' ] ) && ! is_null( $args[ 'size' ] ) ) ? $args[ 'size' ] : 'regular';
	$html = '<input type="password" class="' . $size . '-text" id="bpmj_wpwf_settings[' . $args[ 'id' ] . ']" name="bpmj_wpwf_settings[' . $args[ 'id' ] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
	$html .= '<label for="bpmj_wpwf_settings[' . $args[ 'id' ] . ']"> ' . $args[ 'desc' ] . '</label>';

	echo $html;
}

/**
 * Input type checkbox Callback
 * Przetwarza opcje typu input checkbox
 */
function bpmj_wpwf_checkbox_callback( $args ) {
	global $bpmj_wpwf_settings;

	$checked = isset( $bpmj_wpwf_settings[ $args[ 'id' ] ] ) ? checked( 1, $bpmj_wpwf_settings[ $args[ 'id' ] ], false ) : '';
	$html    = '<input type="checkbox" id="bpmj_wpwf_settings[' . $args[ 'id' ] . ']" name="bpmj_wpwf_settings[' . $args[ 'id' ] . ']" value="1" ' . $checked . '/>';
	$html    .= '<label for="bpmj_wpwf_settings[' . $args[ 'id' ] . ']"> ' . $args[ 'desc' ] . '</label>';
	echo $html;
}

/**
 * Input type textarea Callback
 * Przetwarza opcje typu textarea
 */
function bpmj_wpwf_textarea_callback( $args ) {
	global $bpmj_wpwf_settings;

	if ( isset( $bpmj_wpwf_settings[ $args[ 'id' ] ] ) ) {
		$value = $bpmj_wpwf_settings[ $args[ 'id' ] ];
	} else {
		$value = isset( $args[ 'std' ] ) ? $args[ 'std' ] : '';
	}

	$size = ( isset( $args[ 'size' ] ) && ! is_null( $args[ 'size' ] ) ) ? $args[ 'size' ] : 'regular';
	$html = '<textarea class="large-text" cols="50" rows="5" id="bpmj_wpwf_settings[' . $args[ 'id' ] . ']" name="bpmj_wpwf_settings[' . $args[ 'id' ] . ']">' . esc_textarea( stripslashes( $value ) ) . '</textarea>';
	$html .= '<label for="bpmj_wpwf_settings[' . $args[ 'id' ] . ']"> ' . $args[ 'desc' ] . '</label>';

	echo $html;
}

/**
 * Select
 * Przetwarza opcje typu select
 */
function bpmj_wpwf_select_callback( $args ) {
	global $bpmj_wpwf_settings;

	if ( isset( $bpmj_wpwf_settings[ $args[ 'id' ] ] ) ) {
		$value = $bpmj_wpwf_settings[ $args[ 'id' ] ];
	} else {
		$value = isset( $args[ 'std' ] ) ? $args[ 'std' ] : '';
	}

	$html = '<select id="bpmj_wpwf_settings[' . $args[ 'id' ] . ']" name="bpmj_wpwf_settings[' . $args[ 'id' ] . ']"/>';

	foreach ( $args[ 'options' ] as $option => $name ) :
		$selected = selected( $option, $value, false );
		$html     .= '<option value="' . $option . '" ' . $selected . '>' . $name . '</option>';
	endforeach;

	$html .= '</select>';
	$html .= '<label for="bpmj_wpwf_settings[' . $args[ 'id' ] . ']"> ' . $args[ 'desc' ] . '</label>';

	echo $html;
}

/**
 * Przetwarza opcje typu header
 */
function bpmj_wpwf_header_callback( $args ) {
	echo '<hr/>';
}

function bpmj_wpwf_license_key_callback( $args ) {
	global $bpmj_wpwf_settings;

	$license_status = get_option( 'bpmj_wpwf_license_status' );
	$license_status = apply_filters( 'bpmj_wpwfirma_license_status', $license_status );

	if ( isset( $bpmj_wpwf_settings[ $args[ 'id' ] ] ) ) {
		$value = $bpmj_wpwf_settings[ $args[ 'id' ] ];
	} else {
		$value = '';
	}

	$size = ( isset( $args[ 'size' ] ) && ! is_null( $args[ 'size' ] ) ) ? $args[ 'size' ] : 'regular';
	$html = '<input type="text" class="' . $size . '-text" id="bpmj_wpwf_settings[' . $args[ 'id' ] . ']" name="bpmj_wpwf_settings[' . $args[ 'id' ] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
	if ( $license_status == 'valid' ) {
		$desc = '<span style="color: green">Licencja aktywna</span>';
	} else {
		$desc = '<span style="color: red">Licencja nieaktywna</span>';
	}
	$html .= '<label for="bpmj_wpwf_settings[' . $args[ 'id' ] . ']"> ' . $desc . '</label>';

	if ( $license_status == 'valid-wps' ) {
		$html = '<span style="color: green">Licencja aktywna (WP Seller)</span>';
	}

	echo $html;
}

/**
 * Brak funkcji zwrotnej
 */
function bpmj_wpwf_missing_callback( $args ) {
	printf( __( 'Brak funkcji zwrotnej dla inputa <strong>%s</strong>', 'bpmj_wpwf' ), $args[ 'id' ] );
}
