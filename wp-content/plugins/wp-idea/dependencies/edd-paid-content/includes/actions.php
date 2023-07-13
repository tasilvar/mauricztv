<?php

use bpmj\wpidea\helpers\In_Memory_Cache_Static_Helper;
use bpmj\wpidea\Post_Meta;

/*
 * Przekierowanie do strony logowania, jeżeli użytkownik jest na stronie zastrzeżonej, ale nie jest zalogowany
 */

function bpmj_eddpc_redirect_to_login_page() {
	global $wp_query;

	if ( is_singular() && !is_user_logged_in() ) {
		$post_id = $wp_query->get_queried_object_id();

		// Jeżeli jest zabezpieczony - przekieruj na stronę logowania
		if ( bpmj_eddpc_is_restricted( $post_id ) ) {

			// URL powrotny
			$return_url = get_permalink( $post_id );

			// URL logowania
			$url = wp_login_url( $return_url );

			wp_redirect( $url );
			exit;
		}
	}
}

add_action( 'template_redirect', 'bpmj_eddpc_redirect_to_login_page', 1 );

/*
 * Przekierowanie, jeżeli użytkownik jest na stronie zastrzeżonej 
 * 
 * @since       1.5
 */

function bpmj_eddpc_redirect_no_access() {
	global $wp_query;

	if ( is_singular() ) {
		$uid = get_current_user_id();
		if ( empty( $uid ) )
			return;

		$post_id = $wp_query->get_queried_object_id();

		// Jeżeli jest zabezpieczony
		$restricted = bpmj_eddpc_is_restricted( $post_id );
		if ( $restricted ) {

			// czy ma dostęp
			$access = bpmj_eddpc_user_can_access( $uid, $restricted, $post_id );

			if ( 'valid' == $access[ 'status' ] ) {
				return;
			}

			$redir = bpmj_eddpc_get_redirect_url( $post_id );
			if ( empty( $redir ) ) {
				return;
			}

			wp_redirect( $redir );
			exit;
		}
	}
}

add_action( 'template_redirect', 'bpmj_eddpc_redirect_no_access', 2 );

/**
 * Dopisanie zakupionego czasu dostępu do konta kupującego
 *
 * @since       1.3
 *
 * @param int $download_id
 * @param int $payment_id
 * @param string $type
 * @param array $cart_item
 * @param int $cart_index
 *
 * @return void
 */
function bpmj_eddpc_add_time_on_purchase(
	$download_id = 0, $payment_id = 0, $type = 'default', $cart_item = array(),
	$cart_index = 0
) {

	global $edd_options;
	global $bpmj_eddpc_tnow;

	$payment_meta = edd_get_payment_meta( $payment_id );

	$user_info = edd_get_payment_meta_user_info( $payment_id );
	$user      = get_user_by( 'email', $user_info[ 'email' ] );

	if ( ! $user ) {
		return;
	}

	$user_id = $user->ID;

	if ( $type == 'bundle' ) {
		$downloads = edd_get_bundled_products( $download_id );
	} else {
		$downloads   = array();
		$downloads[] = $download_id;
	}

	if ( ! is_array( $downloads ) ) {
		return;
	}

	foreach ( $downloads as $d_id ) {

	    if ( 'bundle' === $type ) {
            $price_id = Post_Meta::get( $d_id, '_edd_default_price_id' );
        } else {
            $price_id = isset($cart_item['item_number']['options']['price_id']) ? (int)$cart_item['item_number']['options']['price_id'] : false;
        }

		bpmj_eddpc_add_time( $user_id, $d_id, $price_id, strtotime( $payment_meta[ 'date' ] ) );
	}
}

add_action( 'edd_complete_download_purchase', 'bpmj_eddpc_add_time_on_purchase', 99, 5 );

/**
 * @param int $user_id
 * @param int $download_id
 * @param int $price_id
 * @param int|null $buy_time_ts
 */
function bpmj_eddpc_add_time($user_id, $download_id, $price_id, $buy_time_ts = null) {
	global $bpmj_eddpc_tnow;

	if (!$buy_time_ts) {
		$buy_time_ts = $bpmj_eddpc_tnow;
	}
	if ( edd_has_variable_prices( $download_id ) ) {
		if ( empty( $price_id ) ) {
			$price_id = edd_get_default_variable_price( $download_id );
		}
		$access_time		 = bpmj_eddpc_get_access_time_variable( $download_id, $price_id );
		$access_time_unit	 = bpmj_eddpc_get_access_time_unit_variable( $download_id, $price_id );
	} else {
		$access_time		 = bpmj_eddpc_get_access_time_single( $download_id );
		$access_time_unit	 = bpmj_eddpc_get_access_time_unit_single( $download_id );
	}

	$access_start_raw = get_post_meta( $download_id, '_bpmj_eddpc_access_start', true );
	$access_start = 0;
	
	if( !empty( $access_start_raw ) ) {
		$access_start = bpmj_eddpc_adjust_timestamp( strtotime( $access_start_raw ), false );
	}

	$user_access_time = bpmj_eddpc_get_access( $user_id );
	if ( ! $user_access_time ) {
		$user_access_time = array();
	}

	$current_value = ( isset( $user_access_time[ $download_id ][ 'access_time' ] ) ) ? $user_access_time[ $download_id ][ 'access_time' ] : 1;

    if ( ! empty( $current_value) &&  $current_value > $bpmj_eddpc_tnow ) {
        $access_start = 0;
    }

	$user_access_time[ $download_id ][ 'access_time' ]	 = bpmj_eddpc_calculate_access_time( $current_value, $access_time, $access_time_unit, $access_start );
	$user_access_time[ $download_id ][ 'renewals' ]	 = array();
	$user_access_time[ $download_id ][ 'buy_time' ]	 = $buy_time_ts;
	if ( empty( $user_access_time[ $download_id ][ 'price_id' ] ) ) {
		$user_access_time[ $download_id ][ 'price_id' ] = array( );
	}
    if ( !empty( $current_value ) && ( $current_value < $bpmj_eddpc_tnow ) ) { // dostęp przeterminowany - usuń informacje o wczesniej wykupionych wariantach
		$user_access_time[ $download_id ][ 'price_id' ] = array( );
	}
	if ( $price_id && ! in_array( $price_id, $user_access_time[ $download_id ][ 'price_id' ] ) ) {
		$user_access_time[ $download_id ][ 'price_id' ][] = $price_id;
	}

	if ( !empty( $access_start ) ) {
		$user_access_time[ $download_id ][ 'total_time' ] = $bpmj_eddpc_tnow - $access_start;
	}

	bpmj_eddpc_set_access_and_set_flag( $user_id, $user_access_time );

	bpmj_eddpc_user_update_total_time( $user_id, $download_id );

    In_Memory_Cache_Static_Helper::delete('bpmj_eddpc_user_update_total_time_' . $user_id . '_' . $download_id);
}

// integracja z Recurring Payments
function bpmj_eddpc_recurring_payment_received_notice( $payment, $parent_id, $amount, $txn_id, $unique_key ) {

	$payment_id		 = $parent_id;
	$payment_p		 = new EDD_Payment( $payment_id );
	$cart_details	 = $payment_p->cart_details;

	if ( is_array( $cart_details ) ) {

		foreach ( $cart_details as $cart_index => $download ) {

			$download_type = edd_get_download_type( $download[ 'id' ] );

			for ( $i = 0; $i < $download[ 'quantity' ]; $i++ ) {
				bpmj_eddpc_add_time_on_purchase( $download[ 'id' ], $payment_id, $download_type, $download, $cart_index );
			}
		}
	}
}

add_action( 'edd_recurring_record_payment', 'bpmj_eddpc_recurring_payment_received_notice', 10, 5 );

/**
 * Dodanie nagłówka "Validity" do shortcode
 * [download_history]
 */
function bpmj_eddpc_add_shortcode_head() {
	echo '<th>' . __( 'Validity', 'edd-paid-content' ) . '</th>';
}

add_action( 'edd_download_history_header_end', 'bpmj_eddpc_add_shortcode_head' );

/**
 * Dodanie wartości do kolumny "Validity"
 * w shortcode [download_history]
 */
function bpmj_eddpc_add_shortcode_row( $payment_id, $download_id ) {

	$downloads		 = edd_get_payment_meta_cart_details( $payment_id, true );
	$current_user_id = get_current_user_id();
	$access_time	 = get_user_meta( $current_user_id, "_bpmj_eddpc_access", true );

	foreach ( $downloads as $download ) {
		if ( $download_id == $download[ 'id' ] ) {
			if ( isset( $access_time[ $download[ 'id' ] ] ) ) {
				if ( $access_time[ $download[ 'id' ] ][ 'access_time' ] ) {
					$time = date( 'd.m.Y H:i:s', $access_time[ $download[ 'id' ] ][ 'access_time' ] );
				}
			}

			if ( $time ) {
				echo '<td>' . $time;
				if ( isset( $access_time[ $download[ 'id' ] ][ 'discount' ] ) ) {
					if ( edd_is_discount_active( $access_time[ $download[ 'id' ] ][ 'discount' ] ) && ( get_post_meta( $access_time[ $download[ 'id' ] ][ 'discount' ], '_edd_discount_uses', true ) == '0' ) ) {
						echo '<br>' . __( 'Discount code:', 'edd-paid-content' );
						$discount_code	 = get_post_meta( $access_time[ $download[ 'id' ] ][ 'discount' ], '_edd_discount_code', true );
						$url			 = edd_get_checkout_uri( array(
							'edd_action'	 => 'add_to_cart',
							'download_id'	 => $download[ 'id' ],
							'discount'		 => $discount_code
						) );
						echo ' <a href="' . $url . '">' . $discount_code . '</a>';
					}
				}
				echo '</td>';
			} else {
				echo '<td>' . __( 'No limit', 'edd-paid-content' ) . '</td>';
			}

			return;
		}
	}
}

add_action( 'edd_download_history_row_end', 'bpmj_eddpc_add_shortcode_row', 10, 2 );

/**
 *
 */
function bpmj_eddpc_process_encrypted_url() {
	if ( empty( $_GET[ 'bpmj_eddpc_url' ] ) ) {
		return;
	}

	$decrypted    = bpmj_eddpc_decrypt_link( $_GET[ 'bpmj_eddpc_url' ] );
	$redirect_url = remove_query_arg( array( 'bpmj_eddpc_url' ) );
	if ( false === $decrypted ) {
		wp_redirect( $redirect_url );
		die();
	}

	$user_id = empty( $decrypted[ 'user_id' ] ) ? null : $decrypted[ 'user_id' ];

	if ( $user_id && $user_id !== get_current_user_id() ) {
		wp_redirect( $redirect_url );
		die();
	}

	if ( ! empty( $decrypted[ 'post_id' ] ) ) {
		$restricted = bpmj_eddpc_is_restricted( $decrypted[ 'post_id' ] );
		$access     = bpmj_eddpc_user_can_access( $user_id, $restricted, $decrypted[ 'post_id' ] );
		if ( 'valid' !== $access[ 'status' ] ) {
			wp_redirect( $redirect_url );
			die();
		}
	}

	/*
	 * Normalize those URLs so that both are non-SSL
	 */
	$normalized_decrypted_url = str_replace( 'https://', 'http://', $decrypted[ 'url' ] );
	$normalized_site_url      = str_replace( 'https://', 'http://', get_option( 'siteurl' ) );

    $disposition = apply_filters( 'bpmj_eddpc_encrypted_url_disposition', 'attachment' );

	if ( 0 === strpos( $normalized_decrypted_url, $normalized_site_url ) ) {
		$url  = str_replace( $normalized_site_url, '', $normalized_decrypted_url );
		$path = rtrim( ABSPATH, '/' ) . $url;
		if ( ! file_exists( $path ) ) {
			wp_redirect( $redirect_url );
			die();
		}

		$type        = wp_check_filetype( basename( $path ), wp_get_mime_types() );
		header( 'Content-Type: ' . ( ! empty( $type[ 'type' ] ) ? $type[ 'type' ] : 'application/octet-stream' ) );
		header( 'Content-Disposition: ' . $disposition . '; filename="' . basename( $path ) . '"' );
		readfile( $path );
		die;
	}

    do_action( 'wpi_process_encrypted_url_before_final_redirection', $decrypted[ 'url' ], $disposition );

	wp_redirect( $decrypted[ 'url' ] );
	die();
}

add_action( 'init', 'bpmj_eddpc_process_encrypted_url' );

function bpmj_edd_validate_demo_purchase( $valid_data, $data ) {
    global $edd_options;

    if( !empty( $data[ 'edd_email' ] ) && ( ! isset( $edd_options[ 'disable_demo_sales' ] ) || $edd_options['disable_demo_sales'] != 1 ) ) {
        $demo_products_ids = array();
        foreach ( edd_get_cart_contents() as $item ) {
            $item_price = edd_get_cart_item_price( $item[ 'id' ], $item[ 'options' ] );
            if ( $item_price == "0.00" && !in_array( $item[ 'id' ], $demo_products_ids ) ) {
                $demo_products_ids[] = $item[ 'id' ];
            }
        }

        $payments = edd_get_payments( $args = array(
            'meta_key' => '_edd_payment_user_email',
            'meta_value' => $data[ 'edd_email' ]
        ) );

        $purchased_demo_ids = array();

        foreach ( $payments as $payment ) {
            $payments_meta = edd_get_payment_meta_cart_details( $payment->ID );
            foreach ( $payments_meta as $meta ) {
                if ( in_array( $meta[ 'item_number' ][ 'id' ], $demo_products_ids )
                    && ( $meta[ 'item_price' ] == 0 || $meta[ 'item_price' ] == 0.00 )
                    && !in_array( $meta[ 'item_number' ][ 'id' ], $purchased_demo_ids ) )
                {
                    $purchased_demo_ids[] = $meta[ 'item_number' ][ 'id' ];
                }
            }
        }

        if ( !empty( $purchased_demo_ids ) ) {
            $demo_titles = array();
            foreach ( $purchased_demo_ids as $demo_id ) {
                $demo_data = edd_get_download( $demo_id );
                $demo_titles[] = $demo_data->post_title;
            }

            edd_set_error( 'edd-demo-error', sprintf( __( 'Only one demo product of each type is allowed per email address. You have already bought following demo(s): %s', 'edd-paid-content' ), implode(', ', $demo_titles) ) );
        }
    }
}

add_action( 'edd_checkout_error_checks', 'bpmj_edd_validate_demo_purchase', 15, 2 );

function bpmj_eddcm_cancel_subscription() {
    if ( isset( $_GET['bpmj_eddcm_cancel_subscription'] ) && wp_verify_nonce( $_GET[ '_wpnonce' ], 'edd_payment_nonce' ) ) {

        $payment_id = absint( $_GET[ 'purchase_id' ] );

        edd_update_payment_status( $payment_id, 'revoked' );

        wp_redirect(wp_get_referer());
        exit;
    }
}

add_action( 'init', 'bpmj_eddcm_cancel_subscription' );
