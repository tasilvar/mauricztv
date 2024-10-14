<?php

namespace bpmj\wp\eddpayu\service;

class PayuTransparent {

	const PAYU_SCRIPT_URI = '/front/widget/js/payu-bootstrap.js';

	/**
	 * @var PayuTransparent
	 */
	private static $instance;

	protected $pos_id;
	protected $pos_auth_key;
	protected $key1;
	protected $key2;
	protected $script_url;

	private function __construct() {
		$this->pos_id       = edd_get_option( 'payu_pos_id' );
		$this->pos_auth_key = edd_get_option( 'payu_pos_auth_key' );
		$this->key1         = edd_get_option( 'payu_key1' );
		$this->key2         = edd_get_option( 'payu_key2' );
		$payu_url_parts     = parse_url( \OpenPayU_Configuration::getServiceUrl() );
		if ( empty( $payu_url_parts[ 'port' ] ) ) {
			$payu_url_parts[ 'port' ] = '';
		}	
		$port               = in_array( (int)$payu_url_parts[ 'port' ], array(
			0,
			80,
			443
		) ) ? '' : ':' . $payu_url_parts[ 'port' ];
		$this->script_url   = "{$payu_url_parts['scheme']}://{$payu_url_parts['host']}{$port}" . self::PAYU_SCRIPT_URI;
	}

	/**
	 * @return PayuTransparent
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new static();
		}

		return self::$instance;
	}

	public function create_script_tag( $script_attrs = array(), $echo = true ) {
		$bloginfo_name = preg_replace("/&#?[a-z0-9]{2,8};/i",'',get_bloginfo( 'name' )); 
		$script_attrs_defaults = array(
			'src'               => $this->script_url,
			'pay-button'        => '#payu-button',
			'merchant-pos-id'   => $this->pos_id,
			'shop-name'         => empty($bloginfo_name) ? 'WP Idea' : $bloginfo_name,
			'total-amount'      => '',
			'currency-code'     => 'PLN',
			'customer-language' => 'pl',
			'store-card'        => 'true',
			'recurring-payment' => 'true',
			'customer-email'    => '',
			'payu-brand'        => 'true',
			'widget-mode'       => 'pay',
			'success-callback'  => 'edd_payu_process_token_response',
		);

		$script_attrs = array_merge( $script_attrs_defaults, $script_attrs );

		$sig_keys = array(
			'merchant-pos-id',
			'shop-name',
			'total-amount',
			'currency-code',
			'customer-language',
			'store-card',
			'recurring-payment',
			'customer-email',
			'payu-brand',
			'widget-mode'
		);
		sort( $sig_keys );
		$sig_src = '';
		foreach ( $sig_keys as $sig_key ) {
			$sig_src .= $script_attrs[ $sig_key ];
		}
		$script_attrs[ 'sig' ] = hash( 'sha256', $sig_src . $this->key2 );
		$script_tag            = '<script';
		foreach ( $script_attrs as $attr => $value ) {
			$script_tag .= ' ' . $attr . '="' . esc_attr( $value ) . '"';
		}
		$script_tag .= '></script>';
		if ( $echo ) {
			echo $script_tag;
		}

		return $script_tag;
	}


}