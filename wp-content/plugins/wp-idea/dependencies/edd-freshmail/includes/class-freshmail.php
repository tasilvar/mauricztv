<?php
/**
 * Created by PhpStorm.
 * User: psypek
 * Date: 21.12.16
 * Time: 02:39
 */

namespace bpmj\wp\eddfm;


use bpmj\wp\eddfm\api\FreshmailApi;
use bpmj\wpidea\helpers\Cache;
use bpmj\wpidea\helpers\Translator_Static_Helper;
use bpmj\wpidea\integrations\Interface_External_Service_Integration;
use bpmj\wpidea\integrations\Trait_External_Service_Integration;

class Freshmail extends \EDD_Newsletter_V2 implements Interface_External_Service_Integration {
use Trait_External_Service_Integration;
    const SERVICE_NAME = 'Freshmail';

    private const API_TEST_RESULT_TRANSIENT = 'freshmail_api_test_result_cache';
    private const API_TEST_RESULT_OK = 'ok';
    private const API_TEST_RESULT_ERROR = 'error';

    protected ?bool $api_test = null;

	/**
	 * Sets up the checkout label
	 */
	public function init() {
		global $edd_options;
		if ( !empty( $edd_options['bpmj_eddfm_label'] ) ) {
			$this->checkout_label = trim( $edd_options['bpmj_eddfm_label'] );
		} else {
			$this->checkout_label = Translator_Static_Helper::translate('newsletter.sign_up');
		}
		add_filter( 'edd_settings_sections_extensions', array( $this, 'subsection' ), 10, 1 );
        add_action( 'freshmail_clear_cache', [$this, 'clear_cache'] );
	}

	/**
	 * Register our subsection for EDD 2.5
	 */
	function subsection( $sections ) {
		$sections['freshmail'] = __( 'FreshMail', BPMJ_EDDFM_DOMAIN );

		return $sections;
	}

	/**
	 * Registers the plugin settings
	 */
	public function settings( $settings ) {

		global $edd_options;

		$standard_fields = array(
			array(
				'id'   => 'bpmj_eddfm_settings',
				'name' => '<strong>' . __( 'FreshMail Settings', BPMJ_EDDFM_DOMAIN ) . '</strong>',
				'desc' => __( 'Configure FreshMail Integration Settings', BPMJ_EDDFM_DOMAIN ),
				'type' => 'header',
			),
			array(
				'id'   => 'bpmj_eddfm_api',
				'name' => __( 'FreshMail API Key', BPMJ_EDDFM_DOMAIN ),
				'desc' => __( 'Enter your FreshMail API key', BPMJ_EDDFM_DOMAIN ),
				'type' => 'text',
				'size' => 'regular',
			),
			array(
				'id'   => 'bpmj_eddfm_api_secret',
				'name' => __( 'FreshMail API Secret', BPMJ_EDDFM_DOMAIN ),
				'desc' => __( 'Enter your FreshMail API secret', BPMJ_EDDFM_DOMAIN ),
				'type' => 'text',
				'size' => 'regular',
			)
		);

		if ( $this->test_api() ) {

			$standard_fields[1]['desc'] = __( 'Your FreshMail API key is valid', BPMJ_EDDFM_DOMAIN );

			$get_lists = $this->get_lists();
			$groups    = !empty( $get_lists ) ? $get_lists : array( '' => 'No groups...' );

			$fields = array(
				array(
					'id'   => 'bpmj_eddfm_show_checkout_signup',
					'name' => __( 'Show Signup on Checkout', BPMJ_EDDFM_DOMAIN ),
					'desc' => __( 'Allow customers to signup for the list selected below during checkout?', BPMJ_EDDFM_DOMAIN ),
					'type' => 'checkbox',
				),
				array(
					'id'      => 'bpmj_eddfm_group',
					'name'    => __( 'Choose a group', BPMJ_EDDFM_DOMAIN ),
					'desc'    => __( 'Select the list you wish to subscribe buyers to', BPMJ_EDDFM_DOMAIN ),
					'type'    => 'select',
					'options' => $groups,
				),
				array(
					'id'      => 'bpmj_eddfm_group_unsubscribe',
					'name'    => __( 'Choose a unsubscribe group', BPMJ_EDDFM_DOMAIN ),
					'desc'    => __( 'Select the list you wish to unsubscribe buyers from', BPMJ_EDDFM_DOMAIN ),
					'type'    => 'select',
					'options' => $groups,
				),
				array(
					'id'   => 'bpmj_eddfm_label',
					'name' => __( 'Checkout Label', BPMJ_EDDFM_DOMAIN ),
					'desc' => __( 'This is the text shown next to the signup option', BPMJ_EDDFM_DOMAIN ),
					'type' => 'text',
					'size' => 'regular',
				),
				array(
					'id'   => 'bpmj_eddfm_double_opt_in',
					'name' => __( 'Double Opt-In', BPMJ_EDDFM_DOMAIN ),
					'desc' => __( 'When checked, users will be sent a confirmation email after signing up, and will only be added once they have confirmed the subscription.', BPMJ_EDDFM_DOMAIN ),
					'type' => 'checkbox',
				)
			);
			if ( !$this->allow_unsubscribe ) {
				unset( $fields[2] );
			}
		} else {

			if ( !empty( $edd_options['bpmj_eddfm_api'] ) ) {
				$api_key = trim( $edd_options['bpmj_eddfm_api'] );
			}

			if ( !empty( $api_key ) ) {
				$standard_fields[1]['desc'] = __( 'Please enter <b>valid</b> FreshMail API key', BPMJ_EDDFM_DOMAIN );
			}

			if ( !empty( $edd_options['bpmj_eddfm_api_secret'] ) ) {
				$api_secret = trim( $edd_options['bpmj_eddfm_api_secret'] );
			}

			if ( !empty( $api_secret ) ) {
				$standard_fields[2]['desc'] = __( 'Please enter <b>valid</b> FreshMail API secret key', BPMJ_EDDFM_DOMAIN );
			}

			$fields = array();
		}

		$eddfm_settings = array_merge( $standard_fields, $fields );


		if ( version_compare( EDD_VERSION, 2.5, '>=' ) ) {
			$eddfm_settings = array( 'freshmail' => $eddfm_settings );
		}

		return array_merge( $settings, $eddfm_settings );
	}

	/**
	 * @return FreshmailApi
	 */
	public function setup_api() {
		global $edd_options;

		return new FreshmailApi( $edd_options['bpmj_eddfm_api'], $edd_options['bpmj_eddfm_api_secret'] );
	}

	/**
	 * Determines if the checkout signup option should be displayed
	 */
	public function show_checkout_signup() {
		global $edd_options;

		return !empty($edd_options['bpmj_eddfm_show_checkout_signup']) && $edd_options[ 'bpmj_eddfm_show_checkout_signup' ] == '1';
	}

	/**
	 * Test API call
	 */
	public function test_api() {
        if ($this->api_test !== null) {
            return $this->api_test;
        }

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

        if ( !empty( $edd_options['bpmj_eddfm_api'] ) ) {
            $api         = $this->setup_api();
            $test_string = 'pong';
            $response    = $api->ping( $test_string );
            if ( $response->is_success() && $test_string === $response->get_data( 'data' ) ) {
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
	 * Retrieves the groups from FreshMail
	 */
	public function get_lists() {
		$transient_name = 'edd_freshmail_get_lists';

		$lists_data = get_transient( $transient_name );
		if ( $lists_data ) {
			return $lists_data;
		}

		if ( $this->test_api() ) {

			$api      = $this->setup_api();
			$response = $api->get_lists();

			$this->lists     = array();
			$this->lists[''] = __( 'Disable', BPMJ_EDDFM_DOMAIN );
			if ( $response->is_success() && is_array( $response->get_raw_data( 'lists' ) ) ) {
				foreach ( $response->get_raw_data( 'lists' ) as $list ) {
					$this->lists[ $list['subscriberListHash'] ] = $list['name'];
				}
			}
		}

		set_transient( $transient_name, (array) $this->lists, 60 * 60 ); // 1 hour

		return (array) $this->lists;
	}

	/**
	 * Subscribe an email to a list
	 */
	public function subscribe_email( $user_info = array(), $list_hash = false, $opt_in_overridde = false ) {
		global $edd_options;

		// Make sure an API key has been entered
		if ( !$this->test_api() ) {
			return false;
		}

		// Retrieve the global list ID if none is provided
		if ( !$list_hash ) {
			$list_hash = !empty( $edd_options['bpmj_eddfm_group'] ) ? $edd_options['bpmj_eddfm_group'] : false;
			if ( !$list_hash ) {
				return false;
			}
		}

		$api = $this->setup_api();

		$opt_in = isset( $edd_options['bpmj_eddfm_double_opt_in'] ) && $edd_options[ 'bpmj_eddfm_double_opt_in' ] == '1' && !$opt_in_overridde;
		if ( $opt_in ) {
			$state = FreshmailApi::SUBSCRIBER_STATE_PENDING;
		} else {
			$state = FreshmailApi::SUBSCRIBER_STATE_ACTIVE;
		}

		$groups = explode( '|', $list_hash );

		foreach ( $groups as $group ) {
			$api->add_list_custom_field( $group, 'Imię' );
			$api->add_list_custom_field( $group, 'Nazwisko' );
			$api->add_subscriber( $user_info['email'], $group, $state, 1, array(
				'imie'     => $user_info['first_name'],
				'nazwisko' => $user_info['last_name'],
			) );
		}

		return true;
	}

	/**
	 * Unsubscribe an email from a list
	 */
	public function unsubscribe_email( $user_info = array(), $list_hash = false ) {
		global $edd_options;

		// Make sure an API key has been entered
		if ( !$this->test_api() ) {
			return false;
		}

		// Retrieve the global list ID if none is provided
		if ( !$list_hash && $this->allow_unsubscribe ) {
			$list_hash = !empty( $edd_options['bpmj_eddfm_group_unsubscribe'] ) ? $edd_options['bpmj_eddfm_group_unsubscribe'] : false;
			if ( !$list_hash ) {
				return false;
			}
		}

		$api = $this->setup_api();

		$groups = explode( '|', $list_hash );

		foreach ( $groups as $group ) {
			$api->remove_subscriber( $user_info['email'], $group );
		}

		return true;
	}

	/**
	 * Flush the list transient on save
	 */
	public function save_settings( $input ) {
		if ( isset( $input['bpmj_eddfm_api_key'] ) ) {
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
        delete_transient( 'edd_freshmail_get_lists' );
        $this->unset_api_test_result_cache();
    }

}
