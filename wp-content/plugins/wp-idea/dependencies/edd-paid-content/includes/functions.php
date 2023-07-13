<?php

use bpmj\wpidea\Caps;
use bpmj\wpidea\helpers\In_Memory_Cache_Static_Helper;

/**
 * @param string $type
 *
 * @return array
 */
function bpmj_eddpc_get_renewals( $type = null ) {
	$renewals = get_option( 'bmpj_eddpc_renewal', array() );
	$result   = array();
	foreach ( $renewals as $renewal_id => $renewal ) {
		if ( ! $type || isset( $renewal[ 'type' ] ) && $type === $renewal[ 'type' ] || ! isset( $renewal[ 'type' ] ) && 'renewal' === $type ) {
			$result[$renewal_id] = $renewal;
		}
	}

	return $result;
}

/**
 * Ustawianie co jaki czas ma być sprawdzany cron
 */
function bmpj_eddpc_cron_schedules( $schedules ) {
	$schedules[ 'bpmj_eddpc_1min' ] = array(
		'interval'	 => 60,
		'display'	 => __( '1 minute', 'edd-paid-content' )
	);
	return $schedules;
}

add_filter( 'cron_schedules', 'bmpj_eddpc_cron_schedules' );

/**
 * Funkcja odpowiadająca za wysłanie emaila z powiadomieniem
 * o wygasaniu dostępu do treści
 *
 * @param bool $user_id
 * @param bool $product_id
 * @param $renewal_id
 * @param $access_time
 */
function bpmj_eddpc_renewal_email( $user_id, $product_id, $renewal_id, $access_time ) {

	if ( $user_id && $product_id ) {

		// Podpisanie zmiennych
		$user       = get_userdata( $user_id );
		$product    = bpmj_eddpc_prepare_product_array_for_email( $user_id, $product_id );
		$renewal    = bpmj_eddpc_get_renewals('renewal');
		$renewal    = $renewal[ $renewal_id ];
		$expiration = bpmj_eddpc_date_i18n( 'd.m.Y - H:i:s', $access_time );

		$message = apply_filters( 'bpmj_eddpc_renewal_email_message_with_id', $renewal[ 'message' ], $user, $product_id, $renewal_id );
		$message = nl2br( apply_filters( 'bpmj_eddpc_renewal_email_message', $message, $user, $product_id ) );
		$message = str_replace( '{name}', $user->first_name . ' ' . $user->last_name, $message );

		$message = str_replace( '{product_main_name}', $product[ 'title_main' ], $message );
		$message = str_replace( '{product_name}', $product[ 'title' ], $message );
		$message = str_replace( '{product_link}', $product[ 'permalink' ], $message );
		$message = str_replace( '{offer_link}', $product[ 'offer' ], $message );
		$message = str_replace( '{expiration}', $expiration, $message );

		$subject = apply_filters( 'bpmj_eddpc_renewal_email_subject', $renewal[ 'subject' ], $user, $product_id, $renewal_id );

		do_action( 'bpmj_eddpc_renewal_email', $user_id, $subject, $message );
		if( !empty( $subject ) ) {
			EDD()->emails->send( $user->user_email, $subject, $message );
		}
	}
}

/**
 * @param int $user_id
 * @param int $product_id
 *
 * @return array
 */
function bpmj_eddpc_prepare_product_array_for_email( $user_id, $product_id ) {
	$product          = array(
		'title'      => get_the_title( $product_id ),
		'title_main' => get_the_title( $product_id ),
		'permalink'  => edd_get_checkout_uri( array(
			'edd_action'  => 'add_to_cart',
			'download_id' => $product_id,
		) ),
		'offer'      => get_permalink( $product_id ),
	);
	$product_variable = bpmj_eddpc_get_product_variable( $product_id, $user_id );
	if ( $product_variable ) {
		$product[ 'title' ]     = $product[ 'title' ] . ' - ' . $product_variable[ 'title' ];
		$product[ 'permalink' ] = $product_variable[ 'url' ];
	}

	return $product;
}

/**
 * @param int $user_id
 * @param int $product_id
 * @param EDD_Payment $nearest_payment
 * @param array $notice
 * @param string $payment_charge_mode
 */
function bpmj_eddpc_send_payment_notice_email( $user_id, $product_id, \EDD_Payment $nearest_payment, array $notice, $payment_charge_mode = 'automatic' ) {
	$user    = get_userdata( $user_id );
	$message = apply_filters( 'bpmj_eddpc_payment_notice_email', $notice[ 'message' ], $user, $product_id, $nearest_payment );
	$message = nl2br( $message );

	$title_main_parts = array();
	$title_parts      = array();

	foreach ( $nearest_payment->downloads as $download ) {
		$options     = $download[ 'options' ];
		$download_id = $download[ 'id' ];
		$quantity    = ! empty( $options[ 'quantity' ] ) ? $options[ 'quantity' ] : 1;
		$price_id    = ! empty( $options[ 'price_id' ] ) ? $options[ 'price_id' ] : null;
		$title_main  = get_the_title( $download_id );
		$title       = $title_main;
		if ( $price_id ) {
			$title .= ' ' . edd_get_price_option_name( $download_id, $price_id, $nearest_payment->ID );
		}
		if ( $quantity > 1 ) {
			$title_main .= ' × ' . $quantity;
			$title      .= ' × ' . $quantity;
		}
		$title_main_parts[] = $title_main;
		$title_parts[]      = $title;
	}

	$payment_key = edd_get_payment_key( $nearest_payment->ID );
	$message     = str_replace(
		array(
			'{name}',
			'{product_main_name}',
			'{product_name}',
			'{payment_date}',
			'{amount}',
			'{payment_link}',
			'{payment_method}',
			'{direct_payment_link}',
		),
		array(
			$user->first_name . ' ' . $user->last_name,
			implode( ', ', $title_main_parts ),
			implode( ', ', $title_parts ),
			get_the_date( '', $nearest_payment->ID ),
			$nearest_payment->total . ' ' . $nearest_payment->currency,
			add_query_arg( 'payment_key', $payment_key, edd_get_success_page_uri() ),
			edd_get_gateway_checkout_label( edd_get_payment_gateway( $nearest_payment->ID ) ),
			$payment_charge_mode === 'automatic' ? __( 'Payment will be made automatically', 'edd-paid-content' ) : add_query_arg( 'edd_payment_to_gateway', $payment_key, home_url( '/' ) ),
		),
		$message
	);

	$subject = apply_filters( 'bpmj_eddpc_notice_email_subject', $notice[ 'subject' ], $user, $product_id );
	if( !empty( $subject ) ) {
		EDD()->emails->send( $user->user_email, $subject, $message );
	}
}

/**
 * Generuje kod zniżkowy
 */
function bpmj_eddpc_renewal_discount( $message, $user, $product_id ) {

	global $edd_options;

	// Czy kupony zniżkowe są włączone
	if ( !isset( $edd_options[ 'bpmj_renewal_discount' ] ) && $edd_options[ 'bpmj_renewal_discount' ] != 'on' )
		return $message;

	// Czy jest podana jakolwiek wartość
	if ( !isset( $edd_options[ 'bpmj_renewal_discount_value' ] ) || empty( $edd_options[ 'bpmj_renewal_discount_value' ] ) )
		return $message;

	// Czy treść zawiera tag {discount}, {discount_main_link}, {discount_link} itp. ({discount...)
	if( strpos( $message, '{discount' ) === false )
		return $message;

	$discount_exist = false;

	// Czy został już wygenerowany kod zniżkowy i czy jest jeszcze ważny
	$access = get_user_meta( $user->ID, '_bpmj_eddpc_access', true );
	if ( isset( $access[ $product_id ][ 'discount' ] ) ) {
		$discount_expiration = get_post_meta( $access[ $product_id ][ 'discount' ], '_edd_discount_expiration', true );
		$discount_status	 = get_post_meta( $access[ $product_id ][ 'discount' ], '_edd_discount_status', true );
		$discount_uses		 = get_post_meta( $access[ $product_id ][ 'discount' ], '_edd_discount_uses', true );

		if ( $discount_uses == '0' ) {
			if ( time() < strtotime( $discount_expiration ) || empty( $discount_expiration ) ) {

				$discount_code	 = get_post_meta( $access[ $product_id ][ 'discount' ], '_edd_discount_code', true );
				$discount_exist	 = true;
			}
		}
	}


	if ( !$discount_exist ) {
		$name	 = sprintf( 'eddpc %s %s', $user->ID, bpmj_eddpc_date_i18n( 'Y-m-d H:i:s', time() ) );
		$args	 = array(
			'code'				 => wp_generate_password( 8, false, false ),
			'name'				 => $name,
			'status'			 => 'active',
			'uses'				 => 0,
			'max'				 => 1,
			'amount'			 => $edd_options[ 'bpmj_renewal_discount_value' ],
			'start'				 => date( 'Y-m-d' ),
			'type'				 => $edd_options[ 'bpmj_renewal_discount_type' ],
			'min_price'			 => 0,
			'products'			 => array( $product_id ),
			'product_condition'	 => 'any',
			'excluded_products'	 => array(),
			'not_global'		 => true,
			'use_once'			 => true,
		);

		if ( $edd_options[ 'bpmj_renewal_discount_time' ] != 'no-limit' ) {
			$args[ 'expiration' ] = date( 'Y-m-d', strtotime( date( 'Y-m-d' ) . $edd_options[ 'bpmj_renewal_discount_time' ] ) );
		}
		
		$args = apply_filters( 'bpmj_eddpc_renewal_discount_args', $args, $product_id, $user );

		$discount_id = edd_store_discount( $args );

		if ( empty( $discount_id ) )
			return $message;

		$discount_code = get_post_meta( $discount_id, '_edd_discount_code', true );

		$access[ $product_id ][ 'discount' ] = $discount_id;
		update_user_meta( $user->ID, '_bpmj_eddpc_access', $access );
	}

	$discount_url = edd_get_checkout_uri( array(
		'edd_action'	 => 'add_to_cart',
		'download_id'	 => $product_id,
		'discount'		 => $discount_code
	) );

	$discount_main_url = $discount_url;

	$product_variable = bpmj_eddpc_get_product_variable( $product_id, $user->ID, $discount_code );
	if ( $product_variable ) {
		$discount_url = $product_variable[ 'url' ];
	}

	$message = str_replace( '{discount}', $discount_code, $message );
	$message = str_replace( '{discount_main_link}', $discount_main_url, $message );
	$message = str_replace( '{discount_link}', $discount_url, $message );

	return $message;
}

add_filter( 'bpmj_eddpc_renewal_email_message', 'bpmj_eddpc_renewal_discount', 10, 3 );

/**
 * Sprawdzamy czy produkt ma warianty cenowe
 * Jeżeli tak wypuszczamy parametr do umieszczenia w url
 */
function bpmj_eddpc_get_product_variable( $download_id, $user_id, $discount_code = false ) {

	$result = false;
	// Sprawdzamy czy produkt ma warianty cenowe
	if ( edd_has_variable_prices( $download_id ) ) {
		// Jeżeli tak to pobieramy ostatnie zamówienie użytkownika z danym produktem
		$customer	 = new EDD_Customer( $user_id, true );
		$payment_ids = explode( ',', $customer->payment_ids );
		$payment_ids = array_reverse( $payment_ids );

		foreach ( $payment_ids as $payment_id ) {
			$downloads = edd_get_payment_meta_cart_details( $payment_id, true );
			foreach ( $downloads as $download ) {
				if ( $download[ 'id' ] == $download_id ) {
					$price_id = $download[ 'item_number' ][ 'options' ][ 'price_id' ];

					$result = array(
						'price_id'	 => $price_id,
						'title'		 => edd_get_price_option_name( $download_id, $price_id )
					);

					if ( $discount_code ) {

						$result[ 'url' ] = edd_get_checkout_uri( array(
							'edd_action'			 => 'add_to_cart',
							'download_id'			 => $download_id,
							'edd_options[price_id]'	 => $price_id,
							'discount'				 => $discount_code
						) );
					} else {

						$result[ 'url' ] = edd_get_checkout_uri( array(
							'edd_action'			 => 'add_to_cart',
							'download_id'			 => $download_id,
							'edd_options[price_id]'	 => $price_id
						) );
					}
					break;
				}
			}
		}
	}

	return $result;
}

/*
 * Formatuje parametr id podany w shortcode
 * $param_id - wartość atrybutu id dla shortcode przed sformatowaniem
 * $post_id - ID aktualnego postu, na którym wyświetlany jest shortcode
 */

function bpmj_eddpc_format_shortcode_id_param( $param_id, $post_id = '' ) {

	$elements = explode( ',', $param_id );

	$output = array();

	foreach ( $elements as $element ) {
		if ( strpos( $element, ':' ) ) {
			$element = explode( ':', $element );

			// Gdy został podany wariant, sprawdź, czy ID jest numerem
			if ( isset( $element[ 0 ] ) && is_numeric( $element[ 0 ] ) ) {
				//$output[] = $element;
			}

			// Gdy został podany hash i wariant, zastąp hash wartością ID postu
			if ( isset( $element[ 0 ] ) && $element[ 0 ] == '#' ) {
				$element[ 0 ]	 = $post_id;
				$output[]		 = $element;
			}
		}

		// Gdy jest numerem, dodaj do tablicy wynikowej
		if ( is_numeric( $element ) ) {
			$output[] = $element;
		}

		// Gdy został podany sam hash, zastąp go wartością ID postu
		if ( $element == '#' ) {
			$output[] = $post_id;
		}
	}

	return $output;
}

/*
 * Formatuje parametr time w shortcode
 */

function bpmj_eddpc_format_shortcode_time_param( $time ) {

	$elements = explode( ',', $time );

	// Utworzenie tablicy z czasem
	$times = array(
		's'	 => 0,
		'm'	 => 0,
		'h'	 => 0,
		'd'	 => 0
	);

	foreach ( $elements as $element ) {

		// Sekundy
		if ( strpos( $element, 's' ) !== FALSE ) {

			$sec = trim( str_replace( 's', '', $element ) );
			if ( is_numeric( $sec ) ) {
				if ( $sec > 60 ) {
					$times[ 's' ] = 60;
				} else {
					$times[ 's' ] = (int) $sec;
				}
			}
		}

		// Minuty
		if ( strpos( $element, 'm' ) !== FALSE ) {
			$min = trim( str_replace( 'm', '', $element ) );
			if ( is_numeric( $min ) ) {
				if ( $min > 60 ) {
					$times[ 'm' ] = 60;
				} else {
					$times[ 'm' ] = (int) $min;
				}
			}
		}

		// Godziny
		if ( strpos( $element, 'h' ) !== FALSE ) {
			$hours = trim( str_replace( 'h', '', $element ) );
			if ( is_numeric( $hours ) ) {
				if ( $hours > 24 ) {
					$times[ 'h' ] = 24;
				} else {
					$times[ 'h' ] = (int) $hours;
				}
			}
		}

		// Dni
		if ( strpos( $element, 'd' ) !== FALSE ) {
			$days			 = trim( str_replace( 'd', '', $element ) );
			if ( is_numeric( $days ) )
				$times[ 'd' ]	 = (int) $days;
		}
	}

	// Suma wszytskich czasów podana w sekundach
	$total_sec = $times[ 's' ] + ( $times[ 'm' ] * 60) + ($times[ 'h' ] * 60 * 60) + ( $times[ 'd' ] * 24 * 60 * 60 );

	return $total_sec;
}

/*
 * Zwraca id wariantu cenowego po podaniu jego ceny
 */

function bpmj_eddpc_convert_variant_name_to_variant_id( $product_id, $variant_name ) {

	// Pobranie wszytskich wariantów cenowych przypisanych do produktu
	$edd_variants = edd_get_variable_prices( $product_id );

	foreach ( $edd_variants as $id => $variant ) {


		if ( $variant[ 'name' ] == $variant_name ) {
			return (int) $id;
		}
	}

	return false;
}

/*
 * Skrypt jQuery, kontrolujący czas opublikowania treści z shortcode.
 *  Po upłynięciu czasu wymusza przeładowanie strony
 */

function bpmj_eddpc_control_time_js_script() {

	$output = '<script>';
	?><script>
		jQuery( document ).ready( function () {

			setInterval( function () {

				var date = new Date();

				var current_time = Math.round( date.getTime() / 1000 ) - 5;

				//  var utc = date.getTimezoneOffset() * 60;

				//   var new_date = current_time + utc;

				var time_out = jQuery( '#bpmj_eddpc_content' ).attr( 'data-timecontrol' );


				console.log( time_out + ' - ' + current_time );

				if ( current_time > time_out ) {
					location.reload();
				}


			}, 3000 );

		} );
	</script>
	<?php
	$output .= '</script>';
	?>


	<?php
	// return $output;
}

/*
 * Tryb debugowania. Wyświetla informację szczegółowe o wszytskich czasach w EDD PAID CONTENT
 *
 * @params:
 * 1. $output - orygianla treść zwrócona bez trybu debugowania
 * 2. $purchase_date - data złożenia zamówienia
 * 3. $shortcode_time - czas określony w shortcode timestamp
 */

function bpmj_eddpc_debug_times( $output, $purchase_date = '', $shortcode_time = '', $current_time = '' ) {

	// Data w timestamp w po upłynięciu której treść ma być znów ukryta
	$time_out = $purchase_date + $shortcode_time;

	$return = '<pre><ol>';
	if ( !isset( $purchase_date ) || empty( $purchase_date ) ) {
		$return .= '<li><b>Error</b> Failed to get product purchase date.</li>';
	} else {
		$return .= '<li>Date of product purchase that unlocks content:<br /> <b>' . date( 'd.m.Y, \g\o\d\z: G:i \s\. s', $purchase_date ) . '</b>,<br /> Timestamp: <b> ( ' . $purchase_date . ' )</b></li>';
	}
	if ( !isset( $time_out ) || empty( $time_out ) ) {
		$return .= '<li><b>Error</b> Could not calculate product expiration date</li>';
	} else {
		$return .= '<li>Content Expiration Date:<br /> <b>' . date( 'd.m.Y, \g\o\d\z: G:i \s\. s', $time_out ) . '</b>,<br /> Timestamp: <b> ( ' . $time_out . ' )</b></li>';
	}

	$return .= '<li>Current time:<br /> <b>' . date( 'd.m.Y, \g\o\d\z: G:i \s\. s', $current_time ) . '</b>,<br /> Timestamp: <b> ( ' . $current_time . ' )</b></li>';
	$return .= '</ol></pre><br />';

	return $return . $output;
}

/**
 * Aktualizacja łącznego czasu dostępu
 */
function bpmj_eddpc_user_update_total_time( $user_id, $download_id ) {

	global $bpmj_eddpc_tnow;

	$user_access_time = bpmj_eddpc_get_access_and_remove_flag( $user_id );

	if ( empty( $user_access_time[ $download_id ] ) )
		return [];

	$access_time = ( isset( $user_access_time[ $download_id ][ 'access_time' ] ) ) ? $user_access_time[ $download_id ][ 'access_time' ] : null;
	$last_time	 = ( isset( $user_access_time[ $download_id ][ 'last_time' ] ) ) ? $user_access_time[ $download_id ][ 'last_time' ] : $bpmj_eddpc_tnow;
	$total_time	 = ( isset( $user_access_time[ $download_id ][ 'total_time' ] ) ) ? $user_access_time[ $download_id ][ 'total_time' ] : 0;

	if ( empty( $access_time ) || $access_time >= $bpmj_eddpc_tnow ) { // jeśli jest dostęp
		if ( !empty( $last_time ) )
			$total_time += ($bpmj_eddpc_tnow - $last_time);
		$user_access_time[ $download_id ][ 'total_time' ]	 = $total_time;
		$user_access_time[ $download_id ][ 'last_time' ]	 = $bpmj_eddpc_tnow;

		bpmj_eddpc_set_access_if_flag_is_not_set( $user_id, $user_access_time );
	} else if ( !empty( $last_time ) ) { // jeśli nie ma dostępu i nie zostało wyliczone przesunięcie
		if ( $last_time > $access_time )
			$last_time = $access_time;

		$user_access_time[ $download_id ][ 'total_time' ]	 = $total_time + ($access_time - $last_time);
		$user_access_time[ $download_id ][ 'last_time' ]	 = null;

		bpmj_eddpc_set_access_if_flag_is_not_set( $user_id, $user_access_time );
	}

    return $user_access_time;
}

/**
 * Zwraca do kiedy user ma dostęp do strony (null - bez limitu)
 * Zwraca false, gdy nie ma dostępu
 *
 * @param int $user_id
 * @param int $download_id
 * @param int|bool $post_id
 * @param int|null $price_id
 *
 * @return array
 */
function bpmj_eddpc_get_user_valid_access( $user_id, $download_id, $post_id = false, $price_id = null ) {

	global $bpmj_eddpc_tnow;

    $user_access_time = bpmj_eddpc_cached_bpmj_eddpc_user_update_total_time( $user_id, $download_id );

	$access_time = ( isset( $user_access_time[ $download_id ][ 'access_time' ] ) ) ? $user_access_time[ $download_id ][ 'access_time' ] : null; // j.w. (!isset - odblokuj wszsytko)
	$last_time	 = ( isset( $user_access_time[ $download_id ][ 'last_time' ] ) ) ? $user_access_time[ $download_id ][ 'last_time' ] : $bpmj_eddpc_tnow;
	$total_time	 = ( isset( $user_access_time[ $download_id ][ 'total_time' ] ) ) ? $user_access_time[ $download_id ][ 'total_time' ] : 0;

	$status = 'expired';
	if ( empty( $user_access_time[ $download_id ] ) ) {
		$status = 'locked';
	} else if ( empty( $access_time ) || $access_time >= $bpmj_eddpc_tnow ) {
		if ( !$post_id ) {
			$status = 'valid';
		} else if ( $price_id && ! empty( $user_access_time[ $download_id ][ 'price_id' ] ) && ! in_array( $price_id, $user_access_time[ $download_id ][ 'price_id' ] ) ) {
			$status = 'locked';
		} else {
			$drip_value = get_post_meta( $post_id, '_bpmj_eddpc_drip_value', true );
			$drip_unit  = get_post_meta( $post_id, '_bpmj_eddpc_drip_unit', true );

			//if post has no access time set but has parent, use the parent drip value
			if( empty($drip_value) ){
				$ancestors   = get_post_ancestors( $post_id );
				$parent      = $ancestors ? $ancestors[ 0 ] : null;
				
				if( $parent ){
					$drip_value = get_post_meta( $parent, '_bpmj_eddpc_drip_value', true );
					$drip_unit  = get_post_meta( $parent, '_bpmj_eddpc_drip_unit', true );
				}
			}

			$drip = bpmj_eddpc_calculate_access_time( 0, $drip_value, $drip_unit );

			if ( empty( $drip ) ) {
				$drip = 0;
			}

			if ( $total_time >= $drip ) {
				$status = 'valid';
			} else {
				$status = 'waiting';
			}
		}
	}

	return array(
		'status'      => $status,
		'access_time' => $access_time,
		'last_time'   => $last_time,
		'total_time'  => $total_time,
		'drip'        => isset( $drip ) ? $drip : 0,
	);
}

/**
 * Funckja pomocnicza do bpmj_eddpc_user_can_access
 *
 * @param int $user_id
 * @param array $data
 * @param array $params
 * @param int|bool $post_id
 * @param int|null $price_id
 *
 * @return mixed
 */
function bpmj_eddpc_user_can_access_check( $user_id, $data, $params, $post_id = false, $price_id = null ) {

	$update = false;

	$access = bpmj_eddpc_get_user_valid_access( $user_id, $data[ 'download' ], $post_id, $price_id );
	// aktualizuj, gdy wcześniej było locked
	if ( 'locked' == $params[ 'status' ] ) {
		$update = true;
	}

	// aktualizuj, gdy status taki sam
	if ( $access[ 'status' ] == $params[ 'status' ] ) {
		$update = true;
	}

	// aktualizuj, gdy jest dostęp
	if ( 'valid' == $access[ 'status' ] ) {
		$update = true;
	}

	// aktualizuj, gdy jest przejście z expired na waiting
	if ( 'expired' == $params[ 'status' ] && 'waiting' == $access[ 'status' ] ) {
		$update = true;
	}

	if ( $update ) {

		$params[ 'status' ] = $access[ 'status' ];
		if ( 'expired' != $access[ 'status' ] && ( $access[ 'total_time' ] > $params[ 'total_time' ] ) ) {
			$params[ 'total_time' ]	 = $access[ 'total_time' ];
			$params[ 'last_time' ]	 = $access[ 'last_time' ];
		}
		if ( is_null( $access[ 'access_time' ] ) || $access[ 'access_time' ] > $params[ 'access_time' ] ) {
			$params[ 'access_time' ] = $access[ 'access_time' ];
		}
		if ( $access[ 'drip' ] < $params[ 'drip' ] && 'locked' !== $access[ 'status' ] ) {
			$params[ 'drip' ] = $access[ 'drip' ];
		}
	}
	return $params;
}

/**
 * Sprawdzenie, czy user ma dostęp do strony
 *
 * @param bool|int $user_id
 * @param array $restricted_to
 * @param bool|int $post_id
 * @param bool $dont_check_product_purchase
 *
 * @return mixed
 */
function bpmj_eddpc_user_can_access( $user_id, $restricted_to, $post_id = false, $dont_check_product_purchase = false ) {

	global $bpmj_eddpc_tnow;

	$params				 = array(
		'status'		 => 'locked',
		'access_time'	 => 0,
		'last_time'		 => 0,
		'total_time'	 => ~PHP_INT_MAX,
		'drip'			 => PHP_INT_MAX
	);
	$return = array();
	$restricted_count	 = is_countable($restricted_to) ? count( $restricted_to ) : 0;
	$products			 = array();

	// If no user is given, use the current user
	if ( !$user_id ) {
		$user_id = get_current_user_id();
	}

	// Admins have full access
	if ( bpmj_eddpc_cached_user_can( $user_id, Caps::CAP_MANAGE_OPTIONS ) || bpmj_eddpc_cached_user_can( $user_id, Caps::CAP_MANAGE_PRODUCTS) ) {
		$params[ 'status' ] = 'valid';
	}

	// The post author can always access
	if ( $post_id && user_can( $user_id, 'edit_post', $post_id ) ) {
		$params[ 'status' ] = 'valid';
	}

    $is_user_logged_in = is_user_logged_in();
    $edd_has_purchases_user_id = bpmj_eddpc_cached_edd_has_purchases( $user_id );

	if ( $restricted_to && ( 'valid' != $params[ 'status' ] ) ) {

		foreach ( $restricted_to as $item => $data ) {

			if ( empty( $data[ 'download' ] ) ) {
				$params[ 'status' ] = 'valid';
			}

			// The author of a download always has access
			if ( (int) bpmj_eddpc_cached_get_post_field( 'post_author', $data[ 'download' ] ) === (int) $user_id && $is_user_logged_in ) {
				$params[ 'status' ] = 'valid';
				break;
			}

			// If restricted to any customer and user has purchased something
			if ( 'any' === $data[ 'download' ] && $edd_has_purchases_user_id && $is_user_logged_in ) {
				$params[ 'status' ] = 'valid';
				break;
			} elseif ( 'any' === $data[ 'download' ] ) {
				$products[ 0 ]		 = __( 'any product', 'edd-paid-content' );
				$params[ 'status' ]	 = 'locked';
				break;
			}

			// Check for variable prices
			//if( ! $has_access ) {
			if ( bpmj_eddpc_cached_edd_has_variable_prices( $data[ 'download' ] ) ) {
				if ( ! empty( $data[ 'price_id' ] ) && strtolower( $data[ 'price_id' ] ) !== 'all' ) {
					$products[] = '<a href="' . bpmj_eddpc_cached_get_permalink( $data[ 'download' ] ) . '">' . bpmj_eddpc_cached_get_the_title( $data[ 'download' ] ) . ' - ' . bpmj_eddpc_cached_edd_get_price_option_name( $data[ 'download' ], $data[ 'price_id' ] ) . '</a>';
					$params     = bpmj_eddpc_user_can_access_check( $user_id, $data, $params, $post_id, $data[ 'price_id' ] );
				} else {
					$products[] = '<a href="' . bpmj_eddpc_cached_get_permalink( $data[ 'download' ] ) . '">' . bpmj_eddpc_cached_get_the_title( $data[ 'download' ] ) . '</a>';
					$params     = bpmj_eddpc_user_can_access_check( $user_id, $data, $params, $post_id );
				}
			} else {
				$products[] = '<a href="' . bpmj_eddpc_cached_get_permalink( $data[ 'download' ] ) . '">' . bpmj_eddpc_cached_get_the_title( $data[ 'download' ] ) . '</a>';
				$params = bpmj_eddpc_user_can_access_check( $user_id, $data, $params, $post_id );
			}
			//}
		}

		if ( 'valid' != $params[ 'status' ] ) {

			if ( bpmj_eddpc_has_redirect( $post_id ) )
				$message = bpmj_eddpc_get_redirect_script( $post_id );
			else if ( 'expired' == $params[ 'status' ] ) {
				$message = __( 'The content you are trying to view is not available. Your access has expired:', 'edd-paid-content' );
				$message .= ' ' . bpmj_eddpc_date_i18n( get_option( 'time_format' ), $params[ 'access_time' ], true ) . ' ' . bpmj_eddpc_date_i18n( get_option( 'date_format' ), $params[ 'access_time' ] ) . '.';
			} else if ( 'waiting' == $params[ 'status' ] ) {
				$message	 = __( 'The content you are trying to view is not yet available. Wait for:', 'edd-paid-content' );
				$drip_time	 = $bpmj_eddpc_tnow - $params[ 'total_time' ] + $params[ 'drip' ];
				$message .= ' ' . bpmj_eddpc_date_i18n( get_option( 'time_format' ), $drip_time, true ) . ' ' . bpmj_eddpc_date_i18n( get_option( 'date_format' ), $drip_time ) . '.';
			} else if ( $restricted_count > 1 ) {
				$message = __( 'The content you are trying to view is not available. Access for buyers only:', 'edd-paid-content' );
				if ( !empty( $products ) ) {
					$message .= '<ul>';
					foreach ( $products as $id => $product ) {
						$message .= '<li>' . $product . '</li>';
					}
					$message .= '</ul>';
				}
			} else {
				$message = sprintf(
				__( 'The content you are trying to view is not available. Access for buyers only: %s.', 'edd-paid-content' ), $products[ 0 ]
				);
			}
		}
		if ( isset( $message ) ) {
			$return[ 'message' ] = $message;
		} else {
			$return[ 'message' ] = __( 'The content you are trying to view is not available.', '' );
		}
	} else {
		// Just in case we're checking something unrestricted...
		$params[ 'status' ] = 'valid';
	}

	// Allow plugins to modify the restriction requirements
	$params[ 'status' ]	 = apply_filters( 'bpmj_eddpc_user_can_access', $params[ 'status' ], $user_id, $restricted_to );
	$return = array_merge( $return, $params );
	return $return;
}

function bpmj_eddpc_cached_get_permalink(int $product_id): string
{
    $key = 'get_permalink_' . $product_id;

    return In_Memory_Cache_Static_Helper::get_or_set_if_not_exists($key, fn() => get_permalink( $product_id ));
}

function bpmj_eddpc_cached_get_the_title(int $product_id): string
{
    $key = 'get_the_title_' . $product_id;
    return In_Memory_Cache_Static_Helper::get_or_set_if_not_exists($key, fn() => get_the_title( $product_id ));
}

function bpmj_eddpc_cached_edd_get_price_option_name(int $product_id, int $price_id): string
{
    $key = 'edd_get_price_option_name_' . $product_id . '_' . $price_id;
    return In_Memory_Cache_Static_Helper::get_or_set_if_not_exists($key, fn() => edd_get_price_option_name( $product_id, $price_id ));
}

function bpmj_eddpc_cached_edd_has_variable_prices(int $product_id): bool
{
    $key = 'edd_has_variable_prices_' . $product_id;
    return In_Memory_Cache_Static_Helper::get_or_set_if_not_exists($key, fn() => edd_has_variable_prices( $product_id ));
}

function bpmj_eddpc_cached_edd_has_purchases(int $user_id): bool
{
    $key = 'edd_has_purchases_' . $user_id;
    return In_Memory_Cache_Static_Helper::get_or_set_if_not_exists($key, fn() => edd_has_purchases( $user_id ));
}

function bpmj_eddpc_cached_user_can(int $user_id, string $cap): bool
{
    $key = 'user_can_' . $user_id . '_' . $cap;
    return In_Memory_Cache_Static_Helper::get_or_set_if_not_exists($key, fn() => user_can($user_id, $cap));
}

function bpmj_eddpc_cached_get_post_field( string $field, int $post_id )
{
    $key = 'get_post_field_' . $field . '_' . $post_id;
    return In_Memory_Cache_Static_Helper::get_or_set_if_not_exists($key, fn() => get_post_field( $field, $post_id ));
}

function bpmj_eddpc_cached_get_post_status(int $post_id)
{
    $key = 'get_post_status_' . $post_id;
    return In_Memory_Cache_Static_Helper::get_or_set_if_not_exists($key, fn() => get_post_status( $post_id ));
}

function bpmj_eddpc_cached_get_post_meta(int $post_id, string $meta)
{
    $key = 'get_post_meta_' . $post_id . '_' . $meta;
    return In_Memory_Cache_Static_Helper::get_or_set_if_not_exists($key, fn() => get_post_meta( $post_id, $meta, true ));
}

function bpmj_eddpc_cached_bpmj_eddpc_user_update_total_time( int $user_id, int $product_id ): array
{
    $key = 'bpmj_eddpc_user_update_total_time_' . $user_id . '_' . $product_id;
    return In_Memory_Cache_Static_Helper::get_or_set_if_not_exists($key, fn() => bpmj_eddpc_user_update_total_time($user_id, $product_id));
}


/**
 * Filtr treści (blokuje, gdy nie ma dostępu)
 */
function bpmj_eddpc_filter_content( $content ) {
	global $post;
	// If $post isn't an object, we aren't handling it!
	if ( !is_object( $post ) ) {
		return $content;
	}
	$restricted = bpmj_eddpc_is_restricted( $post->ID );
	if ( $restricted ) {
		$content = bpmj_eddpc_filter_restricted_content( $content, $restricted, null, $post->ID );
		$content = bpmj_eddpc_encrypt_all_wpupload_anchors( $content, $post->ID );
	}
	return $content;
}

add_filter( 'the_content', 'bpmj_eddpc_filter_content' );

/**
 * Funkcja filtrująca zawartość
 */
function bpmj_eddpc_filter_restricted_content( $content = '', $restricted = false, $message = null, $post_id = 0,
											   $class = '' ) {
	global $user_ID;
	// If the current user can edit this post, it can't be restricted!
	if ( !current_user_can( 'edit_post', $post_id ) && $restricted ) {
		$has_access = bpmj_eddpc_user_can_access( $user_ID, $restricted, $post_id );
		if ( 'valid' != $has_access[ 'status' ] ) {
			if ( !empty( $message ) ) {
				$has_access[ 'message' ] = $message;
			}
			
			if( class_exists('bpmj\wpidea\Info_Message') ){
				$message = new bpmj\wpidea\Info_Message( $has_access[ 'message' ] );
				$content = $message->get();
			} else {
				$content = '<div class="bpmj_eddpc_message ' . $class . '">' . $has_access[ 'message' ] . '</div>';
			}
		}
	}
	return do_shortcode( $content );
}

/**
 * Sprawdza, czy strona / post jest zablokowana
 */
function bpmj_eddpc_is_restricted( $post_id ) {
	$restricted = get_post_meta( $post_id, '_bpmj_eddpc_restricted_to', true );
	return $restricted;
}

/**
 * Sprawdza czy post / page ma ustawiony redirect
 */
function bpmj_eddpc_has_redirect( $post_id ) {
	$redirect = get_post_meta( $post_id, '_bpmj_eddpc_redirect_page', true );
	if ( $redirect && 'none' !== $redirect )
		return true;

	$redirect = get_post_meta( $post_id, '_bpmj_eddpc_redirect_url', true );
	if ( !empty( $redirect ) )
		return true;

	return false;
}

/**
 * Zwraca skrypt przekierowujący (redirect)
 */
function bpmj_eddpc_get_redirect_script( $post_id ) {
	$redirect = get_post_meta( $post_id, '_bpmj_eddpc_redirect_page', true );
	if ( isset( $redirect ) && !empty( $redirect ) && is_numeric( $redirect ) ) {
		$url = get_permalink( $redirect );
		return '<script>window.location.href = "' . $url . '";</script>';
	}

	$redirect = get_post_meta( $post_id, '_bpmj_eddpc_redirect_url', true );
	if ( !empty( $redirect ) )
		return '<script>window.location.href = "' . $redirect . '";</script>';

	return false;
}

/**
 * Zwraca URL przekierowujący (redirect)
 */
function bpmj_eddpc_get_redirect_url( $post_id ) {
	$redirect = get_post_meta( $post_id, '_bpmj_eddpc_redirect_page', true );
	if ( isset( $redirect ) && !empty( $redirect ) && is_numeric( $redirect ) ) {
		return get_permalink( $redirect );
	}

	$redirect = get_post_meta( $post_id, '_bpmj_eddpc_redirect_url', true );
	if ( !empty( $redirect ) )
		return $redirect;

	return false;
}

/**
 * Pobiera ilość czasu przypisaną do danego produktu
 *
 * @since  1.3
 * @return int
 */
function bpmj_eddpc_get_access_time_single( $post_id ) {
	global $post;

	$access_time = get_post_meta( $post_id, '_bpmj_eddpc_access_time', true );

	if ( $access_time )
		return $access_time;

	return 0;
}

function bpmj_eddpc_get_access_time_unit_single( $post_id ) {
	global $post;

	$access_time_unit = get_post_meta( $post_id, '_bpmj_eddpc_access_time_unit', true );

	if ( $access_time_unit )
		return $access_time_unit;

	return 'days';
}

function bpmj_eddpc_get_access_time_variable( $download_id = 0, $price_id = null ) {

	$prices = edd_get_variable_prices( $download_id );

	if ( isset( $prices[ $price_id ][ 'access_time' ] ) )
		return absint( $prices[ $price_id ][ 'access_time' ] );

	return 0;
}

function bpmj_eddpc_get_access_time_unit_variable( $download_id = 0, $price_id = null ) {

	$prices = edd_get_variable_prices( $download_id );

	if ( isset( $prices[ $price_id ][ 'access_time_unit' ] ) )
		return $prices[ $price_id ][ 'access_time_unit' ];

	return 'days';
}

function bpmj_eddpc_calculate_access_time( $current_value, $access_time, $unit, $access_start = '' ) {

	global $bpmj_eddpc_tnow;

	if ( empty( $access_time ) )
		return null;

	if ( !empty( $current_value ) && ( $current_value < $bpmj_eddpc_tnow ) ) { // dostęp przeterminowany - licz od teraz (chyba, że jest 0 - wylicz timespan)
		$current_value = $bpmj_eddpc_tnow;
	}

	if ( !empty( $access_start ) && ( $access_start > $bpmj_eddpc_tnow ) ) { // moment startu
		$current_value = $access_start;
	}

	switch ( $unit ) {

		case 'minutes':
			$new_value = $current_value + ( $access_time * 60 );
			break;

		case 'hours':
			$new_value = $current_value + ( $access_time * 60 * 60 );
			break;

		case 'days':
			$new_value = $current_value + ( $access_time * 24 * 60 * 60 );
			break;

		case 'months':
			$date = new DateTime();
			$date->setTimestamp( $current_value );

			if ( 1 == $access_time ) {
				$date->modify( '+1 month' );
				$new_value = $date->getTimestamp();
			} else {
				$date->modify( '+' . $access_time . ' months' );
				$new_value = $date->getTimestamp();
			}
			break;

		case 'years':
			$date = new DateTime();
			$date->setTimestamp( $current_value );

			if ( 1 == $access_time ) {
				$date->modify( '+1 year' );
				$new_value = $date->getTimestamp();
			} else {
				$date->modify( '+' . $access_time . ' years' );
				$new_value = $date->getTimestamp();
			}
			break;

		default:
			$new_value = $current_value;
			break;
	}

	return $new_value;
}

function bpmj_eddpc_get_access_time( $attr ) {

	$params = array(
		'has_access'	 => false,
		'access_time'	 => 0,
		'last_time'		 => 0,
		'total_time'	 => ~PHP_INT_MAX,
		'drip'			 => PHP_INT_MAX
	);

	if ( !empty( $attr[ 'download' ] ) )
		$restricted_to	 = array( array( 'download' => $attr[ 'download' ] ) );
	else
		$restricted_to	 = bpmj_eddpc_is_restricted( $attr[ 'id' ] );

	if ( empty( $restricted_to ) )
		return $attr[ 'empty' ];

	$user_id	 = get_current_user_id();
	$access_time = 0;

	foreach ( $restricted_to as $item => $data ) {

		if ( empty( $data[ 'download' ] ) ) {
			return $attr[ 'nolimit' ];
		}

		// If restricted to any customer and user has purchased something
		if ( 'any' === $data[ 'download' ] && edd_has_purchases( $user_id ) && is_user_logged_in() ) {
			return $attr[ 'nolimit' ];
		} elseif ( 'any' === $data[ 'download' ] ) {
			return $attr[ 'noaccess' ]; // brak dostępu
		}

		// Check for variable prices
		if ( edd_has_variable_prices( $data[ 'download' ] ) ) {
			if ( strtolower( $data[ 'price_id' ] ) !== 'all' && !empty( $data[ 'price_id' ] ) ) {
				//$products[] = '<a href="' . get_permalink( $data['download'] ) . '">' . get_the_title( $data['download'] ) . ' - ' . edd_get_price_option_name( $data['download'], $data['price_id'] ) . '</a>';
				$params = bpmj_eddpc_user_can_access_check( $user_id, $data, $params, $data[ 'price_id' ] );
			} else {
				//$products[] = '<a href="' . get_permalink( $data['download'] ) . '">' . get_the_title( $data['download'] ) . '</a>';
				$params = bpmj_eddpc_user_can_access_check( $user_id, $data, $params );
			}
		} else {
			//$products[] = '<a href="' . get_permalink( $data['download'] ) . '">' . get_the_title( $data['download'] ) . '</a>';
			$params = bpmj_eddpc_user_can_access_check( $user_id, $data, $params );
		}

		if ( is_null( $params[ 'access_time' ] ) )
			return $attr[ 'nolimit' ];
	}

	if ( 0 === $params[ 'access_time' ] )
		return $attr[ 'noaccess' ];

	return sprintf( $attr[ 'accessto' ], bpmj_eddpc_date_i18n( get_option( 'time_format' ), $params[ 'access_time' ] ), bpmj_eddpc_date_i18n( get_option( 'date_format' ), $params[ 'access_time' ] ) );
}

/**
 * Koryguje podany timestamp o przesunięcie strefy czasowej
 *
 * @param int $unix_timestamp
 * @param bool $add
 *
 * @return int mixed
 */
function bpmj_eddpc_adjust_timestamp( $unix_timestamp, $add = true ) {
	$correction = ( get_option( 'gmt_offset' ) * 3600 );
	if( !$add ) $correction = -$correction;
	return $unix_timestamp + $correction;
}

const MAX_TIMESTAMP = 31557600000;

/**
 * date_i18n z poprawką (prawidłowe wyświetlanie czasu lokalnego z timestampa)
 *
 * @param $date_format_string
 * @param int $unix_timestamp
 * @param bool $gmt
 *
 * @return string
 */
function bpmj_eddpc_date_i18n( $date_format_string, $unix_timestamp, $gmt = false ) {
	if($unix_timestamp > MAX_TIMESTAMP) {
		$unix_timestamp = MAX_TIMESTAMP;
	}
	return date_i18n( $date_format_string, bpmj_eddpc_adjust_timestamp( $unix_timestamp ), $gmt );
}

/**
 * Get all bundle product IDs purchased by the user
 *
 * @param int $user
 * @param string $status
 *
 * @return bool|object
 */
function bpmj_eddpc_get_users_purchased_bundles( $user = 0, $status = 'complete' ) {
	// temporarily modify default query args
	$temporary_args_filter = function ( $args ) {
		$args[ 'meta_key' ]		 = '_edd_product_type';
		$args[ 'meta_value' ]	 = 'bundle';

		return $args;
	};
	add_filter( 'edd_get_users_purchased_products_args', $temporary_args_filter, 99 );
	$products = edd_get_users_purchased_products( $user, $status );
	remove_filter( 'edd_get_users_purchased_products_args', $temporary_args_filter, 99 );

	return $products;
}

/**
 * Get all product IDs that the user has purchased in bundles
 *
 * @param int $user
 * @param string $status
 *
 * @return array
 */
function bpmj_eddpc_get_users_purchased_bundled_products( $user = 0, $status = 'complete' ) {
	$bundles				 = bpmj_eddpc_get_users_purchased_bundles( $user, $status );
	$bundled_products_raw	 = array();
	if ( !empty( $bundles ) && is_array( $bundles ) ) {
		/* @var WP_Post $bundle */
		foreach ( $bundles as $bundle ) {
			$bundled_products_raw = array_merge( $bundled_products_raw, edd_get_bundled_products( $bundle->ID ) );
		}
	}

	$bundled_products = array_unique( $bundled_products_raw );

	return $bundled_products;
}

/**
 * Check if the user has purchased the specified product in a bundle
 *
 * @param int $user_id
 * @param array|int $downloads
 *
 * @return bool
 */
function bpmj_eddpc_has_user_purchased_in_bundle( $user_id, $downloads ) {
	static $purchased_bundled_products;
	if ( !isset( $purchased_bundled_products ) ) {
		// There is no need to run this query more than once
		$purchased_bundled_products = bpmj_eddpc_get_users_purchased_bundled_products( $user_id );
	}
	if ( !is_array( $downloads ) ) {
		$downloads = (array) $downloads;
	}
	$intersection = array_intersect( $downloads, $purchased_bundled_products );
	if ( count( $intersection ) > 0 ) {
		return true;
	}

	return false;
}

/**
 * Check if the user has purchased the specified product (either particular or in a bundle) - wrapper replacement
 * for edd_has_user_purchased
 *
 * @param int $user_id
 * @param array|int $downloads
 * @param int $variable_price_id
 *
 * @return bool
 */
function bpmj_eddpc_has_user_purchased_single_or_in_bundle( $user_id, $downloads, $variable_price_id = null ) {
	if ( edd_has_user_purchased( $user_id, $downloads, $variable_price_id ) ) {
		return true;
	} else {
		return bpmj_eddpc_has_user_purchased_in_bundle( $user_id, $downloads );
	}
}

/**
 * @return bool
 */
function bpmj_eddpc_recurring_payments_possible() {
	if ( function_exists( 'edd_any_enabled_gateway_supports_recurring_payments' ) ) {
		return edd_any_enabled_gateway_supports_recurring_payments();
	}

	return false;
}

/**
 * @param int $user_id
 * @param int $product_id
 *
 * @return bool
 */
function bpmj_eddpc_maybe_send_payment_notice( $user_id, $product_id ) {
	$nearest_payment = edd_user_get_nearest_pending_recurring_payment( $user_id, $product_id );
	if ( ! $nearest_payment ) {
		return false;
	}
	$payment_notices      = bpmj_eddpc_get_renewals( 'payment' );
	$user_payment_notices = get_user_meta( $user_id, "_bpmj_eddpc_payment_notices", true );
	if ( ! $user_payment_notices ) {
		$user_payment_notices = array();
	}
	$today = date( 'Y-m-d' );
	foreach ( $payment_notices as $notice ) {
		$send_period = $notice[ 'send_period' ];
		if ( ! empty( $user_payment_notices[ $nearest_payment->ID ] ) && in_array( $send_period, $user_payment_notices[ $nearest_payment->ID ] ) ) {
			// This notice has already been sent
			continue;
		}

		$charge_modes = empty( $notice[ 'charge_modes' ] ) ? array(
			'automatic',
			'manual'
		) : $notice[ 'charge_modes' ];

		$payment_charge_mode = function_exists( 'edd_get_payment_charge_mode' ) ? edd_get_payment_charge_mode( $nearest_payment->ID ) : 'automatic';

		/**
		 * $send_period looks something like '-1month' - so it's suitable to be used
		 * in strtotime context
		 */
		if ( $today === date( 'Y-m-d', strtotime( $send_period, strtotime( $nearest_payment->date ) ) ) && in_array( $payment_charge_mode, $charge_modes ) ) {
			bpmj_eddpc_send_payment_notice_email( $user_id, $product_id, $nearest_payment, $notice, $payment_charge_mode );
			if ( ! isset( $user_payment_notices[ $nearest_payment->ID ] ) || ! is_array( $user_payment_notices[ $nearest_payment->ID ] ) ) {
				$user_payment_notices[ $nearest_payment->ID ] = array();
			}
			$user_payment_notices[ $nearest_payment->ID ][] = $send_period;
		}

	}
	update_user_meta( $user_id, '_bpmj_eddpc_payment_notices', $user_payment_notices );

	return true;
}

/**
 * @param string $period
 *
 * @return array|null
 */
function bpmj_eddpc_renewal_period_components( $period ) {
	if ( 1 === preg_match( '/(-|)(\d+)((?:day|week|month|year)s?)/', $period, $matches ) ) {
		return array(
			'number' => $matches[ 2 ],
			// Convert 'day' to 'days', 'week' to 'weeks' etc.
			'period' => 's' === substr( $matches[ 3 ], - 1 ) ? $matches[ 3 ] : $matches[ 3 ] . 's',
			'sign'   => $matches[ 1 ] === '-' ? '-' : '+',
		);
	}

	return null;
}

/**
 * @param string $period
 * @param string $type
 *
 * @return string
 */
function bpmj_eddpc_renewal_period_description( $period, $type = 'renewal' ) {
	$components = bpmj_eddpc_renewal_period_components( $period );
	if ( ! $components ) {
		return '';
	}

	return implode( ' ', array(
		$components[ 'number' ],
		bpmj_eddpc_renewal_period_type_options( $components[ 'period' ] ),
		bpmj_eddpc_renewal_period_sign_options( $type, $components[ 'sign' ] ),
	) );
}

/**
 * @param null|string $get
 *
 * @return array|string
 */
function bpmj_eddpc_renewal_period_type_options( $get = null ) {
	$options = array(
		'days'   => __( 'days/day', 'edd-paid-content' ),
		'weeks'  => __( 'weeks/week', 'edd-paid-content' ),
		'months' => __( 'months/month', 'edd-paid-content' ),
		'years'  => __( 'years/year', 'edd-paid-content' ),
	);
	if ( $get ) {
		return $options[ $get ];
	}

	return $options;
}

/**
 * @param string $type
 * @param null|string $get
 *
 * @return array|string
 */
function bpmj_eddpc_renewal_period_sign_options( $type = 'renewal', $get = null ) {
	switch ( $type ) {
		case 'payment':
			$period_sign_options = array(
				'-' => __( 'before payment', 'edd-paid-content' ),
				'+' => __( 'after payment', 'edd-paid-content' ),
			);
			break;
		default:
			$period_sign_options = array(
				'-' => __( 'before expiration', 'edd-paid-content' ),
				'+' => __( 'after expiration', 'edd-paid-content' ),
			);
	}

	if ( $get ) {
		return $period_sign_options[ $get ];
	}

	return $period_sign_options;
}

/**
 * @param string $type
 * @param string $selected_period
 * @param bool $echo
 *
 * @return null|string
 */
function bpmj_eddpc_renewal_period_input( $type = 'renewal', $selected_period = '-1days', $echo = true ) {
	$period_number     = 1;
	$period_period     = 'days';
	$period_sign       = '-';
	$period_components = bpmj_eddpc_renewal_period_components( $selected_period );
	if ( $period_components ) {
		$period_number = $period_components[ 'number' ];
		$period_period = $period_components[ 'period' ];
		$period_sign   = $period_components[ 'sign' ];
	}


	$html_options = function ( array $options, $selected ) {
		foreach ( $options as $value => $label ) {
			?>
			<option
				value="<?php echo esc_attr( $value ); ?>" <?php selected( $selected, $value ); ?>><?php echo esc_html( $label ); ?></option>
			<?php
		}
	};

	if ( ! $echo ) {
		ob_start();
	}
	?>
	<input type="number" min="0" name="send_period_number" title=""
	       value="<?php echo $period_number; ?>"/>
	<select name="send_period_period" title="">
		<?php
		$html_options( bpmj_eddpc_renewal_period_type_options(), $period_period );
		?>
	</select>
	<?php
	$sign_options = bpmj_eddpc_renewal_period_sign_options( $type );
	if ( count( $sign_options ) > 1 ):
		?>
		<select name="send_period_sign" title="">
			<?php
			$html_options( $sign_options, $period_sign );
			?>
		</select>
		<?php
	else:
		?>
		<input type="hidden" name="send_period_sign" value="<?php echo esc_attr( key( $sign_options ) ); ?>"/>
		<span><?php echo current( $sign_options ); ?></span>
		<?php
	endif;
	if ( ! $echo ) {
		return ob_get_clean();
	}

	return null;
}

/**
 * @param int|null $number_default
 * @param string|null $period_default
 * @param string|null $sign_default
 * @param string $number_key
 * @param string $period_key
 * @param string $sign_key
 *
 * @return null|string
 */
function bpmj_eddpc_renewal_period_combine_inputs( $number_default = null, $period_default = null, $sign_default = null, $number_key = 'send_period_number', $period_key = 'send_period_period', $sign_key = 'send_period_sign' ) {
	$number = isset( $_POST[ $number_key ] ) ? $_POST[ $number_key ] : $number_default;
	$period = isset( $_POST[ $period_key ] ) ? $_POST[ $period_key ] : $period_default;
	$sign   = isset( $_POST[ $sign_key ] ) ? $_POST[ $sign_key ] : $sign_default;

	if ( is_null( $number ) || is_null( $period ) || is_null( $sign ) ) {
		return null;
	}

	return implode( '', array(
		$sign === '+' ? '' : '-',
		$number,
		$period,
	) );
}

/**
 * @param string $url
 * @param int $protected_post_id
 *
 * @return string
 */
function bpmj_eddpc_encrypt_link( $url, $protected_post_id = null )
{
    $encrypt = apply_filters('bpmj_eddpc_enable_encrypt_link', true);

    if(!$encrypt){
        return $url;
    }

	$data = array(
		'user_id' => get_current_user_id(),
		'post_id' => $protected_post_id,
		'url'     => $url,
	);

	  return '?bpmj_eddpc_url=' . urlencode( base64_encode( xxtea_encrypt( serialize( $data ), AUTH_KEY ) ) );
}

/**
 * @param string $encrypted_string
 *
 * @return bool|array
 */
function bpmj_eddpc_decrypt_link( $encrypted_string ) {
	$decoded_string = base64_decode( $encrypted_string );
	if ( ! $decoded_string ) {
		return false;
	}
	$decrypted_string = xxtea_decrypt( $decoded_string, AUTH_KEY );
	if ( $decrypted_string ) {
		return unserialize( $decrypted_string );
	}

	return false;
}

/**
 * @param string $content
 * @param int $protected_post_id
 *
 * @return string
 */
function bpmj_eddpc_encrypt_all_wpupload_anchors( $content, $protected_post_id = null ) {
    $pattern = apply_filters('wpi_encrypt_anchors_pattern', '#href=("[^"]+?/wp-content/uploads/[^"]+"|\'[^\']+?/wp-content/uploads/[^\']+\')#');
	return preg_replace_callback( $pattern, function ( $matches ) use ( $protected_post_id ) {
		$quote = substr( $matches[ 1 ], 0, 1 );
		$url   = trim( $matches[ 1 ], '"\'' );

        $image_extensions = [
            'png',
            'jpg',
            'jpeg',
            'gif',
        ];

        if (!in_array(pathinfo($url, PATHINFO_EXTENSION), $image_extensions)) {
            $url = bpmj_eddpc_encrypt_link($url, $protected_post_id);
        }

        return 'href=' . $quote . $url . $quote;
	}, $content );
}


// _bpmj_eddpc_access - mutex
function bpmj_eddpc_get_access( $user_id ) {
    wp_cache_delete( $user_id, 'user_meta' ); // fixes issue with concurrent access to _bpmj_eddpc_access
    return get_user_meta( $user_id, '_bpmj_eddpc_access', true );
}

function bpmj_eddpc_set_access( $user_id, $access ) {
    update_user_meta( $user_id, "_bpmj_eddpc_access", $access );
}

function bpmj_eddpc_set_access_and_set_flag( $user_id, $access ) {
    bpmj_eddpc_set_flag( $user_id );
    bpmj_eddpc_set_access( $user_id, $access );
}

function bpmj_eddpc_get_access_and_remove_flag( $user_id ) {
    bpmj_eddpc_remove_flag( $user_id );
    return bpmj_eddpc_get_access( $user_id );
}

function bpmj_eddpc_set_access_if_flag_is_not_set( $user_id, $access ) {
    global $bpmj_eddpc_tnow;
    $flag_validity_timestamp = (int)get_transient( '_bpmj_eddpc_access_lock_' . $user_id );
    if( $flag_validity_timestamp >= $bpmj_eddpc_tnow ) {
        return false;
    }
    return bpmj_eddpc_set_access( $user_id, $access );
}

function bpmj_eddpc_set_flag( $user_id ) {
    global $bpmj_eddpc_tnow;
    set_transient( '_bpmj_eddpc_access_lock_' . $user_id, $bpmj_eddpc_tnow + 2, 2 ); // 2 sec
}

function bpmj_eddpc_remove_flag( $user_id ) {
    delete_transient( '_bpmj_eddpc_access_lock_' . $user_id );
}
// ===
