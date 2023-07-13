<?php

/**
 * Super-simple, minimum abstraction MailerLite API wrapper
 * 
 * @author Based on class by Drew McLellan <drew.mclellan@gmail.com>
 * @version 1.0
 */
class EDD_MailerLite_API {

	private $api_key;
	private $api_endpoint	 = 'https://api.mailerlite.com/api/v2';
	private $verify_ssl		 = false;

	/**
	 * Create a new instance
	 * @param string $api_key Your MailChimp API key
	 */
	function __construct( $api_key, $options = array() ) {
		$this->api_key = $api_key;
	}

	/**
	 * Call an API method. Every request needs the API key, so that is added automatically -- you don't need to pass it in.
	 * @param  string $method The API method to call, e.g. 'lists/list'
	 * @param  array  $args   An array of arguments to pass to the method. Will be json-encoded for you.
	 * @return array          Associative array of json decoded API response.
	 */
	public function call( $api_call, $args = array(), $method = 'POST' ) {
		return $this->_raw_request( $api_call, $method, $args );
	}

	/**
	 * Performs the underlying HTTP request. Not very exciting
	 * @param  string $method The API method to be called
	 * @param  array  $args   Assoc array of parameters to be passed
	 * @return array          Assoc array of decoded result
	 */
	private function _raw_request( $api_call, $method, $args = [] ) {

		$url = $this->api_endpoint . '/' . $api_call;

		$body = null;
		if ( !empty( $args ) ) {
			$body = json_encode( $args );
		}

		$request_args = array(
			'method'		 => $method,
			'timeout'		 => 20,
			'redirection'	 => 5,
			'httpversion'	 => '1.0',
			'blocking'		 => true,
			'headers'		 => array(
				'content-type'			 => 'application/json',
				'X-MailerLite-ApiKey'	 => $this->api_key
			),
			'body'			 => $body,
		);

		$request = wp_remote_post( $url, $request_args );

		return is_wp_error( $request ) ? false : json_decode( wp_remote_retrieve_body( $request ) );
	}

}
