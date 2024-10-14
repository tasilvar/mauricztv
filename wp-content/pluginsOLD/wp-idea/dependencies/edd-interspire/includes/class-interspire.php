<?php

use bpmj\wpidea\integrations\Interface_External_Service_Integration;
use bpmj\wpidea\integrations\Trait_External_Service_Integration;
use bpmj\wpidea\helpers\Translator_Static_Helper;

/**
 * EDD ConvertKit class, extension of the EDD base newsletter classs
 *
 * @copyright   Copyright (c) 2013, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */
class BPMJ_EDD_Interspire extends EDD_Newsletter implements Interface_External_Service_Integration {
use Trait_External_Service_Integration;

    const SERVICE_NAME = 'Interspire';

	/**
	 * Sets up the checkout label
	 */
	public function init() {
		global $edd_options;

		if ( !empty( $edd_options[ 'bpmj_edd_in_label' ] ) ) {
			$this->checkout_label = trim( $edd_options[ 'bpmj_edd_in_label' ] );
		} else {
			$this->checkout_label = Translator_Static_Helper::translate('newsletter.sign_up');
		}
		add_filter( 'edd_settings_sections_extensions', array( $this, 'subsection' ), 10, 1 );
	}

	/**
	 * Register our subsection for EDD 2.5
	 */
	function subsection( $sections ) {
		$sections[ 'interspire' ] = __( 'Interspire', BPMJ_EDD_IN_DOMAIN );
		return $sections;
	}

	/**
	 * Registers the plugin settings
	 */
	public function settings( $settings ) {
		global $edd_options;

		$standard_fields = array(
			array(
				'id'	 => 'bpmj_edd_in_settings',
				'name'	 => '<strong>' . __( 'Interspire Settings', BPMJ_EDD_IN_DOMAIN ) . '</strong>',
				'type'	 => 'header'
			),
			array(
				'id'	 => 'bpmj_edd_in_username',
				'name'	 => __( 'Interspire Username', BPMJ_EDD_IN_DOMAIN ),
				'desc'	 => __( 'Enter your Interspire Username', BPMJ_EDD_IN_DOMAIN ),
				'type'	 => 'text',
				'size'	 => 'regular'
			),
			array(
				'id'	 => 'bpmj_edd_in_token',
				'name'	 => __( 'Interspire Token', BPMJ_EDD_IN_DOMAIN ),
				'desc'	 => __( 'Enter Interspire Token', BPMJ_EDD_IN_DOMAIN ),
				'type'	 => 'text',
				'size'	 => 'regular'
			),
			array(
				'id'	 => 'bpmj_edd_in_xmlEndpoint',
				'name'	 => __( 'Interspire XML path', BPMJ_EDD_IN_DOMAIN ),
				'desc'	 => __( 'Enter your full Interspire XML path', BPMJ_EDD_IN_DOMAIN ),
				'type'	 => 'text',
				'size'	 => 'regular'
			)
		);

		$apiTest = $this->test_api();
		if ( $apiTest === true ) {

			$standard_fields[ 0 ][ 'name' ] = '<strong>' . __( 'Interspire Settings', BPMJ_EDD_IN_DOMAIN ) . '</strong><br><i style="font-weight: 400; color: green;">'. __('You are connected', BPMJ_EDD_IN_DOMAIN) .'</i>';
			$get_lists = $this->get_lists();
			$get_lists	= !empty( $get_lists ) ? $get_lists : array( '' => 'No groups...' );

			$fields = array(
				array(
					'id'	 => 'bpmj_edd_in_show_checkout_signup',
					'name'	 => __( 'Show Signup on Checkout', BPMJ_EDD_IN_DOMAIN ),
					'desc'	 => __( 'Allow customers to signup for the list selected below during checkout?', BPMJ_EDD_IN_DOMAIN ),
					'type'	 => 'checkbox'
				),
				array(
					'id'		 => 'bpmj_edd_in_contact_list',
					'name'		 => __( 'Choose Contact List', BPMJ_EDD_IN_DOMAIN ),
					'desc'		 => __( 'Select the list you wish to subscribe buyers to', BPMJ_EDD_IN_DOMAIN ),
					'type'		 => 'select',
					'options'	 => $get_lists
				),
				array(
					'id'	 => 'bpmj_edd_in_label',
					'name'	 => __( 'Checkout Label', BPMJ_EDD_IN_DOMAIN ),
					'desc'	 => __( 'This is the text shown next to the signup option', BPMJ_EDD_IN_DOMAIN ),
					'type'	 => 'text',
					'size'	 => 'regular'
				),
				array(
					'id'	 => 'bpmj_edd_in_double_opt_in',
					'name'	 => __( 'Double Opt-In', BPMJ_EDD_IN_DOMAIN ),
					'desc'	 => __( 'When checked, to users will be sent a confirmation email after signing up, and will only be added once they have confirmed the subscription.', BPMJ_EDD_IN_DOMAIN ),
					'type'	 => 'checkbox'
				)
			);
		} else {

			if ( !empty( $edd_options[ 'bpmj_edd_in_api' ] ) ) {
				$api_key = trim( $edd_options[ 'bpmj_edd_in_api' ] );
			}

			$standard_fields[ 0 ][ 'name' ] = '<strong>' . __( 'Interspire Settings', BPMJ_EDD_IN_DOMAIN ) . '</strong><br><i style="font-weight: 400">' . $apiTest .'</i>';

			$fields = array();
		}

		$eddin_settings = array_merge( $standard_fields, $fields );


		if ( version_compare( EDD_VERSION, 2.5, '>=' ) ) {
			$eddin_settings = array( 'interspire' => $eddin_settings );
		}
		return array_merge( $settings, $eddin_settings );
	}


	/**
	 * Pokazywanie lub ukrywanie checkboxa subskrybcji
	 */
	public function show_checkout_signup() {
		global $edd_options;

		return !empty($edd_options['bpmj_edd_in_show_checkout_signup']) && $edd_options[ 'bpmj_edd_in_show_checkout_signup' ] == '1';
	}


	/**
	 * Testowanie połączenia API
	 */
	public function test_api() {
		try {
			$api	= new EDD_Interspire_API();
			$response = $api->call( 'authentication', 'xmlapitest' );
			return true;

		} catch (Exception $e) {
		  return $e->getMessage();
		}
	}


	/**
	 * Pobieranie list kontaktów
	 */
	public function get_lists() {
		try {
			$api	= new EDD_Interspire_API();
			$response = $api->call( 'lists', 'GetLists', array( 'perpage' => 'all' ) );

			if ( !empty( $response ) ) {
				foreach ( $response->data->item as $list ) {
					$this->lists[ (int)$list->listid ] = $list->name;
				}
			}

		} catch (Exception $e) {}

		return (array) $this->lists;
	}


	/**
	 * Subscribe an email to a list
	 */
	public function subscribe_email( $user_info = array(), $list_id = false, $opt_in_overridde = false ) {
		global $edd_options;

		// Przetestuj połączenie API
		if ( $this->test_api() !== true ) {
			return false;
		}

		// Pobierz ID listy jeżeli żadna nie jest podana
		if ( !$list_id ) {
			$list_id = !empty( $edd_options[ 'bpmj_edd_in_contact_list' ] ) ? $edd_options[ 'bpmj_edd_in_contact_list' ] : false;
			if ( !$list_id ) {
				return false;
			}
		}

		// Czy ma być email automatycznie aktywowany
		$opt_in = isset( $edd_options[ 'bpmj_edd_in_double_opt_in' ] ) && $edd_options[ 'bpmj_edd_in_double_opt_in' ] == '1' && !$opt_in_overridde;
		if ( $opt_in ) {
			$confirmed = 'no';
		} else {
			$confirmed = 'yes';
		}

		// Wyślij zapytanie
		try {
			$api = new EDD_Interspire_API();

			$lists = explode( '|', $list_id );
			foreach( $lists as $list ) {
				$subscriber = array(
					'emailaddress' => $user_info[ 'email' ],
					'mailinglist' => $list,
					'confirmed' => $confirmed,
					'format' => 'html'
				);

				$api->call( 'subscribers', 'AddSubscriberToList', $subscriber );
			}

		} catch (Exception $e){}

	}

    public function check_connection(): bool
    {
        return $this->test_api() === true;
    }

}

$edd_in = new BPMJ_EDD_Interspire( 'interspire', 'Interspire' );
