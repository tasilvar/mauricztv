<?php
// Exit if accessed directly

use bpmj\wpidea\admin\pages\customers\Customers;
use bpmj\wpidea\caps\Access_Filters;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function bpmj_eddcm_edd_is_admin_page( $found, $page ) {
	global $pagenow;
	if ( ! $found && ( 0 === strpos( $page, 'wp-idea' ) || in_array( $pagenow, array(
				'profile.php',
				'user-edit.php'
			) ) )
	) {
		return true;
	}

	return $found;
}

add_filter( 'edd_is_admin_page', 'bpmj_eddcm_edd_is_admin_page', 10, 2 );

/**
 * Modifies EDD settings url so that it points to WP Idea settings
 *
 * @param string $url
 * @param string $path
 *
 * @return string
 */
function bpmj_eddcm_edd_settings_url( $url, $path ) {
	if ( 'edit.php?post_type=download&page=edd-settings' === $path ) {
		return admin_url( 'admin.php?page=wp-idea-settings#courses_payment_gates' );
	}

	return $url;
}

add_filter( 'admin_url', 'bpmj_eddcm_edd_settings_url', 10, 2 );

/**
 * Modifies EDDPC renewals url to point to user edit page
 *
 * @param string $url
 * @param string $path
 *
 * @return string
 */
function bpmj_eddcm_eddpc_renewals_url( $url, $path ) {
	if ( 0 === strpos( $path, 'admin.php?page=' . Customers::PAGE . '&view=bpmj_eddpc_renewals' )
	     && 1 === preg_match( '/&id=(\d+)(?:$|[^\d])/', $path, $matches )
	) {
		$customer_id = (int) $matches[ 1 ];
		$customer    = new EDD_Customer( $customer_id );
		if ( $customer->user_id ) {
			return admin_url( 'user-edit.php?user_id=' . $customer->user_id . '#edd-courses-manager' );
		}
	}

	return $url;
}

add_filter( 'admin_url', 'bpmj_eddcm_eddpc_renewals_url', 10, 2 );

/**
 * @param string $translation
 * @param string $text
 *
 * @return string
 */
function bpmj_eddcm_edd_change_bot_name( $bot_name ) {
	if ( 'EDD Bot' === $bot_name || Access_Filters::cannot_see_sensitive_data() ) {
		return 'WP Idea Bot';
	}

	return $bot_name;
}

add_filter( 'edd_get_payment_note_user', 'bpmj_eddcm_edd_change_bot_name', 10, 1 );

function bpmj_eddcm_change_url_for_elementor_preview( $url, $document ) {
	$correct_id = get_post_meta( $document->get_main_id(), 'course_id', true );
	if ( 'courses' === get_post_type( $document->get_main_id() ) ) {
		return add_query_arg( array( 'elementor-preview' => $correct_id ), $url );
	}

	return $url;
}

add_filter( 'elementor/document/urls/preview', 'bpmj_eddcm_change_url_for_elementor_preview', 10, 2 );

function bpmj_eddcm_wpwf_settings_url( $url ) {
	return admin_url( 'admin.php?page=wp-idea-settings#courses_invoices' );
}

add_filter( 'bpmj_wpwf_settings_url', 'bpmj_eddcm_wpwf_settings_url' );