<?php

if ( !defined( 'ABSPATH' ) )
	exit;

function bpmj_eddpc_check_for_download_price_variations() {
	if ( !current_user_can( 'edit_products' ) ) {
		die( '-1' );
	}

	$download_id = absint( $_POST[ 'download_id' ] );
	$key		 = ( isset( $_POST[ 'key' ] ) ? absint( $_POST[ 'key' ] ) : 0 );
	$download	 = get_post( $download_id );

	if ( 'download' != $download->post_type ) {
		die( '-2' );
	}

	if ( edd_has_variable_prices( $download_id ) ) {
		$variable_prices = edd_get_variable_prices( $download_id );
		if ( $variable_prices ) {
			$ajax_response = '<select class="edd_price_options_select edd-select edd-select bpmj_eddpc_download" name="bpmj_eddpc_download[' . $key . '][price_id]">';
			$ajax_response .= '<option value="all">' . esc_html( __( 'All prices', 'edd-paid-content' ) ) . '</option>';
			foreach ( $variable_prices as $price_id => $price ) {
				$ajax_response .= '<option value="' . esc_attr( $price_id ) . '">' . esc_html( $price[ 'name' ] ) . '</option>';
			}
			$ajax_response .= '</select>';
			echo $ajax_response;
		}
	}
	edd_die();
}

add_action( 'wp_ajax_bpmj_eddpc_check_for_download_price_variations', 'bpmj_eddpc_check_for_download_price_variations' );

/**
 * Zapis access time
 * z karty użytkownika
 */
function bpmj_eddpc_save_access_time_ajax() {
	if ( !current_user_can( 'edit_products' ) ) {
		die( '-1' );
	}

	if ( 'POST' === $_SERVER[ 'REQUEST_METHOD' ] ) {

		$time		 = $_POST[ 'time' ];
		$timestamp	 = strtotime( $time ) - 3600;

		$user_id	 = $_POST[ 'user_id' ];
		$download_id = $_POST[ 'download_id' ];

		$user_access_time									 = get_user_meta( $user_id, "_bpmj_eddpc_access", true );
		$user_access_time[ $download_id ][ 'access_time' ]	 = $timestamp;

		update_user_meta( $user_id, "_bpmj_eddpc_access", $user_access_time );

		die( json_encode( bpmj_eddpc_date_i18n( 'd.m.Y - H:i:s', $timestamp ) ) );
	}
}

//add_action( 'wp_ajax_nopriv_bpmj_eddpc_save_access_time', 'bpmj_eddpc_save_access_time_ajax' );
add_action( 'wp_ajax_bpmj_eddpc_save_access_time', 'bpmj_eddpc_save_access_time_ajax' );

/**
 * Ustawienie no limit access
 * z karty użytkownika
 */
function bpmj_eddpc_no_limit_access_ajax() {
	if ( !current_user_can( 'edit_products' ) ) {
		die( '-1' );
	}

	if ( 'POST' === $_SERVER[ 'REQUEST_METHOD' ] ) {

		$nolimit = $_POST[ 'nolimit' ];

		if ( $nolimit == 'true' )
			$value	 = null;
		else
			$value	 = time();


		$user_id	 = $_POST[ 'user_id' ];
		$download_id = $_POST[ 'download_id' ];

		$user_access_time									 = get_user_meta( $user_id, "_bpmj_eddpc_access", true );
		$user_access_time[ $download_id ][ 'access_time' ]	 = $value;
		update_user_meta( $user_id, "_bpmj_eddpc_access", $user_access_time );

		if ( $nolimit == 'true' )
			die( json_encode( array( 'status' => true, 'info' => __( 'No limit', 'edd-paid-content' ) ) ) );
		else
			die( json_encode( array( 'status' => false, 'info' => bpmj_eddpc_date_i18n( 'd.m.Y - H:i:s', $value ), 'date' => bpmj_eddpc_date_i18n( 'd.m.Y H:i', $value ) ) ) );
	}
}

//add_action( 'wp_ajax_nopriv_bpmj_eddpc_no_limit_access', 'bpmj_eddpc_no_limit_access_ajax' );
add_action( 'wp_ajax_bpmj_eddpc_no_limit_access', 'bpmj_eddpc_no_limit_access_ajax' );

/**
 * Zapis total time
 * z karty użytkownika
 */
function bpmj_eddpc_save_total_time_ajax() {
	if ( !current_user_can( 'edit_products' ) ) {
		die( '-1' );
	}

	if ( 'POST' === $_SERVER[ 'REQUEST_METHOD' ] ) {

		$time	 = $_POST[ 'time' ];
		$time	 = explode( ' ', $time );

		$seconds = 0;
		$seconds += $time[ 0 ] * 24 * 60 * 60;
		$seconds += $time[ 2 ] * 60 * 60;
		$seconds += $time[ 4 ] * 60;
		$seconds += $time[ 6 ];

		if ( $time[ 0 ] == '-0' ) {
			$seconds = -$seconds;
		}

		$user_id	 = $_POST[ 'user_id' ];
		$download_id = $_POST[ 'download_id' ];

		$user_access_time = get_user_meta( $user_id, "_bpmj_eddpc_access", true );

		$user_access_time[ $download_id ][ 'total_time' ]	 = $seconds;
		$user_access_time[ $download_id ][ 'last_time' ]	 = time();

		update_user_meta( $user_id, "_bpmj_eddpc_access", $user_access_time );

		die( json_encode( true ) );
	}
}

//add_action( 'wp_ajax_nopriv_bpmj_eddpc_save_total_time', 'bpmj_eddpc_save_total_time_ajax' );
add_action( 'wp_ajax_bpmj_eddpc_save_total_time', 'bpmj_eddpc_save_total_time_ajax' );

/**
 * Usunięcie access time
 * z karty użytkownika
 */
function bpmj_eddpc_delete_access_ajax() {
	if ( !current_user_can( 'edit_products' ) ) {
		die( '-1' );
	}

	if ( 'POST' === $_SERVER[ 'REQUEST_METHOD' ] ) {

		$user_id	 = $_POST[ 'user_id' ];
		$download_id = $_POST[ 'download_id' ];

		$user_access_time = get_user_meta( $user_id, "_bpmj_eddpc_access", true );
		unset( $user_access_time[ $download_id ] );

		update_user_meta( $user_id, "_bpmj_eddpc_access", $user_access_time );

		die( json_encode( bpmj_eddpc_date_i18n( 'd.m.Y - H:i:s', $timestamp ) ) );
	}
}

add_action( 'wp_ajax_bpmj_eddpc_delete_access', 'bpmj_eddpc_delete_access_ajax' );
?>
