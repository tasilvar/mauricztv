<?php
/**
 * Created by PhpStorm.
 * User: psypek
 * Date: 21.12.16
 * Time: 02:39
 */

namespace bpmj\wp\eddact;


use bpmj\wp\eddact\api\ActiveCampaignApi;
use bpmj\wpidea\helpers\Cache;
use bpmj\wpidea\helpers\Translator_Static_Helper;
use bpmj\wpidea\integrations\Interface_External_Service_Integration;
use bpmj\wpidea\integrations\Trait_External_Service_Integration;

class ActiveCampaign extends \EDD_Newsletter_V2  implements Interface_External_Service_Integration{
    use Trait_External_Service_Integration;

    private const API_TEST_RESULT_TRANSIENT = 'activecampaign_api_test_result_cache';
    private const API_TEST_RESULT_OK = 'ok';
    private const API_TEST_RESULT_ERROR = 'error';

    const SERVICE_NAME = 'Active Campaign';

	/**
	 * @var array
	 */
	public $tags = array();

	/**
	 * @var array
	 */
	public $forms = array();

	/**
	 * @var boolean
	 */
	protected $api_test;

	/**
	 * @var ActiveCampaignApi
	 */
	protected $api;

	/**
	 * Sets up the checkout label
	 */
	public function init() {
		global $edd_options;
		if ( ! empty( $edd_options[ 'bpmj_eddact_label' ] ) ) {
			$this->checkout_label = trim( $edd_options[ 'bpmj_eddact_label' ] );
		} else {
			$this->checkout_label = Translator_Static_Helper::translate('newsletter.sign_up');
		}
		add_filter( 'edd_settings_sections_extensions', array( $this, 'subsection' ), 10, 1 );
		add_filter( 'edd_settings_general_sanitize', array( $this, 'save_settings' ) );
        add_action( 'activecampaign_clear_cache', [$this, 'clear_cache'] );
		if ( isset( $edd_options[ 'bpmj_eddact_token' ] ) && isset( $edd_options[ 'bpmj_eddact_url' ] ) ) {
			$this->upgrade_from_v1();
		}
	}

	/**
	 * Register our subsection for EDD 2.5
	 *
	 * @param array $sections
	 *
	 * @return array
	 */
	function subsection( $sections ) {
		$sections[ 'activecampaign' ] = __( 'ActiveCampaign', BPMJ_EDDACT_DOMAIN );

		return $sections;
	}

	/**
	 * Registers the plugin settings
	 *
	 * @param array $settings
	 *
	 * @return array
	 */
	public function settings( $settings ) {

		global $edd_options;


		$standard_fields = array(
			array(
				'id'   => 'bpmj_eddact_settings',
				'name' => '<strong>' . __( 'ActiveCampaign Settings', BPMJ_EDDACT_DOMAIN ) . '</strong>',
				'desc' => __( 'Configure ActiveCampaign Integration Settings', BPMJ_EDDACT_DOMAIN ),
				'type' => 'header',
			),
			array(
				'id'   => 'bpmj_eddact_api_url',
				'name' => __( 'ActiveCampaign API URL', BPMJ_EDDACT_DOMAIN ),
				'desc' => __( 'Enter your ActiveCampaign API URL', BPMJ_EDDACT_DOMAIN ),
				'type' => 'text',
				'size' => 'regular',
			),
			array(
				'id'   => 'bpmj_eddact_api_token',
				'name' => __( 'ActiveCampaign API Token', BPMJ_EDDACT_DOMAIN ),
				'desc' => __( 'Enter your ActiveCampaign API token', BPMJ_EDDACT_DOMAIN ),
				'type' => 'text',
				'size' => 'regular',
			)
		);

		if ( $this->test_api() ) {

			$standard_fields[ 1 ][ 'desc' ] = __( 'Your ActiveCampaign API key is valid', BPMJ_EDDACT_DOMAIN );

			$get_lists = $this->get_lists();
			$groups    = ! empty( $get_lists ) ? $get_lists : array( '' => 'No lists...' );

			$get_tags = $this->get_tags();
			$tags     = ! ( empty( $get_tags ) ) ? $get_tags : array( '' => 'No tags...' );

			$get_forms = $this->get_forms();
			$forms     = ! empty( $get_forms ) ? $get_forms : array( '' => 'No forms...' );

			$fields = array(
				array(
					'id'   => 'bpmj_eddact_show_checkout_signup',
					'name' => __( 'Show Signup on Checkout', BPMJ_EDDACT_DOMAIN ),
					'desc' => __( 'Allow customers to signup for the list selected below during checkout?', BPMJ_EDDACT_DOMAIN ),
					'type' => 'checkbox',
				),
				array(
					'id'      => 'bpmj_eddact_list',
					'name'    => __( 'Choose a list', BPMJ_EDDACT_DOMAIN ),
					'desc'    => __( 'Select the list you wish to subscribe buyers to', BPMJ_EDDACT_DOMAIN ),
					'type'    => 'select',
					'options' => $groups,
				),
				array(
					'id'      => 'bpmj_eddact_list_unsubscribe',
					'name'    => __( 'Choose a unsubscribe list', BPMJ_EDDACT_DOMAIN ),
					'desc'    => __( 'Select the list you wish to unsubscribe buyers from', BPMJ_EDDACT_DOMAIN ),
					'type'    => 'select',
					'options' => $groups,
				),
				array(
					'id'      => 'bpmj_eddact_tag',
					'name'    => __( 'Choose a tag', BPMJ_EDDACT_DOMAIN ),
					'desc'    => __( 'Select the tag you wish to add buyers to', BPMJ_EDDACT_DOMAIN ),
					'type'    => 'activecampaign_tags',
					'options' => $tags,
				),
				array(
					'id'      => 'bpmj_eddact_tag_unsubscribe',
					'name'    => __( 'Choose a unsubscribe tag', BPMJ_EDDACT_DOMAIN ),
					'desc'    => __( 'Select the tag you wish to remove buyers from', BPMJ_EDDACT_DOMAIN ),
					'type'    => 'activecampaign_tags',
					'options' => $tags,
				),
				array(
					'id'   => 'bpmj_eddact_label',
					'name' => __( 'Checkout Label', BPMJ_EDDACT_DOMAIN ),
					'desc' => __( 'This is the text shown next to the signup option', BPMJ_EDDACT_DOMAIN ),
					'type' => 'text',
					'size' => 'regular',
				),
				array(
					'id'      => 'bpmj_eddact_form_id',
					'name'    => __( 'Confirmation form', BPMJ_EDDACT_DOMAIN ),
					'desc'    => __( 'Select subscription confirmation form. By doing so you can enable double opt-in for subscribing a user (recommended). Forms are created in administration panel at <b>ActiveCampaign.com / Apps / Add form</b>.', BPMJ_EDDACT_DOMAIN ),
					'type'    => 'select',
					'options' => $forms,
				)
			);
			if ( ! $this->allow_unsubscribe ) {
				unset( $fields[ 2 ], $fields[ 4 ] );
			}
		} else {

			if ( ! empty( $edd_options[ 'bpmj_eddact_api_token' ] ) ) {
				$api_token = trim( $edd_options[ 'bpmj_eddact_api_token' ] );
			}

			if ( ! empty( $api_token ) ) {
				$standard_fields[ 2 ][ 'desc' ] = __( 'Please enter <b>valid</b> ActiveCampaign API token', BPMJ_EDDACT_DOMAIN );
			}

			$fields = array();
		}

		$eddact_settings = array_merge( $standard_fields, $fields );


		if ( version_compare( EDD_VERSION, 2.5, '>=' ) ) {
			$eddact_settings = array( 'activecampaign' => $eddact_settings );
		}

		return array_merge( $settings, $eddact_settings );
	}

	/**
	 * @return ActiveCampaignApi
	 */
	public function setup_api() {
		global $edd_options;

		if ( ! isset( $this->api ) ) {
			$this->api = new ActiveCampaignApi( $edd_options[ 'bpmj_eddact_api_url' ], $edd_options[ 'bpmj_eddact_api_token' ] );
		}

		return $this->api;
	}

	/**
	 * Determines if the checkout signup option should be displayed
	 *
	 * @return boolean
	 */
	public function show_checkout_signup() {
		global $edd_options;

		return !empty($edd_options['bpmj_eddact_show_checkout_signup']) && $edd_options[ 'bpmj_eddact_show_checkout_signup' ] == '1';
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

		if ( ! empty( $edd_options[ 'bpmj_eddact_api_url' ] ) && ! empty( $edd_options[ 'bpmj_eddact_api_token' ] ) ) {
			if ( ! isset( $this->api_test ) ) {
				$api            = $this->setup_api();
				$this->api_test = $api->ping();
			}
			$test = $this->api_test;
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
	 * Retrieves the groups from ActiveCampaign
	 *
	 * @return array
	 */
	public function get_lists() {
		$transient_name = 'edd_activecampaign_get_lists';

		$lists_data = get_transient( $transient_name );
		if ( $lists_data ) {
			return $lists_data;
		}

		if ( $this->test_api() ) {

			$api      = $this->setup_api();
			$response = $api->get_lists();

			$this->lists       = array();
			$this->lists[ '' ] = __( 'Disable', BPMJ_EDDACT_DOMAIN );
			if ( $response->is_success() ) {
				foreach ( $response->get_data_array() as $list ) {
					$this->lists[ $list[ 'id' ] ] = $list[ 'name' ];
				}
			}
		}

		set_transient( $transient_name, (array) $this->lists, 60 * 60 ); // 1 hour

		return (array) $this->lists;
	}

	/**
	 * Subscribe an email to a list
	 *
	 * @param array $user_info
	 * @param array $lists
	 * @param array $lists_unsubscribe
	 *
	 * @return bool
	 */
	public function subscribe_email( $user_info = array(), $lists = array(), array $lists_unsubscribe = array() ) {
		global $edd_options;

		// Retrieve the global list ID if none is provided
		if ( ! $lists ) {
			$lists = ! empty( $edd_options[ 'bpmj_eddact_list' ] ) ? (array) $edd_options[ 'bpmj_eddact_list' ] : false;
			if ( ! $lists ) {
				return false;
			}
		}

		if ( ! $lists_unsubscribe ) {
			$lists_unsubscribe = ! empty( $edd_options[ 'bpmj_eddact_list_unsubscribe' ] ) ? (array) $edd_options[ 'bpmj_eddact_list_unsubscribe' ] : array();
		}

		$api = $this->setup_api();

		$form_id = $edd_options[ 'bpmj_eddact_form_id' ] ?? null;
		if ( $form_id ) {
			$state = ActiveCampaignApi::SUBSCRIBER_STATE_UNCONFIRMED;
		} else {
			$state = ActiveCampaignApi::SUBSCRIBER_STATE_ACTIVE;
		}

		$api->add_subscriber( $user_info[ 'email' ], $user_info[ 'first_name' ], $user_info[ 'last_name' ], $lists, $lists_unsubscribe, $state, $form_id );

		return true;
	}

	/**
	 * Flush the list transient on save
	 *
	 * @param array $input
	 *
	 * @return array
	 */
	public function save_settings( $input ) {
		if ( isset( $input[ 'bpmj_eddact_api_token' ] ) ) {
			$this->clear_cache();
		}

		return $input;
	}

	/**
	 *
	 */
	protected function upgrade_from_v1() {
		global $edd_options, $wpdb;

		$edd_options[ 'bpmj_eddact_api_url' ]       = $edd_options[ 'bpmj_eddact_url' ];
		$edd_options[ 'bpmj_eddact_api_token' ]     = $edd_options[ 'bpmj_eddact_token' ];
		$edd_options[ 'bpmj_eddact_list_register' ] = isset( $edd_options[ 'bpmj_eddact_register_user_list' ] ) ? $edd_options[ 'bpmj_eddact_register_user_list' ] : '';
		$edd_options[ 'bpmj_eddact_form_id' ]       = isset( $edd_options[ 'bpmj_eddact_double_option' ] ) ? $edd_options[ 'bpmj_eddact_double_option' ] : '';

		unset( $edd_options[ 'bpmj_eddact_token' ], $edd_options[ 'bpmj_eddact_url' ], $edd_options[ 'bpmj_eddact_double_option' ], $edd_options[ 'bpmj_eddact_register_user_list' ] );
		$table_name = $wpdb->prefix . 'bpmj_edd_activecampaign';
		$wpdb->query( "DROP TABLE IF EXISTS $table_name" );

		$cron_hook = 'bpmj_eddact_http_request_cron_hook';
		if ( $timestamp = wp_next_scheduled( $cron_hook ) ) {
			wp_unschedule_event( $timestamp, $cron_hook );
		}

		update_option( 'edd_settings', $edd_options );
	}

	/**
	 * @return array
	 */
	protected function get_tags() {
		$transient_name = 'edd_activecampaign_get_tags';

		$tags_data = get_transient( $transient_name );
		if ( $tags_data ) {
			return $tags_data;
		}

		if ( $this->test_api() ) {

			$api      = $this->setup_api();
			$response = $api->get_tags();

			$this->tags       = array();
			$this->tags[ '' ] = __( 'Disable', BPMJ_EDDACT_DOMAIN );
			if ( $response->is_success() ) {
				foreach ( $response->get_data_array() as $tag ) {
					$this->tags[ $tag[ 'id' ] ] = $tag[ 'name' ];
				}
			}
		}

		set_transient( $transient_name, (array) $this->tags, 60 * 60 ); // 1 hour

		return (array) $this->tags;
	}

	/**
	 * @return array
	 */
	public function get_forms() {
		$transient_name = 'edd_activecampaign_get_forms';

		$forms_data = get_transient( $transient_name );
		if ( $forms_data ) {
			return $forms_data;
		}

		if ( $this->test_api() ) {

			$api      = $this->setup_api();
			$response = $api->get_forms();

			$this->forms       = array();
			$this->forms[ '' ] = __( 'Disable', BPMJ_EDDACT_DOMAIN );
			if ( $response->is_success() ) {
				foreach ( $response->get_data_array() as $form ) {
					$this->forms[ $form[ 'id' ] ] = $form[ 'name' ];
				}
			}
		}

		set_transient( $transient_name, $this->forms, 60 * 60 ); // 1 hour

		return (array) $this->forms;
	}

	/**
	 * Display the metabox, which is a list of newsletter lists
	 */
	public function render_metabox() {

		global $post;

		parent::render_metabox();

		echo '<p>' . __( 'Specify tags (separated by commas) that will be added to contacts in Active Campaign on completed purchase.', BPMJ_EDDACT_DOMAIN ) . '</p>';
		$subscribe_key   = '_edd_' . esc_attr( $this->id ) . '_tags';
		$subscribe_value = get_post_meta( $post->ID, $subscribe_key, true );
		edd_activecampaign_tags_callback( array(
			'value'      => $subscribe_value,
			'input_name' => $subscribe_key,
			'id'         => $subscribe_key,
			'name'       => $subscribe_key,
		) );

		if ( $this->allow_unsubscribe ) {
			echo '<p>' . __( 'Specify tags (separated by commas) that will be removed from contacts in Active Campaign on completed purchase.', BPMJ_EDDACT_DOMAIN ) . '</p>';
			$unsubscribe_key   = '_edd_' . esc_attr( $this->id ) . '_tags_unsubscribe';
			$unsubscribe_value = get_post_meta( $post->ID, $unsubscribe_key, true );
			edd_activecampaign_tags_callback( array(
				'value'      => $unsubscribe_value,
				'input_name' => $unsubscribe_key,
				'id'         => $unsubscribe_key,
				'name'       => $unsubscribe_key,
			) );
		};
	}

	/**
	 * Save the metabox
	 *
	 * @param array $fields
	 *
	 * @return array
	 */
	public function save_metabox( $fields ) {

		$fields   = parent::save_metabox( $fields );
		$fields[] = '_edd_' . esc_attr( $this->id ) . '_tags';
		if ( $this->allow_unsubscribe ) {
			$fields[] = '_edd_' . esc_attr( $this->id ) . '_tags_unsubscribe';
		}

		return $fields;
	}

	/**
	 * Check if a customer needs to be subscribed on completed purchase of specific products
	 *
	 * @param int $download_id ID of purchased download
	 * @param int $payment_id ID of payment
	 * @param string $download_type Type of download
	 */
	public function completed_download_purchase_signup( $download_id = 0, $payment_id = 0, $download_type = 'default' ) {

        $is_mailing_disabled = apply_filters( 'wpi_disable_mailing', false );
        if ( $is_mailing_disabled ) {
            return false;
        }

		$user_info         = edd_get_payment_meta_user_info( $payment_id );
		$lists             = $this->get_lists_for_post( $download_id );
		$tags              = $this->get_tags_for_post( $download_id );
		$lists_unsubscribe = $this->get_unsubscribe_lists_for_post( $download_id );
		$tags_unsubscribe  = $this->get_unsubscribe_tags_for_post( $download_id );

		if ( 'bundle' == $download_type ) {

			// Get the lists of all items included in the bundle

			$downloads = edd_get_bundled_products( $download_id );
			if ( $downloads ) {
				foreach ( $downloads as $d_id ) {
					$lists             = array_merge( $this->get_lists_for_post( $d_id ), $lists );
					$tags              = array_merge( $this->get_tags_for_post( $d_id ), $tags );
					$lists_unsubscribe = array_merge( $this->get_unsubscribe_lists_for_post( $d_id ), $lists_unsubscribe );
					$tags_unsubscribe  = array_merge( $this->get_unsubscribe_tags_for_post( $d_id ), $tags_unsubscribe );
				}
			}
		}

		if ( empty( $lists ) || ! $this->test_api() ) {
			return;
		}
		$lists = is_array( $lists ) ? array_unique( $lists ) : array();
		$tags  = is_array( $tags ) ? array_unique( $tags ) : array();

        $lists = \array_filter($lists, static function ($element) {
            return $element !== "off";
        });

        $tags = \array_filter($tags, static function ($element) {
            return $element !== "off";
        });

        $lists_unsubscribe = \array_filter($lists_unsubscribe, static function ($element) {
            return $element !== "off";
        });

		$lists = apply_filters( 'bpmj_eddac_on_complete_purchase_lists', $lists, $payment_id );
		$lists_unsubscribe = apply_filters( 'bpmj_eddac_on_complete_purchase_lists_unsubscribe', $lists_unsubscribe, $payment_id );

		$this->subscribe_email( $user_info, $lists, $lists_unsubscribe );
		$this->add_to_tags( $user_info, $tags );
		$tags_unsubscribe = is_array( $tags_unsubscribe ) ? array_unique( $tags_unsubscribe ) : array();
		$this->remove_from_tags( $user_info, $tags_unsubscribe );
	}

	/**
	 * @param array $user_info
	 * @param array $tags_unsubscribe
	 *
	 * @return bool
	 */
	protected function remove_from_tags( $user_info, array $tags_unsubscribe = array() ) {
		global $edd_options;

		if ( ! $tags_unsubscribe && $this->allow_unsubscribe ) {
			$tags_unsubscribe = ! empty( $edd_options[ 'bpmj_eddact_tag_unsubscribe' ] ) ? explode( ',', $edd_options[ 'bpmj_eddact_tag_unsubscribe' ] ) : false;
			if ( ! $tags_unsubscribe ) {
				return false;
			}
		}

		$api = $this->setup_api();
		foreach ( $tags_unsubscribe as $tag ) {
			$api->remove_subscriber_tag( $user_info[ 'email' ], $tag );
		}

		return true;
	}

	/**
	 * @param array $user_info
	 * @param array $tags
	 *
	 * @return bool
	 */
	protected function add_to_tags( $user_info, array $tags = array() ) {
		global $edd_options;

		if ( ! $tags ) {
			$tags = ! empty( $edd_options[ 'bpmj_eddact_tag' ] ) ? explode( ',', $edd_options[ 'bpmj_eddact_tag' ] ) : false;
			if ( ! $tags ) {
				return false;
			}
		}

		$api = $this->setup_api();
		foreach ( $tags as $tag ) {
			$api->add_subscriber_tag( $user_info[ 'email' ], $tag );
		}

		return true;
	}

	/**
	 * @param int $post_id
	 *
	 * @return array
	 */
	public function get_lists_for_post( $post_id ) {
		$lists = get_post_meta( $post_id, '_edd_' . $this->id, true );
		if ( empty( $lists ) ) {
			// backward compatibility with v1
			$lists = get_post_meta( $post_id, 'edd_activecampaign', true );
		}

		if ( ! empty( $lists ) && ! is_array( $lists ) ) {
			$lists = explode( '|', $lists );
		}

		return $lists ? (array) $lists : array();
	}

	/**
	 * @param int $post_id
	 *
	 * @return array
	 */
	public function get_unsubscribe_lists_for_post( $post_id ) {
		$lists = get_post_meta( $post_id, '_edd_' . $this->id . '_unsubscribe', true );
		if ( empty( $lists ) ) {
			// backward compatibility with v1
			$lists = get_post_meta( $post_id, 'edd_activecampaign_delete', true );
		}

		if ( ! empty( $lists ) && ! is_array( $lists ) ) {
			$lists = explode( '|', $lists );
		}

		return $lists ? (array) $lists : array();
	}

	/**
	 * @param int $post_id
	 *
	 * @return array
	 */
	public function get_tags_for_post( $post_id ) {
		$tags = get_post_meta( $post_id, '_edd_' . $this->id . '_tags', true );

		if ( ! empty( $tags ) && ! is_array( $tags ) ) {
			$tags = explode( ',', $tags );
		}

		return $tags ? (array) $tags : array();
	}

	/**
	 * @param int $post_id
	 *
	 * @return array
	 */
	public function get_unsubscribe_tags_for_post( $post_id ) {
		$tags = get_post_meta( $post_id, '_edd_' . $this->id . '_tags_unsubscribe', true );

		if ( ! empty( $tags ) && ! is_array( $tags ) ) {
			$tags = explode( ',', $tags );
		}

		return $tags ? (array) $tags : array();
	}

	/**
	 * @param array $posted
	 * @param array $user_info
	 * @param array $valid_data
	 */
	public function checkout_signup( $posted, $user_info, $valid_data ) {
		parent::checkout_signup( $posted, $user_info, $valid_data );

		if ( isset( $posted[ 'edd_' . $this->id . '_signup' ] ) ) {
			$this->add_to_tags( $user_info );
		}
		if ( $this->allow_unsubscribe ) {
			$this->remove_from_tags( $user_info );
		}
	}

    public function check_connection(): bool
    {
        return $this->test_api();
    }

    public function clear_cache(): void
    {
        delete_transient( 'edd_activecampaign_get_lists' );
        delete_transient( 'edd_activecampaign_get_tags' );
        delete_transient( 'edd_activecampaign_get_forms' );
        $this->unset_api_test_result_cache();
    }
}
