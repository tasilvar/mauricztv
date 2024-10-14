<?php

use bpmj\wpidea\integrations\Interface_External_Service_Integration;
use bpmj\wpidea\integrations\Trait_External_Service_Integration;
use bpmj\wpidea\helpers\Translator_Static_Helper;

/**
 * EDD ConvertKit class, extension of the EDD base newsletter classs
 *
 * @copyright   Copyright (c) 2013, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */
class BPMJ_EDD_Salesmanago extends EDD_Newsletter implements Interface_External_Service_Integration {
use Trait_External_Service_Integration;

    const SERVICE_NAME = 'Salesmanago';

	/**
	 * API Secret
	 * @string
	 */
	public $api_secret;

	/**
	 * Endpoint
	 * @string
	 */
	public $endpoint;

	/**
	 * Client ID
	 * @string
	 */
	public $client_id;


	/**
	 * Api Key (random 10 digits key)
	 * @string
	 */
	public $api_key;


	/**
	 * Owner email
	 * @string
	 */
	public $owner;


	/**
	 * Checkout checkbox
	 * @string
	 */
	public $checkout_mode;


	/**
	 * Sets up the checkout label
	 */
	public function init() {

		$this->checkout_label = edd_get_option( 'salesmanago_checkout_label' , Translator_Static_Helper::translate('newsletter.sign_up') );

		$this->api_secret = edd_get_option( 'salesmanago_api_secret' , '' );
		$this->endpoint = edd_get_option( 'salesmanago_endpoint' , '' );
		$this->client_id = edd_get_option( 'salesmanago_client_id' , '' );
		$this->owner = edd_get_option( 'salesmanago_owner' , '' );

		$this->checkout_mode = (edd_get_option( 'salesmanago_checkout_mode' , '-1' ) == '1');

		$this->api_key = $this->get_random_string( 10 );
	}


	/**
	 * Post request Curl
	 */
	public function do_post_request( $url, $data ) {
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER,
			array(
				'Content-Type: application/json',
				'Content-Length: ' . strlen($data)
			)
		);

		return curl_exec($ch);
	}


	/**
	 * Display metabox on product edit page
	 */
	public function render_metabox() {
		global $post;
	?>
		<input type="text" name="_bpmj_edd_sm_tags" id="salesmanago-tags" value="<?php echo get_post_meta( $post->ID, '_bpmj_edd_sm_tags', true ); ?>">
		<label for="salesmanago-tags"><i><?php _e( 'Wpisz tagi (oddzielając je przecinkiem), które mają być dodane do kontaktu w panelu SALESmanago po zakupie tego produktu.', BPMJ_EDD_SM_DOMAIN ); ?></i></label>
	<?php
	}


	/**
	 * Save the metabox
	 */
	public function save_metabox( $fields ) {
		$fields[] = '_bpmj_edd_sm_tags';
		return $fields;
	}


	/**
	 * SALESmanago api connect
	 */
	public function update_contact( $user_info, $tags ){

		// Make sure an API key has been entered
		if ( empty( $this->client_id ) || empty( $this->endpoint ) || empty( $this->api_secret ) || empty( $this->owner ) )
			return false;

		// Make sure client email has been entered
		if( !isset( $user_info['email'] ) )
			return false;


		$firstname = isset( $user_info['first_name'] ) ? $user_info['first_name'] . ' ' : '';
		$lastname = isset( $user_info['last_name'] ) ? $user_info['last_name'] : '';
		$name = $firstname . $lastname;

		$data = array(
      'clientId' => $this->client_id,
      'apiKey' => $this->api_key,
      'requestTime' => time(),
      'sha' => sha1( $this->api_key . $this->client_id . $this->api_secret ),
      'contact' => array(
      	'email' => $user_info['email'],
      	'name' => $name
      ),
      'owner' => $this->owner,
      'forceOptIn' => true,
      'tags' => $tags
    );

    $json = json_encode($data);

    $url = 'http://' . $this->endpoint .'/api/contact/upsert';
    $request = $this->do_post_request( $url, $json );

    if( $request ){
			$r = json_decode( $request );
			$clientID = $r->{'contactId'};
			setcookie( 'smclient', $clientID, time() + 3650 * 86400, COOKIEPATH, COOKIE_DOMAIN, false);
		}
	}


	/**
	 * Generate random string
	 */
	public function get_random_string( $length ) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}


	/**
	 * Determines if the checkout signup option should be displayed
	 */
	public function show_checkout_signup() {
		if ( $this->checkout_mode && !empty( $this->client_id ) && !empty( $this->endpoint ) && !empty( $this->api_secret ) && !empty( $this->owner ) )
			return true;
	}


	/**
	 * Get tags from products
	 */
	public function completed_download_purchase_signup( $download_id = 0, $payment_id = 0, $download_type = 'default' ) {

        $is_mailing_disabled = apply_filters( 'wpi_disable_mailing', false );
        if ( $is_mailing_disabled ) {
            return false;
        }

		// Make sure an API key has been entered
		if ( empty( $this->client_id ) || empty( $this->endpoint ) || empty( $this->api_secret ) || empty( $this->owner ) )
			return false;

		$user_info = edd_get_payment_meta_user_info( $payment_id );

		$product_tags = get_post_meta( $download_id, '_bpmj_edd_sm_tags', true );
		$product_tags = explode( ',', $product_tags );
		$product_tags = array_unique( $product_tags );

		if( !empty( $product_tags ) ){
			$request = $this->update_contact( $user_info, $product_tags  );
		}
	}


	/**
	 * Subscribe an email to a list
	 */
	public function subscribe_email( $user_info = array(), $product_tags = false ) {

		// Make sure an API key has been entered
		if ( empty( $this->client_id ) || empty( $this->endpoint ) || empty( $this->api_secret ) )
			return false;

		// Retrieve the global list of tags
		$global_tags = edd_get_option( 'bpmj_eddsm_salesmanago_tags', false );
		if ( $global_tags ){

			$global_tags = explode( ',', $global_tags );
			$global_tags = array_unique( $global_tags );

			$request = $this->update_contact( $user_info, $global_tags  );
		}
	}

    public function check_connection(): bool
    {
        $data = array(
            'clientId' => $this->client_id,
            'apiKey' => $this->api_key,
            'requestTime' => time(),
            'sha' => sha1( $this->api_key . $this->client_id . $this->api_secret )
        );

        $json = json_encode($data);

        $url = 'http://' . $this->endpoint .'/api/user/listByClient';
        $request = $this->do_post_request( $url, $json );
        if(!$request){
            return false;
        }
        $response = json_decode( $request );

        return isset($response->success);
    }

}
$edd_salesmanago = new BPMJ_EDD_Salesmanago( 'edd_salesmanago', 'EDD SALESmanago' );
