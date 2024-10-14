<?php

use Paynow\Environment;
use Paynow\Client;
use Paynow\Service\ShopConfiguration;
use Paynow\Service\Payment;
use Paynow\Notification;
use Paynow\Model\Payment\Status;
use Paynow\Exception\SignatureVerificationException;
use Paynow\Exception\PaynowException;
use bpmj\wpidea\helpers\Price_Formatting;

function bpmj_paynow_edd_process_payment( $purchase_data ) {
    global $edd_options;

    $payment_data = array(
        'price'			 => $purchase_data[ 'price' ],
        'date'			 => $purchase_data[ 'date' ],
        'user_email'	 => $purchase_data[ 'user_email' ],
        'purchase_key'	 => $purchase_data[ 'purchase_key' ],
        'currency'		 => $edd_options[ 'currency' ],
        'downloads'		 => $purchase_data[ 'downloads' ],
        'cart_details'	 => $purchase_data[ 'cart_details' ],
        'user_info'		 => $purchase_data[ 'user_info' ],
        'status'		 => 'pending'
    );

    $payment_id = edd_insert_payment( $payment_data );

    if ( ! $payment_id ) {
        edd_record_gateway_error( __( 'Payment error', 'bpmj_paynow_edd' ), sprintf( __( 'Payment error befor sending to Paynow.  Data: %s', 'bpmj_paynow_edd' ), json_encode( $payment_data ) ), $payment_id );
        edd_send_back_to_checkout( '?payment-mode=' . $purchase_data[ 'post_data' ][ 'edd-gateway' ] );
    } else {
        try {
            if ( empty( $edd_options['paynow_access_key'] ) || empty( $edd_options['paynow_signature_key'] ) ) {
                edd_record_gateway_error( __( 'Payment error', 'bpmj_paynow_edd' ), __( 'You do not set Paynow Access Key or Paynow Signature Key', 'bpmj_paynow_edd' ) );
                edd_send_back_to_checkout( '?payment-mode=' . $purchase_data[ 'post_data' ][ 'edd-gateway' ] );
            }

            $environment = 'production' === $edd_options['paynow_environment'] ?
                Environment::PRODUCTION :
                Environment::SANDBOX;

            $client = new Client(
                $edd_options['paynow_access_key'],
                $edd_options['paynow_signature_key'],
                $environment,
                get_bloginfo('name')
            );

            $localhosts = [
                '127.0.0.1',
                '::1'
            ];

            if( ! in_array( $_SERVER['REMOTE_ADDR'], $localhosts ) ) {
                $paynow_config = new ShopConfiguration($client);
                $paynow_config->changeUrls( get_permalink( $edd_options[ 'success_page' ] ), home_url() . '/?payment-confirmation=paynow');
            }

            $payment_data = [
                'amount' => Price_Formatting::round_and_format_to_int( $purchase_data[ 'price' ], Price_Formatting::MULTIPLY_BY_100 ),
                'currency' => $edd_options[ 'currency' ],
                'externalId' => $payment_id,
                'description' => __( 'Order no: ', 'bpmj_paynow_edd' ) . $payment_id,
                'buyer' => [
                    'email' => $purchase_data[ 'user_email' ],
                ],
                'continueUrl' => get_permalink( $edd_options[ 'success_page' ] ),
            ];

            $payment = new Payment($client);

                $payment_data = $payment->authorize($payment_data, uniqid($payment_id, true));

            edd_empty_cart();

            wp_redirect( $payment_data->getRedirectUrl() );
            exit;
        } catch (PaynowException $exception) {
            wp_die(__( 'The payment system is incorrectly configured. Notify the platform administrator of this fact.', BPMJ_EDDCM_DOMAIN ));
        }
    }
}

add_action( 'edd_gateway_paynow_gateway', 'bpmj_paynow_edd_process_payment' );

function bpmj_edd_listen_for_paynow() {
    if ( isset( $_GET['payment-confirmation'] ) && $_GET['payment-confirmation'] === 'paynow' ) {
        global $edd_options;

        $data = trim( file_get_contents( 'php://input' ) );

        $headers = [];
        foreach ( $_SERVER as $key => $value ) {
            if ( substr( $key, 0, 5 ) == 'HTTP_' ) {
                $subject = ucwords( str_replace( '_', ' ', strtolower( substr( $key, 5 ) ) ) );
                $headers[ str_replace( ' ', '-', $subject ) ] = $value;
            }
        }

        $notification_data = json_decode( $data, true );

        try {
            $payment = edd_get_payment( $notification_data['externalId'] );

            if ( ! $payment ) {
                exit( sprintf( __( 'Payment %s was not found.', 'bpmj_paynow_edd' ), $notification_data['externalId'] ) );
            }

            if ( 'publish' === $payment->status )
                exit;

            // If not throw exception then paynow signature is verified
            new Notification( $edd_options['paynow_signature_key'], $data, $headers );

            $notification_status = $notification_data['status'];

            switch ( $notification_status ) {
                case Status::STATUS_PENDING:
                    edd_update_payment_status( $payment->ID, 'pending' );
                    break;
                case Status::STATUS_REJECTED:
                    edd_update_payment_status( $payment->ID, 'revoked' );
                    break;
                case Status::STATUS_CONFIRMED:
                    edd_update_payment_status( $payment->ID, 'completed' );
                    break;
                case Status::STATUS_ERROR:
                    edd_update_payment_status( $payment->ID, 'failed' );
                    break;
            }
        } catch ( SignatureVerificationException $exception ) {
            exit( sprintf( __( 'Signature is not verified. Payment %s is not processed.', 'bpmj_paynow_edd' ), $notification_data['externalId'] ) );
        }

        exit;
    }
}

add_action( 'init', 'bpmj_edd_listen_for_paynow' );

function bpmj_edd_paynow_gateway_cc_form() {

}

add_action( 'edd_paynow_gateway_cc_form', 'bpmj_edd_paynow_gateway_cc_form' );
