<?php
/**
 * Created by PhpStorm.
 * User: psypek
 * Date: 21.12.16
 * Time: 02:36
 */

namespace bpmj\wp\eddfm\api;

/**
 * FreshMail API implementation
 * Documentation: https://freshmail.pl/developer-api/autoryzacja/
 * Documentation version: 1.0.21
 *
 * @package bpmj\wp\eddfm
 */
class FreshmailApi {
	/**
	 * The API Key (retrieved from FreshMail panel)
	 * @var string
	 */
	protected $api_key;

	/**
	 * The Secret Key (retrieved from FreshMail panel)
	 * @var string
	 */
	protected $secret_key;

	/**
	 * URL to FreshMail API endpoint
	 * @var string
	 */
	protected $api_endpoint;

	/**
	 * Whether it's possible to use cURL functions (cURL is enabled in PHP)
	 * @var bool
	 */
	protected $use_curl;

	/**
	 * Content type for data sent to the API endpoint
	 * @var string
	 */
	protected $content_type = 'application/json';

	/**
	 * Last call's response object
	 * @var Response
	 */
	protected $last_response;

	const URI_PREFIX = 'rest/';

	const SUBSCRIBER_STATE_ACTIVE = 1;
	const SUBSCRIBER_STATE_PENDING = 2;
	const SUBSCRIBER_STATE_INACTIVE = 3;
	const SUBSCRIBER_STATE_UNSUBSCRIBED = 4;
	const SUBSCRIBER_STATE_BOUNCING_SOFT = 5;
	const SUBSCRIBER_STATE_BOUNCING_HARD = 8;

	/**
	 * FreshmailApi setup
	 *
	 * @param string $api_key
	 * @param string $secret_key
	 * @param string $api_endpoint
	 */
	public function __construct( $api_key, $secret_key, $api_endpoint = 'https://api.freshmail.com/' ) {
		$this->set_api_key( $api_key );
		$this->set_secret_key( $secret_key );
		$this->set_api_endpoint( $api_endpoint );
		$this->detect_curl();
	}

	/**
	 * Shortcut function to FreshmailApi::action() with HTTP method GET
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
	 * Shortcut function to FreshmailApi::action() with HTTP method POST
	 *
	 * @param string $action
	 * @param array $params
	 *
	 * @return Response
	 */
	protected function post( $action, array $params = array() ) {
		return $this->action( $action, 'POST', $params );
	}

	/**
	 * Call the action
	 *
	 * @param string $action
	 * @param string $method
	 * @param array $params
	 *
	 * @return Response
	 */
	protected function action( $action, $method = 'GET', array $params = array() ) {
		$url              = "{$this->api_endpoint}/" . static::URI_PREFIX . "{$action}";
		$method_uppercase = strtoupper( $method );
		if ( !empty( $params ) && 'GET' === $method_uppercase ) {
			$url .= '?' . http_build_query( $params );
		}
		$post_data = '';
		if ( 'GET' !== $method ) {
			if ( 'application/json' === $this->content_type ) {
				$post_data = json_encode( $params );
			} else if ( !empty( $params ) ) {
				$post_data = http_build_query( $params );
			}
		}
		$signature = sha1( $this->api_key . '/' . static::URI_PREFIX . $action . $post_data . $this->secret_key );
		$headers   = array(
			'X-Rest-ApiKey: ' . $this->api_key,
			'X-Rest-ApiSign: ' . $signature,
		);
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
        curl_setopt( $curl, CURLOPT_TIMEOUT, 5);

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
	 * @param string $input
	 *
	 * @return Response
	 */
	public function ping( $input = '' ) {
		if ( !$input ) {
			return $this->get( 'ping' );
		} else {
			return $this->post( 'ping', array( 'data' => $input ) );
		}
	}

	/**
	 * Get list of lists
	 *
	 * @return Response
	 */
	public function get_lists() {
		return $this->get( 'subscribers_list/lists' );
	}

	/**
	 * Add new custom field to a list
	 *
	 * @param string $list_hash
	 * @param string $field_name
	 *
	 * @return Response
	 */
	public function add_list_custom_field( $list_hash, $field_name ) {
		return $this->post( 'subscribers_list/addField', array(
			'hash' => $list_hash,
			'name' => $field_name,
		) );
	}

	/**
	 * Add new subscriber to a list
	 *
	 * @param string $email
	 * @param string $list_hash
	 * @param int $state
	 * @param int $confirm
	 * @param array $custom_fields
	 *
	 * @return Response
	 */
	public function add_subscriber( $email, $list_hash, $state = self::SUBSCRIBER_STATE_PENDING, $confirm = 1, $custom_fields = array() ) {
		return $this->post( 'subscriber/add', array(
			'email'         => $email,
			'list'          => $list_hash,
			'state'         => $state,
			'confirm'       => $confirm,
			'custom_fields' => $custom_fields,
		) );
	}

	/**
	 * Remove the subscriber from the list
	 *
	 * @param string $email
	 * @param string $list_hash
	 * @param int $state
	 * @param int $confirm
	 * @param array $custom_fields
	 *
	 * @return Response
	 */
	public function remove_subscriber( $email, $list_hash ) {
		return $this->post( 'subscriber/delete', array(
			'email' => $email,
			'list'  => $list_hash,
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
	 * @return FreshmailApi
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
	 * @param string $api_key
	 *
	 * @return FreshmailApi
	 */
	public function set_api_key( $api_key ) {
		$this->api_key = $api_key;

		return $this;
	}

	/**
	 * Secret Key setter
	 *
	 * @param string $secret_key
	 *
	 * @return FreshmailApi
	 */
	public function set_secret_key( $secret_key ) {
		$this->secret_key = $secret_key;

		return $this;
	}

	/**
	 * API Endpoint setter
	 *
	 * @param string $api_endpoint
	 *
	 * @return FreshmailApi
	 */
	public function set_api_endpoint( $api_endpoint ) {
		$this->api_endpoint = rtrim( $api_endpoint, '/' );

		return $this;
	}
}