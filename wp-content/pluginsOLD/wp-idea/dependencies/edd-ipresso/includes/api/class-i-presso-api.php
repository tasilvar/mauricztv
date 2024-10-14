<?php

namespace bpmj\wp\eddip\api;

/**
 * iPresso API implementation
 * Documentation: http://apidoc.ipresso.pl/v2
 *
 * @package bpmj\wp\eddip
 */
class iPressoApi {
	/**
	 * The API key (retrieved from iPresso panel)
	 * @var string
	 */
	protected $api_key;

	/**
	 * The API login (retrieved from iPresso panel)
	 * @var string
	 */
	protected $api_login;

	/**
	 * The API password (retrieved from iPresso panel)
	 * @var string
	 */
	protected $api_password;

	/**
	 * URL to iPresso API endpoint
	 * @var string
	 */
	protected $api_endpoint;

	/**
	 * URL to iPresso API token
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
	 * If set then this handler will be called on each API call (to/from)
	 * @var callable|null
	 */
	protected $debug_handler = null;

	/**
	 * The sequence number of API calls
	 * @var int
	 */
	protected $debug_api_call_index = 0;

	const URI_PREFIX = 'rest/';

	/**
	 * iPressoApi setup
	 *
	 * @param string $api_endpoint
	 * @param string $api_key
	 * @param string $api_login
	 * @param string $api_password
	 * @param string $api_token
	 */
	public function __construct( $api_endpoint, $api_key, $api_login, $api_password, $api_token = '' ) {
		$this->set_api_key( $api_key );
		$this->set_api_login( $api_login );
		$this->set_api_password( $api_password );
		$this->set_api_endpoint( $api_endpoint );
		$this->set_api_token( $api_token );
		$this->detect_curl();
	}

	/**
	 * @param callable|null $debug_handler
	 *
	 * @return $this
	 */
	public function set_debug_handler( $debug_handler = null ) {
		$this->debug_handler = $debug_handler && is_callable( $debug_handler ) ? $debug_handler : null;

		return $this;
	}

	/**
	 * Shortcut function to iPressoApi::action() with HTTP method GET
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
	 * Shortcut function to iPressoApi::action() with HTTP method POST
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
	 * Shortcut function to iPressoApi::action() with HTTP method PUT
	 *
	 * @param string $action
	 * @param array $params
	 *
	 * @return Response
	 */
	protected function put( $action, array $params = array() ) {
		return $this->action( $action, 'PUT', $params );
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
		$url              = "{$this->api_endpoint}/{$action}";
		$method_uppercase = strtoupper( $method );
		if ( ! empty( $params ) && 'GET' === $method_uppercase ) {
			$url .= '?' . http_build_query( $params );
		}
		$post_data = '';
		if ( 'GET' !== $method ) {
			if ( 'application/json' === $this->content_type ) {
				$post_data = json_encode( $params );
			} else if ( ! empty( $params ) ) {
				$post_data = http_build_query( $params );
			}
		}
		$headers = array(
			'IPRESSO_TOKEN: ' . $this->api_token,
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
	 * @param string $login
	 * @param string $password
	 *
	 * @return string JSON
	 */
	protected function action_curl( $url, $method, $post_data = '', $headers = array(), $login = '', $password = '' ) {
		$curl = curl_init( $url );

		if ( 'GET' !== $method ) {
			if ( $post_data ) {
				curl_setopt( $curl, CURLOPT_POST, true );
				curl_setopt( $curl, CURLOPT_POSTFIELDS, $post_data );
			}
			curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, $method );
		}
		if ( ! empty( $headers ) ) {
			curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers );
		}
		curl_setopt( $curl, CURLOPT_HEADER, false );
		curl_setopt( $curl, CURLOPT_URL, $url );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );

		curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, false );
		curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, false );

		if ( $login && $password ) {
			curl_setopt( $curl, CURLOPT_USERPWD, $login . ':' . $password );
		}

		if ( $this->debug_handler ) {
			call_user_func( $this->debug_handler, ++ $this->debug_api_call_index, "$method $url [request]", array(
				'headers'   => $headers,
				'post_data' => $post_data
			) );
		}

		$result = curl_exec( $curl );

		if ( $this->debug_handler ) {
			call_user_func( $this->debug_handler, ++ $this->debug_api_call_index, "$method $url [response]", $result );
		}

		return $result;
	}

	/**
	 * Call the action using file_get_contents
	 *
	 * @param string $url
	 * @param string $method
	 * @param string $post_data
	 * @param array $headers
	 * @param string $login
	 * @param string $password
	 *
	 * @return string JSON
	 */
	protected function action_file_get_contents( $url, $method, $post_data = '', $headers = array(), $login = '', $password = '' ) {
		if ( $login && $password ) {
			$headers[] = 'Authentication: Basic ' . base64_encode( "$login:$password" );
		}
		$context_options_http = array(
			'method' => $method,
			'header' => $headers,
		);
		if ( 'GET' !== $method && $post_data ) {
			$context_options_http[ 'content' ] = $post_data;
		}
		$context = stream_context_create( array(
			'http' => $context_options_http,
		) );

		if ( $this->debug_handler ) {
			call_user_func( $this->debug_handler, ++ $this->debug_api_call_index, "$method $url [request]", array(
				'headers'              => $headers,
				'post_data'            => $post_data,
				'context_options_http' => $context_options_http,
			) );
		}

		$result = file_get_contents( $url, false, $context );

		if ( $this->debug_handler ) {
			call_user_func( $this->debug_handler, ++ $this->debug_api_call_index, "$method $url [response]", $result );
		}

		return $result;
	}

	/**
	 * Ping
	 *
	 * @return bool
	 */
	public function ping() {
		return ! ! $this->api_token;
	}

	/**
	 * Add new contact to iPresso
	 *
	 * @param array $user_data
	 *
	 * @return Response
	 */
	public function add_contact( array $user_data ) {
		$response = $this->post( 'contact', array( 'contact' => array( $user_data ) ) );
		if ( $response->is_success() ) {
			$added_contacts = $response->get_data( 'contact' );
			if ( ! empty( $added_contacts ) && ! empty( $added_contacts[ 1 ] ) ) {
				$added_contact = $added_contacts[ 1 ];
				if ( 303 === $added_contact[ 'code' ] ) {
					// contact with this e-mail already exists
					if ( ! empty( $user_data[ 'tag' ] ) && is_array( $user_data[ 'tag' ] ) ) {
						$this->add_contact_tags( $added_contact[ 'id' ], $user_data[ 'tag' ] );
					}
					$this->update_contact( $added_contact[ 'id' ], $user_data );
				}
			}
		}

		return $response;
	}

	/**
	 * @param int $contact_id
	 * @param array $user_data
	 *
	 * @return Response
	 */
	public function update_contact( $contact_id, array $user_data ) {
		$old_contact_response = $this->get( "contact/$contact_id" );
		if ( $old_contact_response->is_success() ) {
			$old_contact             = $old_contact_response->get_data( 'contact' );
			$update_contact_response = $this->put( "contact/$contact_id", array( 'contact' => $user_data ) );

			return $update_contact_response;
		}

		return $old_contact_response;
	}

	/**
	 * Add new tags to the contact
	 *
	 * @param int $contact_id
	 * @param array $tags
	 *
	 * @return Response|null
	 */
	public function add_contact_tags( $contact_id, array $tags ) {
		$existing_tags = array_values( $this->get_contact_tags( $contact_id ) );
		$new_tags      = array_diff( $tags, $existing_tags );
		if ( ! empty( $new_tags ) ) {
			return $this->post( "contact/$contact_id/tag", array( 'tag' => $new_tags ) );
		}

		return null;
	}

	/**
	 * Remove tags from the contact
	 *
	 * @param string $email
	 * @param array $tags
	 *
	 * @return Response|null
	 */
	public function remove_contact_tags( $email, array $tags ) {
		$contact_ids = $this->search_contacts_by_email( $email );
		if ( empty( $tags ) ) {
			return null;
		}
		$update_contact_response = null;
		foreach ( $contact_ids as $contact_id ) {
			foreach ( $this->get_contact_tags( $contact_id ) as $tag_id => $tag ) {
				if ( in_array( $tag, $tags ) ) {
					$this->remove_contact_tag( $contact_id, $tag_id );
				}
			}
		}

		return $update_contact_response;
	}

	/**
	 * @param $contact_id
	 *
	 * @return array
	 */
	public function get_contact_tags( $contact_id ) {
		$tags_response = $this->get( "contact/$contact_id/tag" );
		if ( $tags_response->is_success() ) {
			$tags = $tags_response->get_data( 'tag' );
			if ( is_array( $tags ) ) {
				return $tags;
			}
		}

		return array();
	}

	/**
	 * @param $contact_id
	 * @param $tag_id
	 *
	 * @return Response
	 */
	public function remove_contact_tag( $contact_id, $tag_id ) {
		$tag_remove_response = $this->action( "contact/$contact_id/tag/$tag_id", 'DELETE' );

		return $tag_remove_response;
	}

	/**
	 * @param $email
	 *
	 * @return array
	 */
	public function search_contacts_by_email( $email ) {
		$contact_search_response = $this->post( 'contact/search', array(
			'contact' => array(
				'email' => $email,
			),
		) );
		if ( $contact_search_response->is_success() ) {
			$contact_search_results = $contact_search_response->get_data( 'contact' );
			if ( ! empty( $contact_search_results ) && is_array( $contact_search_results ) ) {
				return $contact_search_results;
			}
		}

		return array();
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
	 * @return iPressoApi
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
	 * @return iPressoApi
	 */
	public function set_api_key( $api_key ) {
		$this->api_key = $api_key;

		return $this;
	}

	/**
	 * API login setter
	 *
	 * @param string $api_login
	 *
	 * @return iPressoApi
	 */
	public function set_api_login( $api_login ) {
		$this->api_login = $api_login;

		return $this;
	}

	/**
	 * API password setter
	 *
	 * @param string $api_password
	 *
	 * @return iPressoApi
	 */
	public function set_api_password( $api_password ) {
		$this->api_password = $api_password;

		return $this;
	}

	/**
	 * API Endpoint setter
	 *
	 * @param string $api_endpoint
	 *
	 * @return iPressoApi
	 */
	public function set_api_endpoint( $api_endpoint ) {
		$api_endpoint       = 'https://' . preg_replace( '/^https?\:\/\//', '', trim( $api_endpoint ) );
		$this->api_endpoint = rtrim( $api_endpoint, '/' ) . '/api/2';

		return $this;
	}

	/**
	 * API token setter
	 *
	 * @param string $api_token
	 *
	 * @return iPressoApi
	 */
	public function set_api_token( $api_token ) {
		$this->api_token = $api_token;

		return $this;
	}

	/**
	 * @return string
	 */
	public function generate_api_token() {
		$url = "{$this->api_endpoint}/auth/{$this->api_key}";

		if ( $this->use_curl ) {
			$json = $this->action_curl( $url, 'GET', '', array(), $this->api_login, $this->api_password );
		} else {
			$json = $this->action_file_get_contents( $url, 'GET', '', array(), $this->api_login, $this->api_password );
		}

		$response            = new Response( $json );
		$this->last_response = $response;
		if ( $response->is_success() ) {
			$this->api_token = $response->get_data();
		}

		return $this->api_token;
	}

	/**
	 * @param $contact_id
	 *
	 * @return Response
	 */
	public function remove_contact( $contact_id ) {
		return $this->action( "contact/$contact_id", 'DELETE' );
	}

	/**
	 * @param $contact_id
	 *
	 * @return array|null
	 */
	public function get_contact( $contact_id ) {
		$old_contact_response = $this->get( "contact/$contact_id" );
		if ( $old_contact_response->is_success() ) {
			return $old_contact_response->get_data( 'contact' );
		}

		return null;
	}

}