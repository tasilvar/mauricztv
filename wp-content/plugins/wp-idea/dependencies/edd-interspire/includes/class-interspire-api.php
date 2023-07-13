<?php
/**
 * Klasa tworząca połączenie z Interspire i obsługująca API
 */

class EDD_Interspire_API {

	private $username;
	private $token;
	private $xmlEndpoint;
	private $details = '';

	/**
	 * Kontrukcja klasy
	 */
	function __construct() {
		global $edd_options;

		$this->username = isset( $edd_options['bpmj_edd_in_username'] ) && !empty( $edd_options['bpmj_edd_in_username'] ) ? $edd_options['bpmj_edd_in_username'] : false;
		$this->token = isset( $edd_options['bpmj_edd_in_token'] ) && !empty( $edd_options['bpmj_edd_in_token'] ) ? $edd_options['bpmj_edd_in_token'] : false;
		$this->xmlEndpoint = isset( $edd_options['bpmj_edd_in_xmlEndpoint'] ) && !empty( $edd_options['bpmj_edd_in_xmlEndpoint'] ) ? $edd_options['bpmj_edd_in_xmlEndpoint'] : false;

		if( !$this->username || !$this->token || !$this->xmlEndpoint )
			throw new Exception( 'Configure Interspire Integration Settings' );
	}

	/**
	 * Wywołanie API
	 *
	 * @type (string): Typ operacji (subscribers, authentication, lists)
	 * @method (string): Działanie które chcemy wykonać (GetLists, DeleteSubscriber, xmlapitest)
	 * @args (array): Tablica z wszystkimi zmiennymi
	 */
	public function call( $type, $method, $args = array() ) {

		$xml = '
		<xmlrequest>
			<username>'. $this->username .'</username>
			<usertoken>'. $this->token .'</usertoken>
			<requesttype>'. $type .'</requesttype>
			<requestmethod>'. $method .'</requestmethod>
			<details>';

			// Dodajemy szczegóły zapytania jeżeli istnieją
			if( !empty($args) ){
				$this->parse_details( $args );
				$xml .= $this->details;
			}

		$xml .= '
			</details>
		</xmlrequest>';

		return $this->request( $xml );
	}

	/**
	 * Parsowanie argumentów szczegółowych
	 */
	public function parse_details( $args ){
		foreach( $args as $key => $value ){
			$this->details .= '<'. $key .'>';
				if( is_array($value) && !empty($value) ){
					$this->details .= $this->parse_details( $value );
				} else {
					$this->details .= $value;
				}
			$this->details .= '</'. $key .'>';
		}
	}

	/**
	 * Wysłanie zapytania do Interspire
	 *
	 * @xml (XML): dane do przekazania przez API w formacie XML
	 */
	private function request( $xml ) {

		$request_args = array(
			'method'	=> 'POST',
			'timeout'	=> 20,
			'redirection'	=> 5,
			'httpversion'	=> '1.0',
			'blocking'	=> true,
			'headers'	=> array(
				'content-type' => 'application/xml'
			),
			'body'	=> $xml,
		);

		$request = wp_remote_post( $this->xmlEndpoint, $request_args );
		$request = is_wp_error( $request ) ? false : simplexml_load_string( wp_remote_retrieve_body( $request ) );

		if( $request && $request->status == 'SUCCESS' ){
			return $request;
		} else {
			throw new Exception( '<span style="color: red">'. (isset($request->errormessage)) ?? 'error' .'</span>' );
		}
	}
}
