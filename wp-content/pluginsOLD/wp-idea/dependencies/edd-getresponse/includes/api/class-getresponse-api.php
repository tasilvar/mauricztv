<?php

namespace bpmj\wp\eddres\api;

/**
 * GetResponse API implementation
 * Documentation: http://apidocs.getresponse.com/v3
 *
 * @package bpmj\wp\eddres
 */
class GetresponseApi {
	/**
	 * The API Key (retrieved from GetResponse panel)
	 * @var string
	 */
	protected $api_key;

	/**
	 * URL to GetResponse API endpoint
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
	 * GetresponseApi setup
	 *
	 * @param string $api_key
	 * @param string $api_endpoint
	 */
	public function __construct( $api_key, $api_endpoint = 'https://api.getresponse.com/v3' ) {
		$this->set_api_key( $api_key );
		$this->set_api_endpoint( $api_endpoint );
		$this->detect_curl();
	}

	/**
	 * Shortcut function to GetresponseApi::action() with HTTP method GET
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
	 * Shortcut function to GetresponseApi::action() with HTTP method POST
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
	 * @param string $action
	 * @param array $params
	 *
	 * @return Response
	 */
	protected function delete( $action, array $params = array() ) {
		return $this->action( $action, 'DELETE', $params );
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
			'X-Auth-Token: api-key ' . $this->api_key,
		);
		if ( $this->content_type ) {
			$headers[] = 'Content-Type: ' . $this->content_type;
		}
		if ( $this->use_curl ) {
			return $this->action_curl( $url, $method_uppercase, $post_data, $headers );
		} else {
			return $this->action_file_get_contents( $url, $method_uppercase, $post_data, $headers );
		}
	}

	/**
	 * Call the action using cURL
	 *
	 * @param string $url
	 * @param string $method
	 * @param string $post_data
	 * @param array $headers
	 *
	 * @return Response
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

		$body = curl_exec( $curl );

		return new Response( $body, curl_getinfo( $curl, CURLINFO_HTTP_CODE ) );
	}

	/**
	 * Call the action using file_get_contents
	 *
	 * @param string $url
	 * @param string $method
	 * @param string $post_data
	 * @param array $headers
	 *
	 * @return Response
	 */
	protected function action_file_get_contents( $url, $method, $post_data, $headers ) {
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

		$body            = file_get_contents( $url, false, $context );
		$http_code       = 0;
		$response_header = $http_response_header[ 0 ];
		if ( preg_match( "#HTTP/[0-9\.]+\s+([0-9]+)#", $response_header, $out ) ) {
			$http_code = (int) ( $out[ 1 ] );
		}

		return new Response( $body, $http_code );
	}

	/**
	 * Get list of lists
	 *
	 * @return Response
	 */
	public function get_lists() {
		return $this->get( 'campaigns' );
	}

	/**
	 * Get list of tags
	 *
	 * @return Response
	 */
	public function get_tags() {
		return $this->get( 'tags' );
	}

	/**
	 * @return bool
	 */
	public function ping() {
		$response = $this->get( 'campaigns', array( 'perPage' => 1 ) );
		if ( $response->is_success() ) {
			return true;
		}

		return false;
	}

	/**
	 * Add new subscriber to a list
	 *
	 * @param string $name
	 * @param string $email
	 * @param string $list_hash
	 * @param array $tags
	 *
	 * @return Response
	 */
	public function add_subscriber( $name, $email, $list_hash, $tags = array() ) {
		if ( '' === trim( $name ) ) {
			$name = $email;
		}

		$data = array(
			'name'     => $name,
			'email'    => $email,
			'campaign' => array(
				'campaignId' => $list_hash,
			),
			'dayOfCycle'	 => 0
		);
		if ( ! empty( $tags ) ) {
			$data[ 'tags' ] = array_map( function ( $tag ) {
				return array( 'tagId' => $tag );
			}, $tags );
		}

		$response = $this->post( 'contacts', $data );
		
		// Code 409 means that contact already exists, so we need to find ID of this contact and edit contact data
		if($response->get_code() == 409){

			// Find contact by email
			$contacts = $this->get( 'contacts', array(
				'query' => array(
					'email'      => $email,
					'campaignId' => $list_hash
				)
			) );
			
			// Change contact data
			if ( $contacts->is_success() && 1 === count( $contacts->get_data() ) ) {
				$contacts_data = $contacts->get_data();
				$contact_id   = $contacts_data[0][ 'contactId' ];
				
				if(!empty($contacts_data[0][ 'dayOfCycle' ])) {
					unset( $data['dayOfCycle'] );
				}

				if ( $contact_id ) {

					if( ! empty( $data['tags'] ) ){
						// add tags to this contact
						$this->post( 'contacts/' . $contact_id . '/tags', array( 'tags' => $data['tags'] ) ); 
						
						// unset tags, to not overwrite them while editing contact
						unset($data['tags']);
					}

					// edit contact
					return $this->post( 'contacts/' . $contact_id, $data );
				}
			}
		}

		return $response;
	}

	/**
	 * Remove the subscriber from the list
	 *
	 * @param string $email
	 * @param string $list_hash
	 *
	 * @return Response
	 */
	public function remove_subscriber( $email, $list_hash ) {
		$contacts = $this->get( 'contacts', array(
			'query' => array(
				'email'      => $email,
				'campaignId' => $list_hash,
			)
		) );

		if ( $contacts->is_success() && 1 === count( $contacts->get_data() ) ) {
			$contact_list = $contacts->get_data();
			$contact_id   = $contact_list[ 0 ][ 'contactId' ];
			if ( $contact_id ) {
				return $this->delete( 'contacts/' . $contact_id );
			}
		}

		return new Response( '' );
	}

	/**
	 * Detect whether the system has cURL enabled
	 *
	 * @return GetresponseApi
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
	 * @return GetresponseApi
	 */
	public function set_api_key( $api_key ) {
		$this->api_key = $api_key;

		return $this;
	}

	/**
	 * API Endpoint setter
	 *
	 * @param string $api_endpoint
	 *
	 * @return GetresponseApi
	 */
	public function set_api_endpoint( $api_endpoint ) {
		$this->api_endpoint = rtrim( $api_endpoint, '/' );

		return $this;
	}

	/**
	 * @param string $email
	 * @param array $tags
	 */
	public function add_to_tags( $email, $tags ) {
		// Find contact by email
		$contacts = $this->get( 'contacts', array(
			'query' => array(
				'email' => $email,
			)
		) );

		// Change contact data
		if ( $contacts->is_success() && 1 === count( $contacts->get_data() ) ) {
			$contacts_data = $contacts->get_data();
			$contact_id    = $contacts_data[ 0 ][ 'contactId' ];

			if ( $contact_id ) {

				// add tags to this contact
				$this->post( 'contacts/' . $contact_id . '/tags', array(
					'tags' => array_map( function ( $tag ) {
						return array( 'tagId' => $tag );
					}, $tags )
				) );
			}
		}
	}
}