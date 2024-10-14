<?php

namespace bpmj\wp\eddpayu;

class OAuthCacheTransient implements \OauthCacheInterface {

	const CACHE_EXPIRATION = 14400; // 4 hours

	/**
	 * @param string $key
	 *
	 * @return null | object
	 */
	public function get( $key ) {
		return get_transient( $key );
	}

	/**
	 * @param string $key
	 * @param object $value
	 *
	 * @return bool
	 */
	public function set( $key, $value ) {
		return set_transient( $key, $value, static::CACHE_EXPIRATION );
	}

	/**
	 * @param string $key
	 *
	 * @return bool
	 */
	public function invalidate( $key ) {
		return delete_transient( $key );
	}
}