<?php
/*
Plugin Name: Easy Digital Downloads - Coinbase
Plugin URI: https://easydigitaldownloads.com/extensions/coinbase
Description: Adds support for accepting crytocurrency payments through Coinbase Commerce and Easy Digital Downloads
Version: 1.1
Author: Easy Digital Downloads
Author URI: https://easydigitaldownloads.com
Contributors: mordauk
*/

use bpmj\wpidea\integrations\Interface_External_Service_Integration;
use bpmj\wpidea\integrations\Trait_External_Service_Integration;
use bpmj\wpidea\http\Http_Client;

class EDD_Coinbase implements Interface_External_Service_Integration{
    use Trait_External_Service_Integration;

    const SERVICE_NAME = 'Coinbase';
    const API_VERSION = '2018-03-22';

    CONST API_URL_CHECKOUTS = 'https://api.commerce.coinbase.com/checkouts';

	private $api_key;

	public function __construct() {

		if( ! function_exists( 'edd_get_option' ) ) {
			return;
		}

		$this->api_key = trim( edd_get_option( 'edd_coinbase_api_key', '' ) );

		add_action( 'init',                           array( $this, 'textdomain' ) );
		add_action( 'edd_gateway_coinbase',           array( $this, 'process_payment' ) );
		add_action( 'init',                           array( $this, 'listener' ) );
		add_action( 'edd_coinbase_cc_form',           '__return_false' );
		add_action( 'template_redirect',              array( $this, 'process_confirmation' ) );

		add_filter( 'edd_payment_gateways',           array( $this, 'register_gateway' ) );
		add_filter( 'edd_currencies',                 array( $this, 'currencies' ) );
		add_filter( 'edd_sanitize_amount_decimals',   array( $this, 'btc_decimals' ) );
		add_filter( 'edd_format_amount_decimals',     array( $this, 'btc_decimals' ) );
		add_filter( 'edd_settings_sections_gateways', array( $this, 'subsection' ), 10, 1 );
		add_filter( 'edd_settings_gateways',          array( $this, 'settings' ) );

	}

	public function textdomain() {

		// Set filter for plugin's languages directory
		$lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
		$lang_dir = apply_filters( 'edd_coinbase_languages_directory', $lang_dir );

		// Traditional WordPress plugin locale filter
		$locale        = apply_filters( 'plugin_locale',  get_locale(), 'edd-coinbase' );
		$mofile        = sprintf( '%1$s-%2$s.mo', 'edd-coinbase', $locale );

		// Setup paths to current locale file
		$mofile_local  = $lang_dir . $mofile;
		$mofile_global = WP_LANG_DIR . '/edd-coinbase/' . $mofile;

		if ( file_exists( $mofile_global ) ) {
			load_textdomain( 'edd-coinbase', $mofile_global );
		} elseif ( file_exists( $mofile_local ) ) {
			load_textdomain( 'edd-coinbase', $mofile_local );
		} else {
			// Load the default language files
			load_plugin_textdomain( 'edd-coinbase', false, $lang_dir );
		}

	}

	public function register_gateway( $gateways ) {

		$gateways['coinbase'] = array(
			'checkout_label'  => edd_get_option( 'edd_coinbase_checkout_label', __( 'Bitcoin', 'edd-coinbase' ) ),
			'admin_label'     => __( 'Coinbase', 'edd-coinbase' )
		);

		return $gateways;

	}

	public function process_payment( $purchase_data ) {

		if( ! $this->is_api_valid() ) {
			edd_set_error( 'edd_coinbase_api_invalid', __( 'Please enter your Coinbase API key in Settings', 'edd-coinbase' ) );
			edd_send_back_to_checkout( '?payment-mode=coinbase' );
		}

		$purchase_summary = '';
		if( is_array( $purchase_data['cart_details'] ) && ! empty( $purchase_data['cart_details'] ) ) {

			foreach( $purchase_data['cart_details'] as $item ) {
				$purchase_summary .= $item['name'];
				$price_id = isset( $item['item_number']['options']['price_id'] ) ? absint( $item['item_number']['options']['price_id'] ) : false;
				if ( false !== $price_id ) {
					$purchase_summary .= ' - ' . edd_get_price_option_name( $item['id'], $item['item_number']['options']['price_id'] );
				}
				$purchase_summary .= ', ';
			}

			$purchase_summary = rtrim( $purchase_summary, ', ' );

		}

		$headers = array(
			'X-CC-Api-Key' => $this->api_key,
			'X-CC-Version' => '2018-03-22',
			'Content-Type' => 'application/json'
		);

		$args = array(
			'headers' => $headers,
			'body'   => json_encode( array(
				'name' => get_bloginfo( 'name' ),
				'description' => $purchase_summary,
				'pricing_type' => 'fixed_price',
				'local_price' => array(
					'amount' => $purchase_data['price'],
					'currency' => edd_get_currency(),
				),
				'metadata' => array(
					'payment_key' => $purchase_data['purchase_key'],
					'email' => $purchase_data['user_email']
				),
				'redirect_url' => add_query_arg( array(
					'payment_key' => $purchase_data['purchase_key'],
					'gateway' => 'coinbase'
				), edd_get_success_page_uri() )
			) )
		);

		$request = wp_remote_post( 'https://api.commerce.coinbase.com/charges', $args );

//		echo '<pre>'; print_r( $request ); echo '</pre>'; exit;
		if( is_wp_error( $request ) ) {
			edd_set_error( $request->get_error_code(), $request->get_error_message() );
			edd_send_back_to_checkout( '?payment-mode=coinbase' );
		}

		$body = json_decode( wp_remote_retrieve_body( $request ) );

		if( 200 !== wp_remote_retrieve_response_code( $request ) && 201 !== wp_remote_retrieve_response_code( $request ) ) {

			edd_set_error( $body->error->code, $body->error->message );
			edd_send_back_to_checkout( '?payment-mode=coinbase' );

		}

		$payment_data = array(
			'price'         => $purchase_data['price'],
			'date'          => $purchase_data['date'],
			'user_email'    => $purchase_data['user_email'],
			'purchase_key'  => $purchase_data['purchase_key'],
			'currency'      => edd_get_currency(),
			'downloads'     => $purchase_data['downloads'],
			'cart_details'  => $purchase_data['cart_details'],
			'user_info'     => $purchase_data['user_info'],
			'status'        => 'pending',
			'gateway'       => 'coinbase'
		);

		// record the pending payment
		$payment_id = edd_insert_payment( $payment_data );
		if ( $payment_id ) {

			edd_set_payment_transaction_id( $payment_id, $body->data->code );
			edd_insert_payment_note( $payment_id, sprintf( __( 'Pending Coinbase charge created, code %s', 'edd-coinbase' ), $body->data->code ) );
			wp_redirect( $body->data->hosted_url ); exit;
		}


	}

	public function listener() {

		if( empty( $_GET['edd-listener'] ) ) {
			return;
		}

		if( 'coinbase' != $_GET['edd-listener'] ) {
			return;
		}

		if( ! $this->is_api_valid() ) {
			return;
		}

		$body = @file_get_contents( 'php://input' );
		$data = json_decode( $body );

		if( empty( $data->event ) ) {
			return;
		}

		$type       = $data->event->type;
		$key        = sanitize_text_field( $data->event->data->metadata->payment_key );
		$trans_id   = $data->event->data->code;
		$payment_id = edd_get_purchase_id_by_key( $key );

		//edd_debug_log( 'Coinbase webhook processing for payment ' . $payment_id );

		if( ! $payment_id ) {
			//edd_debug_log( 'Coinbase webhook processing stopped because payment was not found from provided key' );
			die( 'no payment found' );
		}

		if( 'charge:confirmed' !== $type ) {
			//edd_debug_log( 'Coinbase webhook processing stopped because it is not a charge:confirmed type' );
			die( 'charge not completed' );
		}

		if( ! edd_is_payment_complete( $payment_id ) ) {

			//edd_debug_log( 'Coinbase webhook processing preparing to verify charge for payment ' . $payment_id );

			// Query charge from Coinbase to verify it
			$args = array(
				'headers' => array(
					'X-CC-Api-Key' => $this->api_key,
					'X-CC-Version' => '2018-03-22',
					'Content-Type' => 'application/json'
				)
			);
			$request = wp_remote_get( 'https://api.commerce.coinbase.com/charges/' . $trans_id, $args );

			$body = json_decode( wp_remote_retrieve_body( $request ) );

			if( 200 === wp_remote_retrieve_response_code( $request ) || 201 === wp_remote_retrieve_response_code( $request ) ) {

				//edd_debug_log( 'Coinbase webhook charge retrieved successfully. Charge object: ' . var_export( $body, true ) );

				if( ! empty( $body->data->confirmed_at ) ) {

					//edd_debug_log( 'Coinbase webhook processing for payment ' . $payment_id . ' successful, marking payment as complete' );
					edd_update_payment_status( $payment_id, 'publish' );

				}

			} else {

				//edd_debug_log( 'Coinbase webhook processing failed. Non 200/201 returned by Coinbase. Charge response: ' . var_export( $body, true ) );

			}

		}

	}

	public function process_confirmation() {

		if ( ! isset( $_GET['payment_key'] ) || ! isset( $_GET['gateway'] ) || 'coinbase' !== $_GET['gateway'] ) {
			return;
		}

		if( ! edd_is_success_page() || ! edd_is_gateway_active( 'coinbase' ) ) {
			return;
		}

		$payment_key = isset( $_GET['payment_key'] ) ? sanitize_text_field( $_GET['payment_key'] ) : false;

		if( empty( $payment_key ) ) {
			return;
		}

		$payment_id = edd_get_purchase_id_by_key( $payment_key );

		$payment = new EDD_Payment( $payment_id );

		if( $payment && $payment->ID > 0 ) {

			// Query charge from Coinbase to verify it
			$args = array(
				'headers' => array(
					'X-CC-Api-Key' => $this->api_key,
					'X-CC-Version' => '2018-03-22',
					'Content-Type' => 'application/json'
				)
			);
			$request = wp_remote_get( 'https://api.commerce.coinbase.com/charges/' . $payment->transaction_id, $args );

			$body = json_decode( wp_remote_retrieve_body( $request ) );

			if( 200 === wp_remote_retrieve_response_code( $request ) || 201 === wp_remote_retrieve_response_code( $request ) ) {

				//edd_debug_log( 'Coinbase confirmation charge retrieved successfully. Charge object: ' . var_export( $body, true ) );

				if( ! empty( $body->data->confirmed_at ) ) {

					// Purchase verified, set to completed
					//edd_debug_log( 'Coinbase confirmation processing for payment ' . $payment_id . ' successful, marking payment as complete' );
					$payment->status = 'publish';
					$payment->save();

				}

			} else {

				//edd_debug_log( 'Coinbase confirmation processing failed. Non 200/201 returned by Coinbase. Charge response: ' . var_export( $body, true ) );

			}

		}

	}

	public function currencies( $currencies ) {

		$currencies['BTC'] = __( 'Bitcoin', 'edd-coinbase' );

		return $currencies;
	}

	function btc_decimals( $decimals = 2 ) {
		global $edd_options;

		$currency = edd_get_currency();

		switch ( $currency ) {
			case 'BTC' :

				$decimals = 8;
				break;
		}

		return $decimals;
	}

	public function subsection( $sections ) {
		$sections['coinbase'] = __( 'Coinbase', 'edd-coinbase' );
		return $sections;
	}

	public function settings( $settings ) {

		$coinbase_settings = array(
			array(
				'id'      => 'edd_coinbase_header',
				'name'    => '<strong>' . __( 'Coinbase', 'edd-coinbase' ) . '</strong>',
				'desc'    => '',
				'type'    => 'header',
				'size'    => 'regular'
			),
			array(
				'id'      => 'edd_coinbase_checkout_label',
				'name'    => __( 'Checkout Label', 'edd-coinbase' ),
				'desc'    => __( 'Enter text you would like shown on checkout for customers. This is shown when selecting the payment method.' ),
				'type'    => 'text',
				'std'     => 'Bitcoin'
			),
			array(
				'id'      => 'edd_coinbase_api_key',
				'name'    => __( 'API Key', 'edd-coinbase' ),
				'desc'    => __( 'Enter your Coinbase API key' ),
				'type'    => 'text'
			),
			array(
				'id'    => 'coinbase_webhook_description',
				'type'  => 'descriptive_text',
				'name'  => __( 'Webhooks', 'edd-coinbase' ),
				'desc'  =>
					'<p>' . sprintf( __( 'In order for Coinbase to function completely, you must configure your webhooks. Visit your <a href="%s" target="_blank">account dashboard</a> to configure them. Please add a webhook endpoint for the URL below.', 'edd-coinbase' ), 'https://commerce.coinbase.com/dashboard/settings' ) . '</p>' .
					'<p><strong>' . sprintf( __( 'Webhook URL: %s', 'edds' ), home_url( 'index.php?edd-listener=coinbase' ) ) . '</strong></p>' .
					'<p>' . sprintf( __( 'See our <a href="%s">documentation</a> for more information.', 'edd-coinbase' ), 'https://docs.easydigitaldownloads.com/article/314-coinbase-payment-gateway-setup-documentation' ) . '</p>'
			),
		);

		if ( version_compare( EDD_VERSION, 2.5, '>=' ) ) {
			$coinbase_settings = array( 'coinbase' => $coinbase_settings );
		}


		return array_merge( $settings, $coinbase_settings );

	}

	public function is_api_valid() {

		if( empty( $this->api_key ) ) {
			return false;
		}

		return true;
	}

    public function check_connection(): bool
    {
        $client = new Http_Client();
        $response = $client->create_request()
            ->set_url(self::API_URL_CHECKOUTS)
            ->add_header('X-CC-Api-Key', $this->api_key)
            ->add_header('X-CC-Version', self::API_VERSION)
            ->add_header('Content-Type', 'application/json')
            ->send();

        if($response->is_error()){
            return false;
        }

        $body = $response->get_decoded_body();
        return isset($body->data);
    }

}

function edd_coinbase_init() {
	$coinbase = new EDD_Coinbase;
}
add_action( 'plugins_loaded', 'edd_coinbase_init' );
