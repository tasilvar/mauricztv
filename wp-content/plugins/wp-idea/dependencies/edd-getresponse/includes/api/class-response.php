<?php

namespace bpmj\wp\eddres\api;

/**
 * API call's response
 */
class Response {

	/**
	 * Response HTTP code
	 * @var string
	 */
	protected $code;

	/**
	 * Response data
	 * @var array
	 */
	protected $data;

	/**
	 * Setup the object from JSON
	 *
	 * @param string $json_string
	 * @param int $http_code
	 */
	public function __construct( $json_string, $http_code = 200 ) {
		$this->data = json_decode( $json_string, true );
		$this->code = (int) $http_code;

	}

	/**
	 * Get response code
	 *
	 * @return int
	 */
	public function get_code() {
		return $this->code;
	}

	/**
	 * Get response data (either full or the specified field)
	 *
	 * @param string $key
	 *
	 * @return array
	 */
	public function get_data( $key = null ) {
		if ( $key ) {
			return empty( $this->data[ $key ] ) ? null : $this->data[ $key ];
		}

		return $this->data;
	}

	/**
	 * Is the response successful
	 *
	 * @return bool
	 */
	public function is_success() {
		return $this->code >= 200 && $this->code < 300;
	}

}
