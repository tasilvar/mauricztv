<?php

use bpmj\wpidea\helpers\Translator_Static_Helper;
use bpmj\wpidea\http\Http_Client;
use bpmj\wpidea\integrations\Interface_External_Service_Integration;
use bpmj\wpidea\integrations\Trait_External_Service_Integration;

/**
 * EDD ConvertKit class, extension of the EDD base newsletter class
 * Adapted for WP Idea by upsell.pl team
 *
 * @copyright   Copyright (c) 2013, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
*/
class EDD_ConvertKit extends EDD_Newsletter_V2 implements Interface_External_Service_Integration {
use Trait_External_Service_Integration;


    const SERVICE_NAME = 'Convert Kit';
    const API_URL = 'https://api.convertkit.com/v3';

	/**
	 * ConvertKit API Key
	 *
	 * @var string
	 */
	public $api_key;

	/**
	 * ConvertKit API Secret
	 *
	 * @var string
	 */
	public $api_secret;

	/**
	 * Convert kit account tags
	 *
	 * @var object
	 */
	public $tags;

	/**
	 * Sets up the checkout label
	 */
	public function init() {

		if( ! function_exists( 'edd_get_option' ) ) {
			return;
		}

		$this->checkout_label = edd_get_option( 'edd_convertkit_label', Translator_Static_Helper::translate('newsletter.sign_up') );

		$this->api_key = edd_get_option( 'edd_convertkit_api', '' );
		$this->api_secret = edd_get_option( 'edd_convertkit_api_secret', '' );

		add_filter( 'edd_settings_sections_extensions', array( $this, 'subsection' ), 10, 1 );
		add_filter( 'edd_settings_extensions_sanitize', array( $this, 'save_settings' ) );
        add_action( 'convertkit_clear_cache', [$this, 'clear_cache'] );

	}

	/**
	 * Load the plugin's textdomain
	 */
	public function textdomain() {
		// Load the translations
		load_plugin_textdomain( 'edd-convertkit', false, EDD_CONVERTKIT_PATH . '/languages/' );
	}

	/**
	 * Retrieves the lists from ConvertKit
	 */
	public function get_lists() {

	    $this->lists[] = '';

		if( ! empty( $this->api_key ) ) {

			$lists = get_transient( 'edd_convertkit_list_data' );

			if( false === $lists ) {

				$request = wp_remote_get( self::API_URL.'/forms?api_key=' . $this->api_key );

				if( ! is_wp_error( $request ) && 200 == wp_remote_retrieve_response_code( $request ) ) {

					$lists = json_decode( wp_remote_retrieve_body( $request ) );

					set_transient( 'edd_convertkit_list_data', $lists, 24*24*24 );

				}

			}

			if( ! empty( $lists ) && ! empty( $lists->forms ) ) {

				foreach( $lists->forms as $key => $form ) {

					$this->lists[ $form->id ] = $form->name;

				}

			}

		}

		return (array) $this->lists;
	}

	/**
	 * Retrieve plugin tags
	 */
	public function get_tags() {

		if( ! empty( $this->api_key ) ) {

			$tags = get_transient( 'edd_convertkit_tag_data' );

			if( false === $tags ) {

				$request = wp_remote_get( self::API_URL.'/tags?api_key=' . $this->api_key );

				if( ! is_wp_error( $request ) && 200 == wp_remote_retrieve_response_code( $request ) ) {

					$tags = json_decode( wp_remote_retrieve_body( $request ) );

					set_transient( 'edd_convertkit_tag_data', $tags, 24*24*24 );

				}

			}

			if( ! empty( $tags ) && ! empty( $tags->tags ) ) {

				foreach( $tags->tags as $key => $tag ) {

					$this->tags[ $tag->id ] = $tag->name;

				}

			}

		}

		return (array) $this->tags;

	}

	/**
	 * Register our subsection for EDD 2.5
	 *
	 * @since  1.0.3
	 * @param  array $sections The subsections
	 * @return array           The subsections with Convertkit added
	 */
	function subsection( $sections ) {
		$sections['convertkit'] = __( 'ConvertKit', 'edd-convertkit' );
		return $sections;
	}

	/**
	 * Registers the plugin settings
	 */
	public function settings( $settings ) {
		$list_select = array( '' => '' ) + $this->get_lists();

		$edd_convertkit_settings = array(
			array(
				'id'   => 'edd_convertkit_settings',
				'name' => '<strong>' . __( 'ConvertKit Settings', 'edd-convertkit' ) . '</strong>',
				'desc' => __( 'Configure ConvertKit Integration Settings', 'edd-convertkit' ),
				'type' => 'header'
			),
			array(
				'id'            => 'edd_convertkit_api',
				'name'          => __( 'ConvertKit API Key', 'edd-convertkit' ),
				'desc'          => __( 'Enter your ConvertKit API key', 'edd-convertkit' ),
				'type'          => 'text',
				'size'          => 'regular',
				'tooltip_title' => __( 'API Key', 'edd-convertkit' ),
				'tooltip_desc'  => __( 'This can be found in your ConvertKit account under the Account menu', 'edd-convertkit' )
			),
			array(
				'id'            => 'edd_convertkit_api_secret',
				'name'          => __( 'ConvertKit API Secret', 'edd-convertkit' ),
				'desc'          => __( 'Enter your ConvertKit API secret', 'edd-convertkit' ),
				'type'          => 'text',
				'size'          => 'regular',
				'tooltip_title' => __( 'API#210 Secret', 'edd-convertkit' ),
				'tooltip_desc'  => __( 'This can be found in your ConvertKit account under the Account menu', 'edd-convertkit' )
			),
			array(
				'id'            => 'edd_convertkit_show_checkout_signup',
				'name'          => __( 'Show Signup on Checkout', 'edd-convertkit' ),
				'desc'          => __( 'Allow customers to signup for the list selected below during checkout?', 'edd-convertkit' ),
				'type'          => 'checkbox',
				'tooltip_title' => __( 'Signup on Checkout', 'edd-convertkit' ),
				'tooltip_desc'  => __( 'If enabled, a checkbox will be shown on the checkout screen allowing customers to opt-into an email subscription. If not enabled, customers will be subscribed only if one or more Forms is selected from the Edit screen of the Download product(s) being purchased.', 'edd-convertkit' )
			),
			array(
				'id'      => 'edd_convertkit_list',
				'name'    => __( 'Choose a form', 'edd-convertkit' ),
				'desc'    => __( /** @lang text */
					'Select the form you wish to subscribe buyers to. The form can also be selected on a per-product basis from the product edit screen', 'edd-convertkit' ),
				'type'    => 'select',
				'options' => $list_select
			),
			array(
				'id'   => 'edd_convertkit_label',
				'name' => __( 'Checkout Label', 'edd-convertkit' ),
				'desc' => __( 'This is the text shown next to the signup option', 'edd-convertkit' ),
				'type' => 'text',
				'size' => 'regular'
			)
		);

		if ( version_compare( EDD_VERSION, 2.5, '>=' ) ) {
			$edd_convertkit_settings = array( 'convertkit' => $edd_convertkit_settings );
		}

		return array_merge( $settings, $edd_convertkit_settings );
	}

	/**
	 * Flush the list transient on save
	 */
	public function save_settings( $input ) {
		if( isset( $input['edd_convertkit_api'] ) ) {
			$this->clear_cache();
		}
		return $input;
	}

	/**
	 * Display the metabox, which is a list of newsletter lists
	 */
	public function render_metabox() {

		global $post;

		echo '<p>' . __( 'Select the form you wish buyers to be subscribed to when purchasing.', 'edd-convertkit' ) . '</p>';

		$checked = (array) get_post_meta( $post->ID, '_edd_' . esc_attr( $this->id ), true );
		foreach( $this->get_lists() as $list_id => $list_name ) {
			echo '<label>';
				echo '<input type="checkbox" name="_edd_' . esc_attr( $this->id ) . '[]" value="' . esc_attr( $list_id ) . '"' . checked( true, in_array( $list_id, $checked ), false ) . '>';
				echo '&nbsp;' . $list_name;
			echo '</label><br/>';
		}


		$tags = $this->get_tags();
		if( ! empty( $tags ) ) {
			$checked = (array) get_post_meta( $post->ID, '_edd_' . esc_attr( $this->id ) . '_tags', true );
			echo '<p>' . __( 'Add the following tags to subscribers.', 'edd-convertkit' ) . '</p>';
			foreach ( $tags as $tag_id => $tag_name ){
				echo '<label>';
					echo '<input type="checkbox" name="_edd_' . esc_attr( $this->id ) . '_tags[]" value="' . esc_attr( $tag_id ) . '"' . checked( true, in_array( $tag_id, $checked ), false ) . '>';
					echo '&nbsp;' . $tag_name;
				echo '</label><br/>';
			}
		}

		if( ! empty( $tags ) ) {
			$checked = (array) get_post_meta( $post->ID, '_edd_' . esc_attr( $this->id ) . '_tags_unsubscribe', true );
			echo '<p>' . __( 'Remove the following tags from subscribers.', 'edd-convertkit' ) . '</p>';
			foreach ( $tags as $tag_id => $tag_name ){
				echo '<label>';
					echo '<input type="checkbox" name="_edd_' . esc_attr( $this->id ) . '_tags_unsubscribe[]" value="' . esc_attr( $tag_id ) . '"' . checked( true, in_array( $tag_id, $checked ), false ) . '>';
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

		$fields[] = '_edd_' . esc_attr( $this->id );
		$fields[] = '_edd_' . esc_attr( $this->id ) . '_tags';
		$fields[] = '_edd_' . esc_attr( $this->id ) . '_unsubscribe';
		$fields[] = '_edd_' . esc_attr( $this->id ) . '_tags_unsubscribe';

		return $fields;
	}

	/**
	 * Determines if the checkout signup option should be displayed
	 */
	public function show_checkout_signup() {
		global $edd_options;

		return ! empty( $edd_options['edd_convertkit_show_checkout_signup'] ) && $edd_options[ 'edd_convertkit_show_checkout_signup' ] == '1';
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
		$lists             = get_post_meta( $download_id, '_edd_' . $this->id, true );
		$tags              = (array) get_post_meta( $download_id, '_edd_' . $this->id . '_tags', true );
		$lists_unsubscribe = get_post_meta( $download_id, '_edd_' . $this->id . '_unsubscribe', true );
		$tags_unsubscribe  = (array) get_post_meta( $download_id, '_edd_' . $this->id . '_tags_unsubscribe', true );

		if ( 'bundle' == $download_type ) {

			// Get the lists of all items included in the bundle

			$downloads = edd_get_bundled_products( $download_id );
			if ( $downloads ) {
				foreach ( $downloads as $d_id ) {
					$d_lists             = get_post_meta( $d_id, '_edd_' . $this->id, true );
					$d_tags              = get_post_meta( $d_id, '_edd_' . $this->id . '_tags', true );
					$d_lists_unsubscribe = get_post_meta( $d_id, '_edd_' . $this->id . '_unsubscribe', true );
					$d_tags_unsubscribe  = get_post_meta( $d_id, '_edd_' . $this->id . '_tags_unsubscribe', true );
					if ( is_array( $d_lists ) ) {
						$lists = array_merge( $d_lists, (array) $lists );
					}
					if ( is_array( $d_tags ) ) {
						$tags = array_merge( $d_tags, (array) $tags );
					}
					if ( is_array( $d_lists_unsubscribe ) ) {
						$lists_unsubscribe = array_merge( $d_lists_unsubscribe, (array) $lists_unsubscribe );
					}
					if ( is_array( $d_tags_unsubscribe ) ) {
						$tags_unsubscribe = array_merge( $d_tags_unsubscribe, (array) $tags_unsubscribe );
					}
				}
			}
		}

		if ( empty( $lists ) ) {
			$this->subscribe_email( $user_info, false, false, $tags );
		}

		$lists = is_array( $lists ) ? array_unique( $lists ) : array();
		$tags  = is_array( $tags ) ? array_unique( $tags ) : array();

		foreach ( $lists as $list ) {

			$this->subscribe_email( $user_info, $list, false, $tags );

		}

		$lists_unsubscribe = is_array( $lists_unsubscribe ) ? array_unique( $lists_unsubscribe ) : array();
		$tags_unsubscribe  = is_array( $tags_unsubscribe ) ? array_unique( $tags_unsubscribe ) : array();

		if ( ! empty( $lists_unsubscribe ) || ! empty( $tags_unsubscribe ) ) {
			$this->unsubscribe( $user_info, $lists_unsubscribe, $tags_unsubscribe );
		}

	}

	/**
	 * Subscribe an email to a list
	 *
	 * @param array $user_info User info as returned by edd_get_payment_meta_user_info()
	 * @param bool|string $list_id Optional. ID of list to subscribe to. If false, the default, value saved in UI will be used.
	 * @param bool $opt_in_overridde Not used
	 * @param array $tags Optional. Optional. Array of tags to add to subscriber
	 *
	 * @return bool
	 */
	public function subscribe_email( $user_info = array(), $list_id = false, $opt_in_overridde = false, $tags = array() ) {

		// Make sure an API key has been entered
		if( empty( $this->api_key ) ) {
			return false;
		}

		// Retrieve the global list ID if none is provided
		if( ! $list_id && empty( $tags ) ) {
			$list_id = edd_get_option( 'edd_convertkit_list', false );
			if( ! $list_id ) {
				return false;
			}
		}

		/**
		 * Filter subscriber info sent to ConvertKit
		 *
		 * @since 1.0.4
		 * @return array
		 * @var $user_info Array including all available user info
		 * @var $list_id ID of the ConvertKit list
		 * @var $tags Array of tags
		 */
		$args = apply_filters( 'edd_convertkit_subscribe_vars', array(
			'email'      => $user_info['email'],
			'first_name' => $user_info['first_name'],
			'fields'     => array(
				'last_name'  => $user_info['last_name']
			),
		), $user_info, $list_id, $tags );

		$return = false;

		$request = wp_remote_post(
            self::API_URL.'/forms/' . $list_id . '/subscribe?api_key=' . $this->api_key,
			array(
				'body'    => $args,
				'timeout' => 30,
			)
		);

		if( ! is_wp_error( $request ) && 200 == wp_remote_retrieve_response_code( $request ) ) {
			$return = true;
		}

		if( ! empty( $tags ) ) {

			foreach( $tags as $tag ) {

				$request = wp_remote_post(
                    self::API_URL.'/tags/' . $tag . '/subscribe?api_key=' . $this->api_key,
					array(
						'body'    => $args,
						'timeout' => 15,
					)
				);

				if( ! is_wp_error( $request ) && 200 == wp_remote_retrieve_response_code( $request ) ) {
					$return = true;
				}

			}

		}

		return $return;

	}

	/**
	 * Unsubscribe the user from lists and tags
	 *
	 * @param array $user_info
	 * @param array $lists_unsubscribe
	 * @param array $tags_unsubscribe
	 *
	 * @return bool
	 */
	protected function unsubscribe( $user_info, $lists_unsubscribe, $tags_unsubscribe ) {
		if ( ! $this->api_secret ) {
			return false;
		}
		$email   = $user_info[ 'email' ];
		$request = wp_remote_get( self::API_URL."/subscribers?api_secret={$this->api_secret}&email_address=" . urlencode( $email ), array(
			'timeout' => 15,
		) );

		if ( is_wp_error( $request ) || 200 !== wp_remote_retrieve_response_code( $request ) ) {
			return false;
		}

		$response_data = json_decode( wp_remote_retrieve_body( $request ), true );

		if ( empty( $response_data ) || empty( $response_data[ 'subscribers' ] ) ) {
			return false;
		}

		$subscriber_id = $response_data[ 'subscribers' ][ 0 ][ 'id' ];

		if ( is_array( $lists_unsubscribe ) ) {
			foreach ( $lists_unsubscribe as $list_id ) {
				// Currently not possible in ConvertKit
				null;
			}
		}

		if ( is_array( $tags_unsubscribe ) ) {
			foreach ( $tags_unsubscribe as $tag_id ) {
				$url = self::API_URL."/subscribers/{$subscriber_id}/tags/{$tag_id}?api_secret={$this->api_secret}";
				wp_remote_request( $url, array(
					'method'  => 'DELETE',
					'timeout' => 15,
				) );
			}
		}

		return true;
	}

    public function check_connection(): bool
    {
        $client = new Http_Client();

        $response_api_secret = $client->create_request()
            ->set_url(self::API_URL.'/account?api_secret=' . $this->api_secret)
            ->send();

        if($response_api_secret->is_error()){
            return false;
        }
        $body_api_secret = $response_api_secret->get_decoded_body();
        $connection_result_api_secret = isset($body_api_secret->primary_email_address);

        $response_api_key = $client->create_request()
            ->set_url(self::API_URL.'/forms?api_key=' . $this->api_key)
            ->send();

        if($response_api_key->is_error()){
            return false;
        }
        $body_api_key = $response_api_key->get_decoded_body();
        $connection_result_api_key = isset($body_api_key->forms);

        return $connection_result_api_secret && $connection_result_api_key;
    }

    public function clear_cache(): void
    {
        delete_transient( 'edd_convertkit_list_data' );
        delete_transient( 'edd_convertkit_tag_data' );
    }

}
