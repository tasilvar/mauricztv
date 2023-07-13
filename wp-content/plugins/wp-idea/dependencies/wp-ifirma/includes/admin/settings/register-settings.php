<?php

/*
 * Rejestracja ustawień z wykorzystaniem WordPress Settings API
 */


// Zakoncz, jeżeli plik jest załadowany bezpośrednio
if (!defined('ABSPATH'))
    exit;

/**
 * Dodaje sekcje i pola dla ustawień
 */
function bpmj_wpifirma_register_settings() {

    if (false == get_option('bpmj_wpifirma_settings')) {
        add_option('bpmj_wpifirma_settings');
    }

    foreach (bpmj_wpifirma_get_registered_settings() as $section => $settings) {

        add_settings_section(
                'bpmj_wpifirma_settings_' . $section, __return_null(), '__return_false', 'bpmj_wpifirma_settings_' . $section
        );

        foreach ($settings as $option) {

            $name = isset($option['name']) ? $option['name'] : '';

            add_settings_field(
                'bpmj_wpifirma_settings[' . $option['id'] . ']', $name, function_exists('bpmj_wpifirma_' . $option['type'] . '_callback') ? 'bpmj_wpifirma_' . $option['type'] . '_callback' : 'bpmj_wpifirma_missing_callback', 'bpmj_wpifirma_settings_' . $section, 'bpmj_wpifirma_settings_' . $section, array(
                'id' => isset($option['id']) ? $option['id'] : null,
                'desc' => !empty($option['desc']) ? $option['desc'] : '',
                'name' => isset($option['name']) ? $option['name'] : null,
                'section' => $section,
                'size' => isset($option['size']) ? $option['size'] : null,
                'options' => isset($option['options']) ? $option['options'] : '',
                'std' => isset($option['std']) ? $option['std'] : ''
                    )
            );
        }
    }

    // Creates our settings in the options table
    register_setting('bpmj_wpifirma_settings', 'bpmj_wpifirma_settings', 'bpmj_wpifirma_settings_validation');
}

add_action('admin_init', 'bpmj_wpifirma_register_settings');


/*
 * Domyślne opcje wtyczki.
 * Filtry umożliwiają dodanie nowych ustawień inną wtyczką.
 */

function bpmj_wpifirma_get_registered_settings() {

    $bpmj_wpifirma_settings = array(
        // Generalne ustawienia
        'general' => apply_filters('bpmj_wpifirma_settings_general', array(
            'general_header' => array(
                'id' => 'general_header',
                'name' => '<strong>' . __('Ustawienia generalne', 'bpmj_wpifirma') . '</strong>',
                'type' => 'header'
            ),
            'license_key' => array(
                'id' => 'license_key',
                'name' => __('Klucz licencyjny', 'bpmj_wpifirma'),
                'type' => 'license_key'
            ),
            'ifirma_email' => array(
                'id' => 'ifirma_email',
                'name' => __('Email z systemu iFirma', 'bpmj_wpifirma'),
                'type' => 'text',
                'desc' => __('Podaj email, za pomocą którego logujesz się do panelu systemu iFirma', 'bpmj_wpifirma'),
            ),
            'ifirma_invoice_key' => array(
                'id' => 'ifirma_invoice_key',
                'name' => __('Klucz API faktura', 'bpmj_wpifirma'),
                'type' => 'text',
                'desc' => __('iFirma.pl -> Narzędzia -> API', 'bpmj_wpifirma'),
            ),
            'ifirma_subscriber_key' => array(
                'id' => 'ifirma_subscriber_key',
                'name' => __('Klucz API abonent', 'bpmj_wpifirma'),
                'type' => 'text',
                'desc' => __('iFirma.pl -> Narzędzia -> API', 'bpmj_wpifirma'),
            ),
            'receipt' => array(
                'id' => 'receipt',
                'name' => __('Wystawiaj też paragony', 'bpmj_wpifirma'),
                'type' => 'checkbox',
            ),
            'auto_sent' => array(
                'id' => 'auto_sent',
                'name' => __('Automatyczna wysyłka', 'bpmj_wpifirma'),
                'type' => 'checkbox',
                'desc' => __('Zaznacz, jeżeli dokumenty sprzedaży maja być wysyłane automatycznie e-mailem do klienta.', 'bpmj_wpifirma')
            ),
            'numeration' => array(
                'id' => 'numeration',
                'name' => __('Nazwa numeracji', 'bpmj_wpifirma'),
                'type' => 'text',
                'desc' => __('Podaj nazwę serii numeracji, która ma zostać wykorzystana. Możesz użyć tylko ręcznie utworzone numeracje (tzw. Własne serie numeracji).', 'bpmj_wpifirma')
            )
        )
    ),
        /*
        'prices' => apply_filters('bpmj_wpifirma_settings_prices', array(
            'prices_header' => array(
                'id' => 'prices_header',
                'name' => '<strong>' . __('Ustawienia cen i podatków', 'bpmj_wpifirma') . '</strong>',
                'type' => 'header'
            )
                )
        )
        */
    );

    return $bpmj_wpifirma_settings;
}

/*
 * Walicaja pól formularza z ustawieniami
 */

function bpmj_wpifirma_settings_validation($input = array()) {
    global $bpmj_wpifirma_settings;

    if (empty($_POST['_wp_http_referer'])) {
        return $input;
    }

    // Odkodowanie _wp_http_referer do postaci zmiennych
    parse_str($_POST['_wp_http_referer'], $referrer);

    // Pobranie informacji o aktualnej zakładce
    $tab = isset($referrer['tab']) ? $referrer['tab'] : 'general';

    // Pobranie białej listy wszystkich opcji utworzonych przez wtyczkę i rozszerzenia
    $settings = bpmj_wpifirma_get_registered_settings();

    // W przypadku braku zmian wartości opcji, zwróci pustą tablice.
    $input = $input ? $input : array();


    // Pętla po wszytskich opcjach wtyczki.
    if (!empty($settings[$tab])) {
        foreach ($settings[$tab] as $key => $value) {

            // Usuwa opcję z tablicy, gdy jest pusta. Przydaje się przy input typu checkbox
            if (empty($input[$key])) {
                if (isset($bpmj_wpifirma_settings[$key])) {
                    unset($bpmj_wpifirma_settings[$key]);
                }
            }
        }
    }

    $input['license_key'] = trim( $input['license_key'] ?? '' );

    // Łączy opcje zapisane w bazie z nowymi
    $output = array_merge($bpmj_wpifirma_settings, $input);

    // Przestajemy wstrzymywać mechanizm (jeżeli jest wstrzymany)
	delete_option( 'bpmj_wpifirma_processing_disabled_until' );

    return $output;
}

/**
 * Tworzy elementy menu typu TABS
 */
function bpmj_wpifirma_get_settings_tabs() {

    $settings = bpmj_wpifirma_get_registered_settings();

    $tabs = array();
    $tabs['general'] = __('Ogólne', 'bpmj_wpifirma');

    if (!empty($settings['prices'])) {
        $tabs['prices'] = __('Ceny', 'bpmj_wpifirma');
    }

    return apply_filters('bpmj_wpifirma_settings_tabs', $tabs);
}

/**
 * Input type text Callback
 * Przetwarza opcje typu input text
 */
function bpmj_wpifirma_text_callback($args) {
    global $bpmj_wpifirma_settings;

    if (isset($bpmj_wpifirma_settings[$args['id']]))
        $value = $bpmj_wpifirma_settings[$args['id']];
    else
        $value = isset($args['std']) ? $args['std'] : '';

    $size = ( isset($args['size']) && !is_null($args['size']) ) ? $args['size'] : 'regular';
    $html = '<input type="text" class="' . $size . '-text" id="bpmj_wpifirma_settings[' . $args['id'] . ']" name="bpmj_wpifirma_settings[' . $args['id'] . ']" value="' . esc_attr(stripslashes($value)) . '"/>';
    $html .= '<label for="bpmj_wpifirma_settings[' . $args['id'] . ']"> ' . $args['desc'] . '</label>';

    echo $html;
}

/**
 * Input type checkbox Callback
 * Przetwarza opcje typu input text
 */
function bpmj_wpifirma_checkbox_callback($args) {
    global $bpmj_wpifirma_settings;

    $checked = isset($bpmj_wpifirma_settings[$args['id']]) ? checked(1, $bpmj_wpifirma_settings[$args['id']], false) : '';
    $html = '<input type="checkbox" id="bpmj_wpifirma_settings[' . $args['id'] . ']" name="bpmj_wpifirma_settings[' . $args['id'] . ']" value="1" ' . $checked . '/>';
    $html .= '<label for="bpmj_wpifirma_settings[' . $args['id'] . ']"> ' . $args['desc'] . '</label>';
    echo $html;
}

/**
 * Input type textarea Callback
 * Przetwarza opcje typu textarea
 */
function bpmj_wpifirma_textarea_callback($args) {
    global $bpmj_wpifirma_settings;

    if (isset($bpmj_wpifirma_settings[$args['id']]))
        $value = $bpmj_wpifirma_settings[$args['id']];
    else
        $value = isset($args['std']) ? $args['std'] : '';

    $size = ( isset($args['size']) && !is_null($args['size']) ) ? $args['size'] : 'regular';
    $html = '<textarea class="large-text" cols="50" rows="5" id="bpmj_wpifirma_settings[' . $args['id'] . ']" name="bpmj_wpifirma_settings[' . $args['id'] . ']">' . esc_textarea(stripslashes($value)) . '</textarea>';
    $html .= '<label for="bpmj_wpifirma_settings[' . $args['id'] . ']"> ' . $args['desc'] . '</label>';

    echo $html;
}

/**
 * Select
 * Przetwarza opcje typu select
 */
function bpmj_wpifirma_select_callback($args) {
    global $bpmj_wpifirma_settings;

    if (isset($bpmj_wpifirma_settings[$args['id']]))
        $value = $bpmj_wpifirma_settings[$args['id']];
    else
        $value = isset($args['std']) ? $args['std'] : '';

    $html = '<select id="bpmj_wpifirma_settings[' . $args['id'] . ']" name="bpmj_wpifirma_settings[' . $args['id'] . ']"/>';

    foreach ($args['options'] as $option => $name) :
        $selected = selected($option, $value, false);
        $html .= '<option value="' . $option . '" ' . $selected . '>' . $name . '</option>';
    endforeach;

    $html .= '</select>';
    $html .= '<label for="bpmj_wpifirma_settings[' . $args['id'] . ']"> ' . $args['desc'] . '</label>';

    echo $html;
}

/**
 * Przetwarza opcje typu header
 */
function bpmj_wpifirma_header_callback($args) {
    echo '<hr/>';
}

function bpmj_wpifirma_license_key_callback($args) {
    global $bpmj_wpifirma_settings;

    $license_status = get_option( 'bpmj_wpifirma_license_status' );
	$license_status = apply_filters( 'bpmj_wpifirma_license_status', $license_status );

    if (isset($bpmj_wpifirma_settings[$args['id']]))
        $value = $bpmj_wpifirma_settings[$args['id']];
    else
        $value = '';

    $size = ( isset($args['size']) && !is_null($args['size']) ) ? $args['size'] : 'regular';
    $html = '<input type="text" class="' . $size . '-text" id="bpmj_wpifirma_settings[' . $args['id'] . ']" name="bpmj_wpifirma_settings[' . $args['id'] . ']" value="' . esc_attr(stripslashes($value)) . '"/>';
    if($license_status == 'valid') $desc = '<span style="color: green">Licencja aktywna</span>';
    else $desc = '<span style="color: red">Licencja nieaktywna</span>';
    $html .= '<label for="bpmj_wpifirma_settings[' . $args['id'] . ']"> ' . $desc . '</label>';

	if( $license_status == 'valid-wps' ) {
		$html = '<span style="color: green">Licencja aktywna (WP Seller)</span>';
	}

    echo $html;
}

/**
 * Brak funkcji zwrotnej
 */
function bpmj_wpifirma_missing_callback($args) {
    printf(__('Brak funkcji zwrotnej dla inputa <strong>%s</strong>', 'bpmj_wpifirma'), $args['id']);
}
