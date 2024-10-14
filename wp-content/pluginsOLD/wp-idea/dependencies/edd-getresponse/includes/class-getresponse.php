<?php
/**
 * Created by PhpStorm.
 * User: psypek
 * Date: 21.12.16
 * Time: 02:39
 */

namespace bpmj\wp\eddres;


use bpmj\wp\eddres\api\GetresponseApi;
use bpmj\wpidea\integrations\Interface_External_Service_Integration;
use bpmj\wpidea\integrations\Trait_External_Service_Integration;
use bpmj\wpidea\helpers\Translator_Static_Helper;

class Getresponse extends \EDD_Newsletter_V2 implements Interface_External_Service_Integration {
use Trait_External_Service_Integration;

    const SERVICE_NAME = 'Get Response';
	/**
	 * @var boolean
	 */
	protected $api_test;

	/**
	 * @var array
	 */
	protected $tags = array();

	/**
	 * Sets up the checkout label
	 */
	public function init() {
		global $edd_options;
		if ( ! empty( $edd_options[ 'bpmj_eddres_label' ] ) ) {
			$this->checkout_label = trim( $edd_options[ 'bpmj_eddres_label' ] );
		} else {
			$this->checkout_label = Translator_Static_Helper::translate('newsletter.sign_up');
		}
		add_filter( 'edd_settings_sections_extensions', array( $this, 'subsection' ), 10, 1 );
		add_filter( 'edd_settings_extensions_sanitize', array( $this, 'save_settings' ) );
        add_action( 'getresponse_clear_cache', [$this, 'clear_cache'] );
	}

	/**
	 * Register our subsection for EDD 2.5
	 */
	function subsection( $sections ) {
		$sections[ 'getresponse' ] = __( 'GetResponse', BPMJ_EDDRES_DOMAIN );

		return $sections;
	}

	/**
	 * Registers the plugin settings
	 */
	public function settings( $settings ) {

		global $edd_options;

		$standard_fields = array(
			array(
				'id'   => 'bpmj_eddres_settings',
				'name' => '<strong>' . __( 'GetResponse Settings', BPMJ_EDDRES_DOMAIN ) . '</strong>',
				'desc' => __( 'Configure GetResponse Integration Settings', BPMJ_EDDRES_DOMAIN ),
				'type' => 'header',
			),
			array(
				'id'   => 'bpmj_eddres_token',
				'name' => __( 'GetResponse API Key', BPMJ_EDDRES_DOMAIN ),
				'desc' => __( 'Enter your GetResponse API key', BPMJ_EDDRES_DOMAIN ),
				'type' => 'text',
				'size' => 'regular',
			),
		);

		if ( $this->test_api() ) {

			$standard_fields[ 1 ][ 'desc' ] = __( 'Your GetResponse API key is valid', BPMJ_EDDRES_DOMAIN );

			$get_lists = $this->get_lists();
			$groups    = ! empty( $get_lists ) ? $get_lists : array( '' => 'No groups...' );

			$fields = array(
				array(
					'id'   => 'bpmj_eddres_show_checkout_signup',
					'name' => __( 'Show Signup on Checkout', BPMJ_EDDRES_DOMAIN ),
					'desc' => __( 'Allow customers to signup for the list selected below during checkout?', BPMJ_EDDRES_DOMAIN ),
					'type' => 'checkbox',
				),
				array(
					'id'      => 'bpmj_eddres_list',
					'name'    => __( 'Choose a list', BPMJ_EDDRES_DOMAIN ),
					'desc'    => __( 'Select the list you wish to subscribe buyers to', BPMJ_EDDRES_DOMAIN ),
					'type'    => 'select',
					'options' => $groups,
				),
				array(
					'id'      => 'bpmj_eddres_list_unsubscribe',
					'name'    => __( 'Choose a unsubscribe list', BPMJ_EDDRES_DOMAIN ),
					'desc'    => __( 'Select the list you wish to unsubscribe buyers from', BPMJ_EDDRES_DOMAIN ),
					'type'    => 'select',
					'options' => $groups,
				),
				array(
					'id'   => 'bpmj_eddres_label',
					'name' => __( 'Checkout Label', BPMJ_EDDRES_DOMAIN ),
					'desc' => __( 'This is the text shown next to the signup option', BPMJ_EDDRES_DOMAIN ),
					'type' => 'text',
					'size' => 'regular',
				),
			);
			if ( ! $this->allow_unsubscribe ) {
				unset( $fields[ 2 ] );
			}
		} else {

			if ( ! empty( $edd_options[ 'bpmj_eddres_token' ] ) ) {
				$api_key = trim( $edd_options[ 'bpmj_eddres_token' ] );
			}

			if ( ! empty( $api_key ) ) {
				$standard_fields[ 1 ][ 'desc' ] = __( 'Please enter <b>valid</b> GetResponse API key', BPMJ_EDDRES_DOMAIN );
			}

			$fields = array();
		}

		$eddres_settings = array_merge( $standard_fields, $fields );


		if ( version_compare( EDD_VERSION, 2.5, '>=' ) ) {
			$eddres_settings = array( 'getresponse' => $eddres_settings );
		}

		return array_merge( $settings, $eddres_settings );
	}

	/**
	 * @return GetresponseApi
	 */
	public function setup_api() {
		global $edd_options;

		return new GetresponseApi( $edd_options[ 'bpmj_eddres_token' ] );
	}

	/**
	 * Determines if the checkout signup option should be displayed
	 */
	public function show_checkout_signup() {
		global $edd_options;

		return !empty($edd_options[ 'bpmj_eddres_show_checkout_signup' ]) && $edd_options[ 'bpmj_eddres_show_checkout_signup' ] == '1';
	}

	/**
	 * Test API call
	 */
	public function test_api() {
		global $edd_options;
		if ( isset( $this->api_test ) ) {
			return $this->api_test;
		}
		$test = false;

		if ( ! empty( $edd_options[ 'bpmj_eddres_token' ] ) ) {
			if ( ! isset( $this->api_test ) ) {
				$api            = $this->setup_api();
				$this->api_test = $api->ping();
			}
			$test = $this->api_test;
		}

		return $test;
	}

	/**
	 * Retrieves the groups from GetResponse
	 */
	public function get_lists() {
		$transient_name = 'edd_getresponse_get_lists';

		$lists_data = get_transient( $transient_name );
		if ( $lists_data ) {
			return $lists_data;
		}

		if ( $this->test_api() ) {

			$api      = $this->setup_api();
			$response = $api->get_lists();

			$this->lists       = array();
			$this->lists[ '' ] = __( 'Disable', BPMJ_EDDRES_DOMAIN );
			if ( $response->is_success() && is_array( $response->get_data() ) ) {
				foreach ( $response->get_data() as $list ) {
					$this->lists[ $list[ 'campaignId' ] ] = $list[ 'name' ];
				}
			}
		}

		set_transient( $transient_name, (array) $this->lists, 60 * 60 ); // 1 hour

		return (array) $this->lists;
	}

	/**
	 * Retrieves the groups from GetResponse
	 */
	public function get_tags() {
		$transient_name = 'edd_getresponse_get_tags';

		$tags_data = get_transient( $transient_name );
		if ( $tags_data ) {
			return $tags_data;
		}

		if ( $this->test_api() ) {

			$api      = $this->setup_api();
			$response = $api->get_tags();

			$this->tags = array();
			if ( $response->is_success() && is_array( $response->get_data() ) ) {
				foreach ( $response->get_data() as $tag ) {
					$this->tags[ $tag[ 'tagId' ] ] = $tag[ 'name' ];
				}
			}
		}

		set_transient( $transient_name, (array) $this->tags, 60 * 60 ); // 1 hour

		return (array) $this->tags;
	}

	/**
	 * Subscribe an email to a list
	 *
	 * @param array $user_info
	 * @param string|bool $list_hash
	 * @param array $list_tags
	 *
	 * @return bool
	 */
	public function subscribe_email( $user_info = array(), $list_hash = false, $list_tags = array() ) {
		global $edd_options;

		// Make sure an API key has been entered
		if ( ! $this->test_api() ) {
			return false;
		}

		// Retrieve the global list ID if none is provided
		if ( ! $list_hash ) {
			$list_hash = ! empty( $edd_options[ 'bpmj_eddres_list' ] ) ? $edd_options[ 'bpmj_eddres_list' ] : false;
			if ( ! $list_hash ) {
				return false;
			}
		}

		$api = $this->setup_api();

		$groups = explode( '|', $list_hash );

		foreach ( $groups as $group ) {
			$api->add_subscriber( $user_info[ 'first_name' ] . ' ' . $user_info[ 'last_name' ], $user_info[ 'email' ], $group, $list_tags );
		}

		return true;
	}

	/**
	 * Unsubscribe an email from a list
	 *
	 * @param array $user_info
	 * @param string|array|bool $list_hash
	 *
	 * @return bool
	 */
	public function unsubscribe_email( $user_info = array(), $list_hash = false ) {
		global $edd_options;

		// Make sure an API key has been entered
		if ( ! $this->test_api() ) {
			return false;
		}

		// Retrieve the global list ID if none is provided
		if ( ! $list_hash && $this->allow_unsubscribe ) {
			$list_hash = ! empty( $edd_options[ 'bpmj_eddres_list_unsubscribe' ] ) ? $edd_options[ 'bpmj_eddres_list_unsubscribe' ] : false;
			if ( ! $list_hash ) {
				return false;
			}
		}

		$api = $this->setup_api();

		if ( is_array( $list_hash ) ) {
			$groups = $list_hash;
		} else {
			$groups = explode( '|', $list_hash );
		}

		foreach ( $groups as $group ) {
			$api->remove_subscriber( $user_info[ 'email' ], $group );
		}

		return true;
	}

	/**
	 * Flush the list transient on save
	 */
	public function save_settings( $input ) {
		if ( isset( $input[ 'bpmj_eddres_token' ] ) ) {
			$this->clear_cache();
		}

		return $input;
	}

	/**
	 * Display the metabox, which is a list of newsletter lists
	 */
	public function render_metabox() {

		global $post;

		parent::render_metabox();

		$tags = $this->get_tags();
		if ( ! empty( $tags ) ) {
			$checked = (array) get_post_meta( $post->ID, '_edd_' . esc_attr( $this->id ) . '_tags', true );
			echo '<p>' . __( 'Add the following tags to subscribers.', 'edd-convertkit' ) . '</p>';
			foreach ( $tags as $tag_id => $tag_name ) {
				echo '<label>';
				echo '<input type="checkbox" name="_edd_' . esc_attr( $this->id ) . '_tags[]" value="' . esc_attr( $tag_id ) . '"' . checked( true, in_array( $tag_id, $checked ), false ) . '>';
				echo '&nbsp;' . $tag_name;
				echo '</label><br/>';
			}
		}
	}

	/**
	 * Save the metabox
	 *
	 * @params array $fields
	 */
	public function save_metabox( $fields ) {

		$fields   = parent::save_metabox( $fields );
		$fields[] = '_edd_' . esc_attr( $this->id ) . '_tags';

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

		$user_info = edd_get_payment_meta_user_info( $payment_id );
		$lists     = get_post_meta( $download_id, '_edd_' . $this->id, true );
		if ( ! is_array( $lists ) && ! empty( $lists ) ) {
			$lists = array( $lists );
		} else if ( empty( $lists ) ) {
			$lists = array();
		}
		$tags      = get_post_meta( $download_id, '_edd_' . $this->id . '_tags', true );
		if ( ! is_array( $tags ) && ! empty( $tags ) ) {
			$tags = array( $tags );
		} else if ( empty( $tags ) ) {
			$tags = array();
		}
		$lists_unsubscribe = get_post_meta( $download_id, '_edd_' . $this->id . '_unsubscribe', true );
		if ( empty( $lists_unsubscribe ) ) {
			$lists_unsubscribe = array();
		} else if ( ! is_array( $lists_unsubscribe ) ) {
			$lists_unsubscribe = array( $lists_unsubscribe );
		}

		if ( 'bundle' == $download_type ) {

			// Get the lists of all items included in the bundle

			$downloads = edd_get_bundled_products( $download_id );
			if ( $downloads ) {
				foreach ( $downloads as $d_id ) {
					$d_lists             = get_post_meta( $d_id, '_edd_' . $this->id, true );
					$d_tags              = get_post_meta( $d_id, '_edd_' . $this->id . '_tags', true );
					$d_lists_unsubscribe = get_post_meta( $d_id, '_edd_' . $this->id . '_unsubscribe', true );
					if ( is_array( $d_lists ) ) {
						$lists = array_merge( $d_lists, $lists );
					}
					if ( is_array( $d_tags ) ) {
						$tags = array_merge( $d_tags, $tags );
					}
					if ( is_array( $d_lists_unsubscribe ) ) {
						$lists_unsubscribe = array_merge( $d_lists_unsubscribe, $lists_unsubscribe );
					}
				}
			}
		}

		$lists = is_array( $lists ) ? array_unique( $lists ) : array();
		$tags  = is_array( $tags ) ? array_unique( $tags ) : array();

        $lists = \array_filter($lists, static function ($element) {
            return $element !== "off";
        });

        $tags = \array_filter($tags, static function ($element) {
            return $element !== "off";
        });

		if ( empty( $lists ) && ! empty( $tags ) ) {
			// Add to tags only
			$this->add_to_tags( $user_info, $tags );
		}

		foreach ( $lists as $list ) {
			$this->subscribe_email( $user_info, $list, $tags );
		}

		$lists_unsubscribe = is_array( $lists_unsubscribe ) ? array_unique( $lists_unsubscribe ) : array();

        $lists_unsubscribe = \array_filter($lists_unsubscribe, static function ($element) {
            return $element !== "off";
        });

		if ( ! empty( $lists_unsubscribe ) ) {
			$this->unsubscribe_email( $user_info, $lists_unsubscribe );
		}

	}

	/**
	 * @param array $user_info
	 * @param array $tags
	 *
	 * @return bool
	 */
	public function add_to_tags( $user_info, $tags ) {
		// Make sure an API key has been entered
		if ( ! $this->test_api() ) {
			return false;
		}

		$api = $this->setup_api();
		$api->add_to_tags( $user_info[ 'email' ], $tags );

		return true;
	}

    public function check_connection(): bool
    {
        return $this->test_api();
    }

    public function clear_cache(): void
    {
        delete_transient( 'edd_getresponse_get_lists' );
        delete_transient( 'edd_getresponse_get_tags' );
    }
}
