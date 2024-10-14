<?php
/**
 * Created by PhpStorm.
 * User: psypek
 * Date: 21.12.16
 * Time: 02:39
 */

namespace bpmj\wp\eddip;


use bpmj\wp\eddip\api\iPressoApi;
use bpmj\wpidea\integrations\Interface_External_Service_Integration;
use bpmj\wpidea\integrations\Trait_External_Service_Integration;
use bpmj\wpidea\helpers\Translator_Static_Helper;

class iPresso extends \EDD_Newsletter_V2  implements Interface_External_Service_Integration{
    use Trait_External_Service_Integration;

    const SERVICE_NAME = 'iPresso';

	/**
	 * @var iPressoApi
	 */
	protected $api;

	/**
	 * Sets up the checkout label
	 */
	public function init() {
		global $edd_options;
		if ( ! empty( $edd_options[ 'bpmj_eddip_label' ] ) ) {
			$this->checkout_label = trim( $edd_options[ 'bpmj_eddip_label' ] );
		} else {
			$this->checkout_label = Translator_Static_Helper::translate('newsletter.sign_up');
		}
		add_filter( 'edd_settings_sections_extensions', array( $this, 'subsection' ), 10, 1 );
	}

	/**
	 * Register our subsection for EDD 2.5
	 */
	function subsection( $sections ) {
		$sections[ 'ipresso' ] = __( 'iPresso', BPMJ_EDDIP_DOMAIN );

		return $sections;
	}

	/**
	 * Registers the plugin settings
	 */
	public function settings( $settings ) {

		global $edd_options;

		$standard_fields = array(
			array(
				'id'   => 'bpmj_eddip_settings',
				'name' => '<strong>' . __( 'iPresso Settings', BPMJ_EDDIP_DOMAIN ) . '</strong>',
				'desc' => __( 'Configure iPresso Integration Settings', BPMJ_EDDIP_DOMAIN ),
				'type' => 'header',
			),
			array(
				'id'   => 'bpmj_eddip_api_endpoint',
				'name' => __( 'iPresso panel URL', BPMJ_EDDIP_DOMAIN ),
				'desc' => __( 'Enter your iPresso panel URL (eg. yourcompany.ipresso.com)', BPMJ_EDDIP_DOMAIN ),
				'type' => 'text',
				'size' => 'regular',
			),
			array(
				'id'   => 'bpmj_eddip_api',
				'name' => __( 'iPresso API Key', BPMJ_EDDIP_DOMAIN ),
				'desc' => __( 'Enter your iPresso API key', BPMJ_EDDIP_DOMAIN ),
				'type' => 'text',
				'size' => 'regular',
			),
			array(
				'id'   => 'bpmj_eddip_api_login',
				'name' => __( 'iPresso API Login', BPMJ_EDDIP_DOMAIN ),
				'desc' => __( 'Enter your iPresso API login', BPMJ_EDDIP_DOMAIN ),
				'type' => 'text',
				'size' => 'regular',
			),
			array(
				'id'   => 'bpmj_eddip_api_password',
				'name' => __( 'iPresso API Password', BPMJ_EDDIP_DOMAIN ),
				'desc' => __( 'Enter your iPresso API password', BPMJ_EDDIP_DOMAIN ),
				'type' => 'text',
				'size' => 'regular',
			),
			array(
				'id'   => 'bpmj_eddip_tracking_code',
				'name' => __( 'Tracking code', BPMJ_EDDIP_DOMAIN ),
				'desc' => __( 'Provide iPresso tracking code for this site', BPMJ_EDDIP_DOMAIN ),
				'type' => 'textarea',
				'size' => 'regular'
			),
		);

		if ( $this->test_api() ) {

			$standard_fields[ 2 ][ 'desc' ] = __( 'Your iPresso API key is valid', BPMJ_EDDIP_DOMAIN );

			$fields = array(
				array(
					'id'   => 'bpmj_eddip_show_checkout_signup',
					'name' => __( 'Show Signup on Checkout', BPMJ_EDDIP_DOMAIN ),
					'desc' => __( 'Allow customers to choose if they want to be added to iPresso during checkout?', BPMJ_EDDIP_DOMAIN ),
					'type' => 'checkbox',
				),
				array(
					'id'   => 'bpmj_eddip_subscribe_tags',
					'name' => __( 'Tags added to the contact', BPMJ_EDDIP_DOMAIN ),
					'desc' => __( 'Specify tags (separated by commas) that will be added to contacts in iPresso. The tags will be added <strong>only</strong> if &quot;Show Signup on Checkout&quot; check is shown and checked during checkout.', BPMJ_EDDIP_DOMAIN ),
					'type' => 'ipresso_tags',
					'size' => 'regular'
				),
				array(
					'id'   => 'bpmj_eddip_unsubscribe_tags',
					'name' => __( 'Tags removed from the contact', BPMJ_EDDIP_DOMAIN ),
					'desc' => __( 'Specify tags (separated by commas) that will be removed from contacts in iPresso. The tags will be removed <strong>only</strong> if &quot;Show Signup on Checkout&quot; check is shown and checked during checkout.', BPMJ_EDDIP_DOMAIN ),
					'type' => 'ipresso_tags',
					'size' => 'regular'
				),
				array(
					'id'   => 'bpmj_eddip_label',
					'name' => __( 'Checkout Label', BPMJ_EDDIP_DOMAIN ),
					'desc' => __( 'This is the text shown next to the signup option', BPMJ_EDDIP_DOMAIN ),
					'type' => 'text',
					'size' => 'regular',
				),
			);
			if ( ! $this->allow_unsubscribe ) {
				unset( $fields[ 2 ] );
			}
		} else {

			if ( ! empty( $edd_options[ 'bpmj_eddip_api' ] ) ) {
				$api_key = trim( $edd_options[ 'bpmj_eddip_api' ] );
			}

			if ( ! empty( $api_key ) ) {
				$standard_fields[ 2 ][ 'desc' ] = __( 'Please enter <b>valid</b> iPresso API key', BPMJ_EDDIP_DOMAIN );
			}

			if ( ! empty( $edd_options[ 'bpmj_eddip_api_login' ] ) ) {
				$api_secret = trim( $edd_options[ 'bpmj_eddip_api_login' ] );
			}

			if ( ! empty( $api_secret ) ) {
				$standard_fields[ 3 ][ 'desc' ] = __( 'Please enter <b>valid</b> iPresso API login', BPMJ_EDDIP_DOMAIN );
			}

			if ( ! empty( $edd_options[ 'bpmj_eddip_api_password' ] ) ) {
				$api_secret = trim( $edd_options[ 'bpmj_eddip_api_password' ] );
			}

			if ( ! empty( $api_secret ) ) {
				$standard_fields[ 4 ][ 'desc' ] = __( 'Please enter <b>valid</b> iPresso API password', BPMJ_EDDIP_DOMAIN );
			}

			$fields = array();
		}

		$eddip_settings = array_merge( $standard_fields, $fields );


		if ( version_compare( EDD_VERSION, 2.5, '>=' ) ) {
			$eddip_settings = array( 'ipresso' => $eddip_settings );
		}

		return array_merge( $settings, $eddip_settings );
	}

	/**
	 * @return iPressoApi
	 */
	public function setup_api() {
		global $edd_options;

		if ( ! isset( $this->api ) ) {
			$this->api = new iPressoApi( $edd_options[ 'bpmj_eddip_api_endpoint' ], $edd_options[ 'bpmj_eddip_api' ], $edd_options[ 'bpmj_eddip_api_login' ], $edd_options[ 'bpmj_eddip_api_password' ] );
			$this->api->generate_api_token();
		}

		return $this->api;
	}

	/**
	 * Determines if the checkout signup option should be displayed
	 */
	public function show_checkout_signup() {
		global $edd_options;

		return !empty($edd_options['bpmj_eddip_show_checkout_signup']) && $edd_options[ 'bpmj_eddip_show_checkout_signup' ] == '1';
	}

	/**
	 * Test API call
	 */
	public function test_api() {
		global $edd_options;

		if ( ! empty( $edd_options[ 'bpmj_eddip_api' ] ) ) {
			return $this->setup_api()->ping();
		}

		return false;
	}

	/**
	 * Retrieves the tags from iPresso
	 * Not used
	 */
	public function get_lists() {
		return array();
	}

	/**
	 * Subscribe an email to a list
	 */
	public function subscribe_email( $user_info = array(), $tag_list = false, $opt_in_overridde = false ) {
		global $edd_options;

		// Make sure an API key has been entered
		if ( ! $this->test_api() ) {
			return false;
		}

		// Retrieve the global list ID if none is provided
		if ( ! $tag_list ) {
			$tag_list = ! empty( $edd_options[ 'bpmj_eddip_subscribe_tags' ] ) ? $edd_options[ 'bpmj_eddip_subscribe_tags' ] : false;
			if ( ! $tag_list ) {
				return false;
			}
		}

		$tag_list = apply_filters( 'bpmj_edd_ipresso_subscribe_email_tag_list', $tag_list, $user_info );
		$tag_list = $this->prepare_tag_list( $tag_list );

		$api = $this->setup_api();

		$api->add_contact( array(
			'email' => $user_info[ 'email' ],
			'fname' => $user_info[ 'first_name' ],
			'lname' => $user_info[ 'last_name' ],
			'tag'   => $tag_list,
			'agreement' => array('1' => '1') // na sztywno podstawowa zgoda @todo: do ustawień
		) );

		return true;
	}

	/**
	 * Unsubscribe an email from a list
	 */
	public function unsubscribe_email( $user_info = array(), $tag_list = false ) {
		global $edd_options;

		// Make sure an API key has been entered
		if ( ! $this->test_api() ) {
			return false;
		}

		// Retrieve the global list ID if none is provided
		if ( ! $tag_list && $this->allow_unsubscribe ) {
			$tag_list = ! empty( $edd_options[ 'bpmj_eddip_unsubscribe_tags' ] ) ? $edd_options[ 'bpmj_eddip_unsubscribe_tags' ] : false;
			if ( ! $tag_list ) {
				return false;
			}
		}

		$tag_list = apply_filters( 'bpmj_edd_ipresso_unsubscribe_email_tag_list', $tag_list, $user_info );
		$tag_list = $this->prepare_tag_list( $tag_list );

		$api = $this->setup_api();

		$api->remove_contact_tags( $user_info[ 'email' ], $tag_list );

		return true;
	}

	/**
	 * @param string|array $tag_list
	 *
	 * @return array
	 */
	private function prepare_tag_list( $tag_list ) {
		if ( ! is_array( $tag_list ) ) {
			$tag_list = explode( ',', $tag_list );
		}

		array_walk( $tag_list, 'trim' );

		return $tag_list;
	}

	public function render_metabox() {
		global $post;

		echo '<p>' . __( 'Specify tags (separated by commas) that will be added to contacts in iPresso on completed purchase.', BPMJ_EDDIP_DOMAIN ) . '</p>';
		$subscribe_key   = '_edd_' . esc_attr( $this->id );
		$subscribe_value = get_post_meta( $post->ID, $subscribe_key, true );
		edd_ipresso_tags_callback( array(
			'value' => $subscribe_value,
			'id'    => $subscribe_key,
			'name'  => $subscribe_key,
		) );

		if ( $this->allow_unsubscribe ) {
			echo '<p>' . __( 'Specify tags (separated by commas) that will be removed from contacts in iPresso on completed purchase.', BPMJ_EDDIP_DOMAIN ) . '</p>';
			$unsubscribe_key   = '_edd_' . esc_attr( $this->id ) . '_unsubscribe';
			$unsubscribe_value = get_post_meta( $post->ID, $unsubscribe_key, true );
			edd_ipresso_tags_callback( array(
				'value' => $unsubscribe_value,
				'id'    => $unsubscribe_key,
				'name'  => $unsubscribe_key,
			) );
		};
	}

    public function check_connection(): bool
    {
        return $this->test_api();
    }

}
