<?php
/**
 * Created by PhpStorm.
 * User: psypek
 * Date: 21.12.16
 * Time: 02:36
 */

namespace bpmj\wp\eddact\api;

/**
 * ActiveCampaign API implementation
 * Documentation: https://activecampaign.pl/developer-api/autoryzacja/
 * Documentation version: 1.0.21
 *
 * @package bpmj\wp\eddact
 */
class ActiveCampaignApi {
	const SUBSCRIBER_STATE_UNCONFIRMED = 0;
	const SUBSCRIBER_STATE_ACTIVE = 1;
	const SUBSCRIBER_STATE_UNSUBSCRIBED = 2;
	/**
	 * The API Key (retrieved from ActiveCampaign panel)
	 * @var string
	 */
	protected $api_url;

	/**
	 * The Secret Key (retrieved from ActiveCampaign panel)
	 * @var string
	 */
	protected $api_token;

	/**
	 * Whether it's possible to use cURL functions (cURL is enabled in PHP)
	 * @var bool
	 */
	protected $use_curl;

	/**
	 * Content type for data sent to the API endpoint
	 * @var string
	 */
	protected $content_type = 'application/x-www-form-urlencoded';

	/**
	 * Last call's response object
	 * @var Response
	 */
	protected $last_response;

	/**
	 * ActiveCampaignApi setup
	 *
	 * @param string $api_url
	 * @param string $api_token
	 */
	public function __construct( $api_url, $api_token ) {
		$this->set_api_token( $api_token );
		$api_url = preg_replace( '/\/+$/', '', $api_url );
		if ( ! preg_match( "#^https://www.activecampaign.com#", $api_url ) ) {
			// not a reseller
			$api_url .= "/admin";
		}
		$this->set_api_url( "{$api_url}/api.php?api_key={$api_token}&api_output=json" );
		$this->detect_curl();
	}

	/**
	 * Shortcut function to ActiveCampaignApi::action() with HTTP method GET
	 *
	 * @param string $action
	 * @param array $params
	 *
	 * @return Response
	 */
	protected function get( $action, array $params = array() ) {
		return $this->action( $action, 'GET', $params );
	}

	/**
	 * Shortcut function to ActiveCampaignApi::action() with HTTP method POST
	 *
	 * @param string $action
	 * @param array $params
	 * @param array $additional_get_params
	 *
	 * @return Response
	 */
	protected function post( $action, array $params = array(), array $additional_get_params = array() ) {
		return $this->action( $action, 'POST', $params, $additional_get_params );
	}

	/**
	 * Call the action
	 *
	 * @param string $action
	 * @param string $method
	 * @param array $params
	 * @param array $additional_get_params
	 *
	 * @return Response
	 */
	protected function action( $action, $method = 'GET', array $params = array(), array $additional_get_params = array() ) {
		$url              = "{$this->api_url}&api_action=$action";
		$method_uppercase = strtoupper( $method );
		if ( !empty( $params ) && 'GET' === $method_uppercase ) {
			$url .= '&' . http_build_query( $params );
		}
		if ( ! empty( $additional_get_params ) ) {
			$url .= '&' . http_build_query( $additional_get_params );
		}
		$post_data = '';
		if ( 'GET' !== $method ) {
			if ( 'application/json' === $this->content_type ) {
				$post_data = json_encode( $params );
			} else if ( !empty( $params ) ) {
				$post_data = http_build_query( $params );
			}
		}
		$headers = array();
		if ( $this->content_type ) {
			$headers[] = 'Content-Type: ' . $this->content_type;
		}
		if ( $this->use_curl ) {
			$json = $this->action_curl( $url, $method_uppercase, $post_data, $headers );
		} else {
			$json = $this->action_file_get_contents( $url, $method_uppercase, $post_data, $headers );
		}

		$response = new Response( $json );

		$this->last_response = $response;

		return $response;
	}

	/**
	 * Call the action using cURL
	 *
	 * @param string $url
	 * @param string $method
	 * @param string $post_data
	 * @param array $headers
	 *
	 * @return string JSON
	 */
	protected function action_curl( $url, $method, $post_data, $headers ) {
		$curl = curl_init( $url );

		if ( 'GET' !== $method ) {
			if ( $post_data ) {
				curl_setopt( $curl, CURLOPT_POST, true );
				curl_setopt( $curl, CURLOPT_POSTFIELDS, $post_data );
			}
			curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, $method );
		}
		curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $curl, CURLOPT_HEADER, false );
		curl_setopt( $curl, CURLOPT_URL, $url );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $curl, CURLOPT_TIMEOUT, 15);

		return curl_exec( $curl );
	}

	/**
	 * Call the action using file_get_contents
	 *
	 * @param string $url
	 * @param string $method
	 * @param string $post_data
	 * @param array $headers
	 *
	 * @return string JSON
	 */
	protected function action_file_get_contents( $url, $method, $post_data, $headers ) {
		$context_options_http = array(
			'method' => $method,
			'header' => $headers,
		);
		if ( 'GET' !== $method && $post_data ) {
			$context_options_http['content'] = $post_data;
		}
		$context = stream_context_create( array(
			'http' => $context_options_http,
		) );

		return file_get_contents( $url, false, $context );
	}

	/**
	 * Ping
	 *
	 * @return bool
	 */
	public function ping() {
		$response = $this->get( 'user_me' );

		if ( $response->is_success() && $response->get_raw_data( 'apikey' ) === $this->api_token ) {
			return true;
		}

		return false;
	}

	/**
	 * Get list of lists
	 *
	 * @return Response
	 */
	public function get_lists() {
		return $this->get( 'list_list', array( 'ids' => 'all' ) );
	}

	/**
	 * Get list of tags
	 *
	 * @return Response
	 */
	public function get_tags() {
		return $this->get( 'tags_list' );
	}

	/**
	 * Get list of forms
	 *
	 * @return Response
	 */
	public function get_forms() {
		return $this->get( 'form_getforms' );
	}

	/**
	 * @param string $email
	 *
	 * @return Response
	 */
	public function find_subscriber( $email ) {
		return $this->get( 'contact_view_email', array( 'email' => $email ) );
	}

	/**
	 * @param string $email
	 * @param string $tag
	 *
	 * @return Response
	 */
	public function remove_subscriber_tag( $email, $tag ) {
		return $this->post( 'contact_tag_remove', array(
			'email' => $email,
			'tags'  => $tag,
		) );
	}

	/**
	 * Last call's response
	 *
	 * @return Response
	 */
	public function get_last_response() {
		return $this->last_response;
	}

	/**
	 * Detect whether the system has cURL enabled
	 *
	 * @return ActiveCampaignApi
	 */
	public function detect_curl() {
		if ( function_exists( 'curl_exec' ) ) {
			$this->use_curl = true;
		} else {
			$this->use_curl = false;
		}

		return $this;
	}

	/**
	 * API Key setter
	 *
	 * @param string $api_url
	 *
	 * @return ActiveCampaignApi
	 */
	public function set_api_url( $api_url ) {
		$this->api_url = $api_url;

		return $this;
	}

	/**
	 * Secret Key setter
	 *
	 * @param string $api_token
	 *
	 * @return ActiveCampaignApi
	 */
	public function set_api_token( $api_token ) {
		$this->api_token = $api_token;

		return $this;
	}

	/**
	 * @param string $email
	 * @param string $first_name
	 * @param string $last_name
	 * @param array $lists
	 * @param array $lists_unsubscribe
	 * @param int $state
	 * @param string $form_id
	 *
	 * @return Response
	 */
	public function add_subscriber( $email, $first_name, $last_name, array $lists, array $lists_unsubscribe, $state, $form_id = '' ) {
		$subscriber_response = $this->find_subscriber( $email );
		$subscriber_id       = $subscriber_response->get_raw_data( 'id' );
		if ( $subscriber_id ) {
			$p      = array_combine( $lists, $lists );
			$status = array_fill_keys( $lists, $state );
			foreach ( $lists_unsubscribe as $unsubscribe_list_id ) {
				$p[ $unsubscribe_list_id ]      = $unsubscribe_list_id;
				$status[ $unsubscribe_list_id ] = static::SUBSCRIBER_STATE_UNSUBSCRIBED;
			}
			$data = array(
				'id'         => $subscriber_id,
				'email'      => $email,
				'first_name' => $first_name,
				'last_name'  => $last_name,
				'p'          => $p,
				'status'     => $status,
			);

			return $this->post( 'contact_edit', $data, array( 'overwrite' => 0 ) );
		} else {

			$data = array(
				'email'      => $email,
				'first_name' => $first_name,
				'last_name'  => $last_name,
				'p'          => array_combine( $lists, $lists ),
				'status'     => array_fill_keys( $lists, $state ),
			);
			if ( $form_id ) {
				$data[ 'form' ] = $form_id;
			}

			return $this->post( 'contact_add', $data );
		}
	}

	/**
	 * @param string $email
	 * @param string $tag
	 *
	 * @return Response
	 */
	public function add_subscriber_tag( $email, $tag ) {
		return $this->post( 'contact_tag_add', array(
			'email' => $email,
			'tags'  => $tag,
		) );
	}
}