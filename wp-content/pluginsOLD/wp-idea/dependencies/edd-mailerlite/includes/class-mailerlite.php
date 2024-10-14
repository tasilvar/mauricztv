<?php

use bpmj\wpidea\helpers\Cache;
use bpmj\wpidea\integrations\Interface_External_Service_Integration;
use bpmj\wpidea\integrations\Trait_External_Service_Integration;
use bpmj\wpidea\helpers\Translator_Static_Helper;

class BPMJ_EDD_Mailerlite extends EDD_Newsletter  implements Interface_External_Service_Integration{
    use Trait_External_Service_Integration;

    private const API_TEST_RESULT_TRANSIENT = 'mailerlite_api_test_result_cache';
    private const API_TEST_RESULT_OK = 'ok';
    private const API_TEST_RESULT_ERROR = 'error';

    const SERVICE_NAME = 'Mailerlite';

    /**
	 * Sets up the checkout label
	 */
	public function init() {
		global $edd_options;
		if ( !empty( $edd_options[ 'bpmj_edd_ml_label' ] ) ) {
			$this->checkout_label = trim( $edd_options[ 'bpmj_edd_ml_label' ] );
		} else {
			$this->checkout_label = Translator_Static_Helper::translate('newsletter.sign_up');
		}
		add_filter( 'edd_settings_sections_extensions', array( $this, 'subsection' ), 10, 1 );
        add_filter( 'edd_settings_general_sanitize', array( $this, 'save_settings' ) );
        add_action( 'mailerlite_clear_cache', [$this, 'clear_cache'] );
	}

	/**
	 * Register our subsection for EDD 2.5
	 */
	function subsection( $sections ) {
		$sections[ 'mailerlite' ] = __( 'MailerLite', BPMJ_EDD_ML_DOMAIN );
		return $sections;
	}

	/**
	 * Registers the plugin settings
	 */
	public function settings( $settings ) {

		global $edd_options;

		$standard_fields = array(
			array(
				'id'	 => 'bpmj_edd_ml_settings',
				'name'	 => '<strong>' . __( 'MailerLite Settings', BPMJ_EDD_ML_DOMAIN ) . '</strong>',
				'desc'	 => __( 'Configure MailerLite Integration Settings', BPMJ_EDD_ML_DOMAIN ),
				'type'	 => 'header'
			),
			array(
				'id'	 => 'bpmj_edd_ml_api',
				'name'	 => __( 'MailerLite API Key', BPMJ_EDD_ML_DOMAIN ),
				'desc'	 => __( 'Enter your MailerLite API key', BPMJ_EDD_ML_DOMAIN ),
				'type'	 => 'text',
				'size'	 => 'regular'
			)
		);

		if ( $this->test_api() ) {

			$standard_fields[ 1 ][ 'desc' ] = __( 'Your MailerLite API key is valid', BPMJ_EDD_ML_DOMAIN );

			$get_lists	 = $this->get_lists();
			$groups		 = !empty( $get_lists ) ? $get_lists : array( '' => 'No groups...' );

			$fields = array(
				array(
					'id'	 => 'bpmj_edd_ml_show_checkout_signup',
					'name'	 => __( 'Show Signup on Checkout', BPMJ_EDD_ML_DOMAIN ),
					'desc'	 => __( 'Allow customers to signup for the list selected below during checkout?', BPMJ_EDD_ML_DOMAIN ),
					'type'	 => 'checkbox'
				),
				array(
					'id'		 => 'bpmj_edd_ml_group',
					'name'		 => __( 'Choose a group', BPMJ_EDD_ML_DOMAIN ),
					'desc'		 => __( 'Select the list you wish to subscribe buyers to', BPMJ_EDD_ML_DOMAIN ),
					'type'		 => 'select',
					'options'	 => $groups
				),
				array(
					'id'	 => 'bpmj_edd_ml_label',
					'name'	 => __( 'Checkout Label', BPMJ_EDD_ML_DOMAIN ),
					'desc'	 => __( 'This is the text shown next to the signup option', BPMJ_EDD_ML_DOMAIN ),
					'type'	 => 'text',
					'size'	 => 'regular'
				),
				array(
					'id'	 => 'bpmj_edd_ml_double_opt_in',
					'name'	 => __( 'Double Opt-In', BPMJ_EDD_ML_DOMAIN ),
					'desc'	 => __( 'When checked, users will be sent a confirmation email after signing up, and will only be added once they have confirmed the subscription.', BPMJ_EDD_ML_DOMAIN ),
					'type'	 => 'checkbox'
				)
			);
		} else {

			if ( !empty( $edd_options[ 'bpmj_edd_ml_api' ] ) ) {
				$api_key = trim( $edd_options[ 'bpmj_edd_ml_api' ] );
			}

			if ( !empty( $api_key ) )
				$standard_fields[ 1 ][ 'desc' ] = __( 'Please enter <b>valid</b> MailerLite API key', BPMJ_EDD_ML_DOMAIN );

			$fields = array();
		}

		$eddmc_settings = array_merge( $standard_fields, $fields );


		if ( version_compare( EDD_VERSION, 2.5, '>=' ) ) {
			$eddmc_settings = array( 'mailerlite' => $eddmc_settings );
		}
		return array_merge( $settings, $eddmc_settings );
	}

	/**
	 * Determines if the checkout signup option should be displayed
	 */
	public function show_checkout_signup() {
		global $edd_options;

		return !empty($edd_options[ 'bpmj_edd_ml_show_checkout_signup' ]) && $edd_options[ 'bpmj_edd_ml_show_checkout_signup' ] == '1';
	}

	/**
	 * Test API call
	 */
	public function test_api() {
        $test_result_cache = $this->get_cached_api_test_result();
        if ($test_result_cache !== null) {
            return $test_result_cache === self::API_TEST_RESULT_OK;
        }

        return $this->check_api_connection();
    }

    private function check_api_connection(): bool
    {
		global $edd_options;
		$test = false;

		if ( !empty( $edd_options[ 'bpmj_edd_ml_api' ] ) ) {
			$api		 = new EDD_MailerLite_API( trim( $edd_options[ 'bpmj_edd_ml_api' ] ) );
			$list_data	 = $api->call( 'subscribers', null, 'GET' );

			if (is_array($list_data) ) {
			    $test = true;
            }
		}

        $this->set_api_test_result_cache($test ? self::API_TEST_RESULT_OK : self::API_TEST_RESULT_ERROR);

		return $test;
	}

    private function get_cached_api_test_result(): ?string
    {
        $api_test_result = Cache::get(self::API_TEST_RESULT_TRANSIENT);

        if ($api_test_result !== self::API_TEST_RESULT_OK && $api_test_result !== self::API_TEST_RESULT_ERROR) {
            return null;
        }

        return $api_test_result;
    }

    private function set_api_test_result_cache(string $result): void
    {
        Cache::set(self::API_TEST_RESULT_TRANSIENT, $result, Cache::EXPIRATION_TIME_1_HOUR);
    }

    private function unset_api_test_result_cache(): void
    {
        Cache::unset(self::API_TEST_RESULT_TRANSIENT);
    }

	/**
	 * Retrieves the groups from MailerLite
	 */
	public function get_lists() {
		global $edd_options;

		$transient_name = 'edd_mailerlite_get_lists';

		$this->lists[] = '';

		$lists_data = get_transient( $transient_name );
		if ( $lists_data ) {
			return $lists_data;
		}

		if ( $this->test_api() ) {

			$api		 = new EDD_MailerLite_API( trim( $edd_options[ 'bpmj_edd_ml_api' ] ) );
			$list_data	 = $api->call( 'groups?limit=500', null, 'GET' );

			if ( !empty( $list_data ) ) {
				if ( !isset( $list_data->error ) ) {
					foreach ( $list_data as $key => $list ) {
						$this->lists[ $list->id ] = $list->name;
					}
				}
			}
		}

		set_transient( $transient_name, (array) $this->lists, 60 * 60 ); // 1 hour

		return (array) $this->lists;
	}

	/**
	 * Subscribe an email to a list
	 */
	public function subscribe_email( $user_info = array(), $list_id = false, $opt_in_overridde = false ) {
		global $edd_options;

		// Make sure an API key has been entered
		if ( !$this->test_api() ) {
			return false;
		}

		// Retrieve the global list ID if none is provided
		if ( !$list_id ) {
			$list_id = !empty( $edd_options[ 'bpmj_edd_ml_group' ] ) ? $edd_options[ 'bpmj_edd_ml_group' ] : false;
			if ( !$list_id ) {
				return false;
			}
		}

		$api = new EDD_MailerLite_API( trim( $edd_options[ 'bpmj_edd_ml_api' ] ) );

		$opt_in = isset( $edd_options[ 'bpmj_edd_ml_double_opt_in' ] ) && $edd_options[ 'bpmj_edd_ml_double_opt_in' ] == '1' && !$opt_in_overridde;
		if ( $opt_in ) {
			$type = 'unconfirmed';
		} else {
			$type = 'active';
		}

		$groups	 = explode( '|', $list_id );
		$args	 = array(
			'email'	 => $user_info[ 'email' ],
			'fields' => array(
				'name'		 => $user_info[ 'first_name' ], //trim( $user_info[ 'first_name' ] . ' ' . $user_info[ 'last_name' ] ),
				//'first_name' => $user_info[ 'first_name' ],
				'last_name'	 => $user_info[ 'last_name' ]
			),
			'type'	 => $type
		);

		foreach ( $groups as $group ) {
			$api->call( 'groups/' . $group . '/subscribers', $args );
		}

		return true;
	}

	/**
	 * Flush the list transient on save
	 */
	public function save_settings( $input ) {
		if ( isset( $input[ 'bpmj_edd_ml_api' ] ) ) {
			$this->clear_cache();
		}
		return $input;
	}

    public function check_connection(): bool
    {
        return $this->test_api();
    }

    public function clear_cache(): void
    {
        delete_transient( 'edd_mailerlite_get_lists' );
        $this->unset_api_test_result_cache();
    }

}

$edd_ml = new BPMJ_EDD_Mailerlite( 'mailerlite', 'MailerLite' );
