<?php

/*
 * Wszystkie akcje użyte we wtyczce
 */

// Zakoncz, jeżeli plik jest załadowany bezpośrednio
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use bpmj\wpidea\admin\settings\core\configuration\Integrations_Settings_Group;
use bpmj\wpidea\Caps;

// Wykonuje Cron Hook
add_action( 'bpmj_wpwf_cron', 'bpmj_wpwf_call_cron' );

function bpmj_wpwf_call_cron() {

	require_once BPMJ_WPWF_DIR . 'includes/cron.php';
}

/**
 * Ukrywa z typu posta "bpmj_wp_wfirma" przycisk dodaj nowy
 */
function bpmj_wpwf_custom_post_ui() {

	if ( isset( $_GET[ 'post_type' ] ) && $_GET[ 'post_type' ] === 'bpmj_wp_wfirma' ) {

		echo '<style type="text/css">
    .add-new-h2, .view-switch {display:none;}
    </style>';
	}
}

add_action( 'admin_head', 'bpmj_wpwf_custom_post_ui' );

/**
 *
 */
function bpmj_wpwf_get_company_id_input() {
	$company_id = isset( $_POST[ 'company_id' ] ) ? (int) $_POST[ 'company_id' ] : 0;
	$login      = isset( $_POST[ 'login' ] ) ? $_POST[ 'login' ] : '';
	$password   = isset( $_POST[ 'password' ] ) ? $_POST[ 'password' ] : '';
	$input_id   = isset( $_POST[ 'input_id' ] ) ? $_POST[ 'input_id' ] : '';

	if ( ! empty( $login ) && ! empty( $password ) ) {
		$invoice_object = new BPMJ_WP_wFirma( $login, $password );
		$user_companies = $invoice_object->get_user_companies();
		if ( empty( $user_companies ) ) {
			if ( $invoice_object->is_out_of_service() ) {
				echo __( 'Nie udało się pobrać listy firm - serwis wFirma jest tymczasowo wyłączony', 'bpmj_wpwf' );
			} else {
				echo __( 'Nie udało się pobrać listy firm - sprawdź czy dane logowania są poprawne', 'bpmj_wpwf' );
			}
		} else if ( 1 === count( $user_companies ) ) {
			echo '<input type="hidden" name="bpmj_wpwf_settings[' . $input_id . ']" value="' . key( $user_companies ) . '" />'
			     . reset( $user_companies );
		} else {
			?>
            <select name="bpmj_wpwf_settings[<?php echo $input_id; ?>]">
				<?php foreach ( $user_companies as $id => $company_name ): ?>
                    <option value="<?php echo $id; ?>" <?php selected( $company_id, $id ); ?>><?php echo esc_attr( $company_name ); ?></option>
				<?php endforeach; ?>
            </select>
			<?php
		}
	}

	wp_die();
}

add_action( 'wp_ajax_bpmj_wpwf_get_company_id_input', 'bpmj_wpwf_get_company_id_input' );

/**
 *
 */
function bpmj_wpwf_cron_check_company_id() {
	global $bpmj_wpwf_settings;

	if ( empty( $bpmj_wpwf_settings[ 'wf_login' ] ) || empty( $bpmj_wpwf_settings[ 'wf_pass' ] ) ) {
		return;
	}

	$wf_company_id = $bpmj_wpwf_settings[ 'wf_company_id' ];

	$invoice_object = new BPMJ_WP_wFirma();
	$user_companies = $invoice_object->get_user_companies();

	if ( empty( $user_companies ) ) {
		if ( $invoice_object->is_out_of_service() ) {
			// Serwis jest chwilowo wyłączony
			return;
		}
		// Nie udało się pobrać listy firm - prawdopodobnie login i hasło są nieaktualne
		update_option( 'bpmj_wpwf_credentials_error', 'credentials' );

		return;
	}

	if ( ! $wf_company_id && 1 === count( $user_companies ) ) {
		// ID firmy nie jest wybrany, ale w wFirmie jest tylko jedna firma - wybieramy ją
		$bpmj_wpwf_settings[ 'wf_company_id' ] = key( $user_companies );
		update_option( 'bpmj_wpwf_settings', $bpmj_wpwf_settings );

		return;
	}

	if ( $wf_company_id && isset( $user_companies[ $wf_company_id ] ) ) {
		// Wszystko OK - zapisany ID firmy jest prawidłowy
		return;
	}

	update_option( 'bpmj_wpwf_credentials_error', 'company' );
}

add_action( 'bpmj_wpwf_cron_check_company_id', 'bpmj_wpwf_cron_check_company_id' );

/**
 *
 */
function bpmj_wpwf_credentials_warning() {
	$credentials_error = get_option( 'bpmj_wpwf_credentials_error' );

	$message = '';
	switch ( $credentials_error ) {
		case 'credentials':
			$message = __( 'wFirma: nie udało się pobrać listy firm. Sprawdź, czy login i hasło do wFirmy są ustawione prawidłowo.', 'bpmj_wpwf' );
			break;
		case 'company':
			$message = __( 'wFirma: do prawidłowego funkcjonowania wtyczki konieczne jest wybranie firmy, na którą mają być wystawiane faktury.', 'bpmj_wpwf' );
			break;
	}

	if ( empty( $message ) ) {
		return;
	}
	$settings_url = apply_filters( 'bpmj_wpwf_settings_url', admin_url( 'edit.php?post_type=bpmj_wp_wfirma&page=bpmj_wpwf_options' ) );
	?>
    <div class="error">
        <p>
			<?php
			echo $message;
			?>
        </p>
        <p>
			<?php
			echo '<a href="' . $settings_url . '">' . __( 'Przejdź do ustawień' ) . '</a>';
			?>
        </p>
    </div>
	<?php
}

add_action( 'admin_notices', 'bpmj_wpwf_credentials_warning' );

/**
 *
 */
function bpmj_wpwf_clear_credentials_error() {
	delete_option( 'bpmj_wpwf_credentials_error' );
}

add_action( 'update_option_bpmj_wpwf_settings', 'bpmj_wpwf_clear_credentials_error' );

function bpmj_wpwf_listener() {
    if( empty( $_GET['pbg-listener'] ) ) {
        return;
    }

    if( 'wfirma' != $_GET['pbg-listener'] ) {
        return;
    }

    if( ! current_user_can(Caps::CAP_MANAGE_SETTINGS) ) {
        return;
    }

    $invoice_object = new BPMJ_WP_wFirma();
    $invoice_object->set_authorization_code(preg_replace("/[^a-zA-Z0-9]+/", "", $_GET['code']));
    
    $invoice_object = new BPMJ_WP_wFirma();
    $invoice_object->init_access_data();

    wp_redirect( admin_url(Integrations_Settings_Group::WFIRMA_CONFIGURATION_PATH) );
    exit;
}

add_action( 'init', 'bpmj_wpwf_listener' );
