<?php
/**
 * Super-simple, minimum abstraction MailChimp API v2 wrapper
 * 
 * This probably has more comments than code.
 * 
 * @author Based on class by Drew McLellan <drew.mclellan@gmail.com>
 * @version 1.0
 */
class EDD_MailChimp_API {

	private $api_key;
	private $api_endpoint = 'https://<dc>.api.mailchimp.com/3.0';
	private $verify_ssl   = false;

	/**
	 * Create a new instance
	 * @param string $api_key Your MailChimp API key
	 */
	function __construct( $api_key, $options = [] ) {
		$this->api_key = $api_key;
		list(, $datacentre) = explode( '-', $this->api_key );
		$this->api_endpoint = str_replace( '<dc>', $datacentre, $this->api_endpoint );
	}

	public function call( $path, $method, $args = [] ) {
	    return $this->_raw_request( $path, $method, $args);
	}

	/**
	 * Performs the underlying HTTP request. Not very exciting
	 * @param  string $method The API method to be called
	 * @param  array  $args   Assoc array of parameters to be passed
	 * @return array          Assoc array of decoded result
	 */
	private function _raw_request( $path, $method, $args = [] ) {
		$url = $this->api_endpoint . '/' . $path;

		$request_args = [
			'timeout'     => 20,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => [
				'content-type' => 'application/json',
			    'Authorization' => 'Bearer ' . $this->api_key
			],
			'body'        => ( $args ),
		];
		
		switch ($method) {
		    case 'GET':
		        $request = wp_remote_get( $url, $request_args );
		        break;
		    case 'POST':
		        $request = wp_remote_post( $url, $request_args );
		        break;
		    default:
		        return false;
		}

		return is_wp_error( $request ) ? false : json_decode( wp_remote_retrieve_body( $request ) );

	}

}