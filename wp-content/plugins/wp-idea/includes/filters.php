<?php

use bpmj\wpidea\infrastructure\system\System;
use bpmj\wpidea\Caps;
use bpmj\wpidea\wolverine\user\User;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

include_once __DIR__ . '/buy-as-gift/filters.php';

include_once __DIR__ . '/filters/registration-process.php';

// cleanup
function bpmj_eddcm_filters_cleanup() {
	//remove_action( 'edd_meta_box_price_fields', 'edd_render_price_field', 10 );
}
add_action('init', 'bpmj_eddcm_filters_cleanup');

// zmiana wyglądu downloads

function bpmj_edd_custom_download_link( $purchase_form, $args ) {

	if ( ! isset( $args[ 'download_id' ] ) ) {
		return $purchase_form;
	}

	$product_id = $args[ 'download_id' ];

	$course = WPI()->courses->get_course_by_product( $product_id );

    if ( false === $course ) {
        global $post;

        $product_type = get_post_meta( $post->ID, '_edd_product_type', true );
        $sales_status = WPI()->courses->get_sales_status( $post->ID, $product_id );

        if ( $product_type && 'disabled' === $sales_status[ 'status' ] ) {
            return preg_replace( '/<form/', '<form class="edd-sales-disabled" data-eddcm-sales-disabled-reason="' . esc_attr( $sales_status[ 'reason' ] ) . '" data-eddcm-sales-disabled-reason-long="' . esc_attr( $sales_status[ 'reason_long' ] ) . '"', $purchase_form, 1 );
        }

        return $purchase_form;
    }

	$user_id = get_current_user_id();
	if ( $user_id ) {
		$course_page_id = get_post_meta( $course->ID, 'course_id', true );
		$restricted_to  = array( array( 'download' => $product_id ) );
		$access         = bpmj_eddpc_user_can_access( $user_id, $restricted_to, $course_page_id );
		if ( 'valid' === $access[ 'status' ] || 'waiting' === $access[ 'status' ] ) {
			$links = get_post_meta( $product_id, 'edd_download_files', true );

			if ( is_array( $links ) ) {
				$link = array_shift( $links );
			} else {
				$link = array();
			}

			if ( isset( $link[ 'file' ] ) ) {
				return '<div class="edd_purchase_submit_wrapper">
			<a href="' . $link[ 'file' ] . '" class="button orange edd-submit edd-has-js"><span class="edd-add-to-cart-label">' . $link[ 'name' ] . '</span></a>
			</div>';
			}

			return '';
		}
	}
	/*
	 * Hide download link if purchasing this item is disabled
	 */
	$sales_status = WPI()->courses->get_sales_status( $course->ID, $product_id );
	if ( 'disabled' === $sales_status[ 'status' ] ) {
		return preg_replace( '/<form/', '<form class="edd-sales-disabled" data-eddcm-sales-disabled-reason="' . esc_attr( $sales_status[ 'reason' ] ) . '" data-eddcm-sales-disabled-reason-long="' . esc_attr( $sales_status[ 'reason_long' ] ) . '"', $purchase_form, 1 );
	}

	return $purchase_form;
}

add_filter( 'edd_purchase_download_form', 'bpmj_edd_custom_download_link', 10, 2 );

function bpmj_eddcm_purchase_limit_items_left( $product_id ) {
	if ( edd_has_variable_prices( $product_id ) ) {
		return;
	}
	$purchase_limit            = (int) get_post_meta( $product_id, '_bpmj_eddcm_purchase_limit', true );
	$purchase_limit_items_left = (int) get_post_meta( $product_id, '_bpmj_eddcm_purchase_limit_items_left', true );

	if ( $purchase_limit > 0 && $purchase_limit_items_left > 0 ) {
		printf( __( 'Items available: %d', BPMJ_EDDCM_DOMAIN ), $purchase_limit_items_left );
	}
}

add_filter( 'edd_purchase_link_top', 'bpmj_eddcm_purchase_limit_items_left', 20 );

function bpmj_eddcm_purchase_limit_variable_price( $key, $price ) {
	$purchase_limit            = isset( $price[ 'bpmj_eddcm_purchase_limit' ] ) ? (int) $price[ 'bpmj_eddcm_purchase_limit' ] : 0;
	$purchase_limit_items_left = isset( $price[ 'bpmj_eddcm_purchase_limit_items_left' ] ) ? (int) $price[ 'bpmj_eddcm_purchase_limit_items_left' ] : 0;

	if ( $purchase_limit > 0 ) {
		?>
		<span
			class="bpmj-eddcm-variable-price-purchase-limit <?php
			echo 0 === $purchase_limit_items_left ? 'bpmj-eddcm-variable-price-purchase-limit-reached' : '';
			?>"><?php printf( __( 'Items available: %d', BPMJ_EDDCM_DOMAIN ), $purchase_limit_items_left ); ?></span>
		<?php
	}
}

add_action( 'edd_after_price_option', 'bpmj_eddcm_purchase_limit_variable_price', 10, 2 );

function bpmj_the_permalink_custom_download_link( $url ) {

	$user_id = get_current_user_id();

	if ( empty( $user_id ) ) {
		return $url;
	}

	$download_id = url_to_postid( $url );
	if ( empty( $download_id ) ) {
		return $url;
	}

	if ( get_post_type( $download_id ) != 'download' ) {
		return $url;
	}

	$restricted_to = array( array( 'download' => $download_id ) );
	$course_page   = WPI()->courses->get_course_by_product( $download_id );
	if ( ! $course_page ) {
		return $url;
	}
	$access = bpmj_eddpc_user_can_access( $user_id, $restricted_to, $course_page->ID );
	if ( 'valid' === $access[ 'status' ] || 'waiting' === $access[ 'status' ] ) {
		$links = get_post_meta( $download_id, 'edd_download_files', true );

		$link = array_shift( $links );

		if ( isset( $link[ 'file' ] ) ) {
			return $link[ 'file' ];
		}
	}

	return $url;
}

add_filter( 'the_permalink', 'bpmj_the_permalink_custom_download_link' );

/**
 * @param WP_Post[] $items
 *
 * @return array
 */
function bpmj_courses_menu( $items ) {
	global $post;

	if ( ! function_exists( 'bpmj_eddpc_is_restricted' ) ) {
		return $items;
	}

	$new_items = array();
	foreach ( $items as $i => $item ) {
		if ( '#bpmj-eddcm-panel#' === $item->url ) {
			if ( WPI()->templates->is_in_course() ) {
				$item->classes[]  = 'has-sub';
				$ancestors        = get_post_ancestors( $post->ID );
				$top              = ( $ancestors ) ? $ancestors[ count( $ancestors ) - 1 ] : $post->ID;
                $course_structure = WPI()->courses->get_course_structure_flat( $top ,true, true, false, true);

				if ( empty( $course_structure ) ) {
					unset( $items[ $i ] );
				} else {
					$item->url = get_permalink( $top );
					foreach ( $course_structure as $module_or_lesson ) {
					    if($module_or_lesson->post_parent !== $top) {
					        continue;
                        }

						$css_classes = array( 'menu-item' );
						if ( $module_or_lesson->should_be_grayed_out() ) {
							$css_classes[] = 'grayed';
						}
						$new_item = array(
							'title'            => get_the_title( $module_or_lesson->ID ),
							'menu_item_parent' => $item->ID,
							'ID'               => $module_or_lesson->ID,
							'db_id'            => $module_or_lesson->ID,
							'url'              => $module_or_lesson->should_be_grayed_out() ? 'javascript:' : get_permalink( $module_or_lesson->ID ),
							'classes'          => $css_classes,
							'current'		   => null,
							'target'		   => '',
							'xfn'              => ''
						);

						$new_items[] = ( object ) $new_item;
					}
				}
			} else {
				unset( $items[ $i ] );
			}
		} else if ( '#bpmj-eddcm-my-courses#' === $item->url ) {
			$users_courses = WPI()->courses->get_users_accessible_courses();
			if ( empty( $users_courses ) ) {
				unset( $items[ $i ] );
			} else {
				$item->url       = '#';
				$item->classes[] = 'has-sub';
				foreach ( $users_courses as $course ) {
					$course_page_id = get_post_meta( $course[ 'id' ], 'course_id', true );
					$new_item       = array(
						'title'            => $course[ 'title' ],
						'menu_item_parent' => $item->ID,
						'ID'               => $course_page_id,
						'db_id'            => '',
						'url'              => $course[ 'url' ],
						'classes'          => array( 'menu-item' ),
						'current'		   => null,
						'target'		   => '',
						'xfn'              => ''
					);

					$new_items[] = ( object ) $new_item;
				}
			}
		} else if ( '#bpmj-eddcm-login#' === $item->url ) {
			if ( false === strpos( $item->post_title, '|' ) ) {
				$title_login = $title_logout = $item->post_title;
			} else {
				list( $title_login, $title_logout ) = explode( '|', $item->post_title, 2 );
			}
			if ( is_user_logged_in() ) {
				$item->post_title = $title_logout;
				$item->url        = wp_logout_url();
			} else {
				$item->post_title = $title_login;
				$item->url        = wp_login_url();
			}
			$item->title = $item->post_title;
			$item->classes[] = 'bpmj-login-logout';
		}
	}
	$items = array_merge( $items, $new_items );

	return $items;
}

add_filter( 'wp_nav_menu_objects', 'bpmj_courses_menu' );

function bpmj_cm_should_deny_access() {
	global $wp_query;

	if ( is_singular() ) {
		$uid = get_current_user_id();
		if ( empty( $uid ) ) {
			return false;
		}

		$post_id = $wp_query->get_queried_object_id();

		// Jeżeli jest zabezpieczony
		$restricted = bpmj_eddpc_is_restricted( $post_id );
		if ( $restricted ) {

			// czy ma dostęp
			$access = bpmj_eddpc_user_can_access( $uid, $restricted, $post_id );

			if ( 'valid' == $access[ 'status' ] ) {
				return false;
			}

			$redir = bpmj_eddpc_get_redirect_url( $post_id );
			if ( empty( $redir ) ) {
				return true;
			}
		}
	}

	return false;
}

//add_filter( 'the_content', 'bpmj_courses_lesson_nav' );
// szablon - brak dostępu

function bpmj_cm_get_template_path_page_filter( $page ) {
	if ( 'home.php' == $page || 'full.php' == $page || 'lesson.php' == $page || 'test.php' == $page ) {
		if ( bpmj_cm_should_deny_access() ) {
			return 'page.php';
		}
	}

	return $page;
}

add_filter( 'bpmj_cm_get_template_path_page', 'bpmj_cm_get_template_path_page_filter' );

// Body class when no access
function bpmj_cm_get_body_class( $class ) {
	if ( bpmj_cm_should_deny_access() ) {
		return 'contact form';
	}

	return $class;
}

add_filter( 'bpmj_cm_get_body_class', 'bpmj_cm_get_body_class' );

function bpmj_eddcm_login_headerurl() {
	return '/';
}

add_filter( 'login_headerurl', 'bpmj_eddcm_login_headerurl' );

/**
 * Hides the admin bar from ordinary subscribers
 *
 * @param bool $show_admin_bar
 *
 * @return bool
 */
function bpmj_eddcm_show_admin_bar( $show_admin_bar ) {
	return bpmj_eddcm_is_user_a_subscriber() || bpmj_eddcm_is_user_a_partner() ? false : $show_admin_bar;
}

add_filter( 'show_admin_bar', 'bpmj_eddcm_show_admin_bar' );

add_action( 'edd_complete_purchase', 'bpmj_eddcm_add_participant_to_course' );
add_action( 'edd_update_payment_status', 'bpmj_eddcm_remove_participant_from_course', 10, 3 );

/**
 * @param $payment_id
 */
function bpmj_eddcm_add_participant_to_course( $payment_id ) {
	$user_id = bpmj_eddcm_get_payment_user_id( $payment_id );
	$eddcm_purchase_data = edd_get_payment_meta( $payment_id, 'bpmj_eddcm_purchase_data' );
	if(is_array($eddcm_purchase_data) && key_exists('bpmj_eddcm_buy_as_gift', $eddcm_purchase_data) && $eddcm_purchase_data['bpmj_eddcm_buy_as_gift']) {
	    return;
	}
	foreach ( WPI()->courses->get_courses_by_payment( $payment_id ) as $course ) {
		WPI()->courses->add_course_participant( $course->ID, $user_id, $payment_id );
	}
}

/**
 * @param int $payment_id
 * @param string $new_status
 * @param string $old_status
 */
function bpmj_eddcm_remove_participant_from_course( $payment_id, $new_status, $old_status ) {
	if ( 'publish' !== $old_status && 'revoked' !== $old_status ) {
		return;
	}

	if ( 'publish' === $new_status ) {
		return;
	}

	$user_id = edd_get_payment_user_id( $payment_id );
	foreach ( WPI()->courses->get_courses_by_payment( $payment_id ) as $course ) {
		WPI()->courses->remove_course_participant( $course->ID, $user_id, $payment_id );
	}
}

/**
 * @param $mail_message
 *
 * @return mixed
 */
function bpmj_eddcm_remove_unnecessary_chars_from_email( $mail_message ) {
	$mail_message = preg_replace( '/^\<(http(?:.+?))\>\s*$/m', '$1', $mail_message );

	return $mail_message;
}

add_filter( 'retrieve_password_message', 'bpmj_eddcm_remove_unnecessary_chars_from_email' );

function bpmj_eddcm_login_redirect( $redirect_to, $requested_redirect_to, $user ) {
	global $wpidea_settings;

	if (empty($requested_redirect_to) || $requested_redirect_to === home_url('/')) {
		if ($user && is_object($user) && is_a($user, 'WP_User')) {
			$app_user = User::find($user->ID);


            if ($app_user->can(Caps::CAP_MANAGE_SETTINGS)) {
				return admin_url();
            }
		}
	}

	if ( ! empty( $requested_redirect_to ) ) {
		return $requested_redirect_to;
	}

	if ( ! empty( $wpidea_settings[ 'page_to_redirect_to_after_login' ] ) ) {
		$redirect_to_permalink = get_permalink( $wpidea_settings[ 'page_to_redirect_to_after_login' ] );
		if ( $redirect_to_permalink ) {
			return $redirect_to_permalink;
		}
	}

	return $redirect_to;
}

add_filter( 'login_redirect', 'bpmj_eddcm_login_redirect', 10, 3 );

/**
 * @param array $item
 *
 * @return bool|array
 */
function bpmj_eddcm_edd_add_to_cart_item( $item ) {
	$product_id = $item[ 'id' ];
	$price_id   = isset( $item[ 'options' ][ 'price_id' ] ) ? $item[ 'options' ][ 'price_id' ] : 0;

	if ( ! bpmj_eddcm_can_purchase_product( $product_id, $price_id ) ) {
		return false;
	}

	return $item;
}

add_filter( 'edd_add_to_cart_item', 'bpmj_eddcm_edd_add_to_cart_item' );

/**
 * Hides the item's price if the item is a course and the user has access to it
 *
 * @param string $formatted_price
 * @param int $download_id
 * @param int $price_id
 *
 * @return string
 */
function bpmj_eddcm_hide_price_when_user_has_access( $formatted_price, $download_id, $price_id ) {
	if ( is_admin() ) {
		return $formatted_price;
	}

	$restricted_to = array( array( 'download' => $download_id ) );
	$course_page   = WPI()->courses->get_course_by_product( $download_id );
	if ( ! $course_page ) {
		return $formatted_price;
	}
	$access = bpmj_eddpc_user_can_access( get_current_user_id(), $restricted_to, $course_page->ID );
	if ( 'valid' === $access[ 'status' ] || 'waiting' === $access[ 'status' ] ) {
		return '';
	}

	return $formatted_price;
}

add_filter( 'edd_download_price_after_html', 'bpmj_eddcm_hide_price_when_user_has_access', 100, 3 );

/**
 * @param bool $is_recurring
 * @param int $post_id
 *
 * @return bool
 */
function bpmj_eddcm_is_recurring( $is_recurring, $post_id ) {
	if ( in_array( get_post_type( $post_id ), array( 'courses', 'download' ) ) ) {
		return true;
	}

	return $is_recurring;
}

add_filter( 'edd_download_is_recurring', 'bpmj_eddcm_is_recurring', 10, 2 );


function bpmj_eddcm_disable_user_pending_verification( $pending ) {
	return false;
}

add_filter( 'edd_user_pending_verification', 'bpmj_eddcm_disable_user_pending_verification' );

/**
 * @param array $required_fields
 *
 * @return array
 */
function bpmj_eddcm_purchase_form_required_fields( $required_fields ) {
	$wpidea_settings = get_option( WPI()->settings->get_settings_slug() );
	if ( ! empty( $wpidea_settings[ 'last_name_required' ] ) && 'on' === $wpidea_settings[ 'last_name_required' ] ) {
		$required_fields[ 'edd_last' ] = array(
			'error_id'      => 'invalid_last_name',
			'error_message' => __( 'Please enter your last name', BPMJ_EDDCM_DOMAIN ),
		);
	}

	return $required_fields;
}

add_filter( 'edd_purchase_form_required_fields', 'bpmj_eddcm_purchase_form_required_fields' );

/**
 * @param array $purchase_data
 *
 * @return array
 */
function bpmj_eddcm_purchase_data_before_gateway( $purchase_data ) {
	$wpidea_settings = get_option( WPI()->settings->get_settings_slug() );
	$data            = $purchase_data[ 'post_data' ];
	if ( ! empty( $data[ 'bpmj_eddcm_phone_no' ] ) ) {
		$purchase_data[ 'bpmj_eddcm_phone_no' ]     = preg_replace( '/[^\d]+/', '', $data[ 'bpmj_eddcm_phone_no' ] );
		$purchase_data[ 'bpmj_eddcm_phone_no_raw' ] = sanitize_text_field( $data[ 'bpmj_eddcm_phone_no' ] );
	}

	$additional_checkbox_checked     = isset( $data[ 'bpmj_eddcm_additional_checkbox' ] ) && '1' === $data[ 'bpmj_eddcm_additional_checkbox' ];
	$additional_checkbox_description = isset( $wpidea_settings[ 'additional_checkbox_description' ] ) ? $wpidea_settings[ 'additional_checkbox_description' ] : '';
	if ( $additional_checkbox_checked ) {
		$purchase_data[ 'bpmj_eddcm_additional_checkbox_checked' ]     = true;
		$purchase_data[ 'bpmj_eddcm_additional_checkbox_description' ] = $additional_checkbox_description;
	}

	$additional_checkbox2_checked     = isset( $data[ 'bpmj_eddcm_additional_checkbox2' ] ) && '1' === $data[ 'bpmj_eddcm_additional_checkbox2' ];
	$additional_checkbox2_description = isset( $wpidea_settings[ 'additional_checkbox2_description' ] ) ? $wpidea_settings[ 'additional_checkbox2_description' ] : '';
	if ( $additional_checkbox2_checked ) {
		$purchase_data[ 'bpmj_eddcm_additional_checkbox2_checked' ]     = true;
		$purchase_data[ 'bpmj_eddcm_additional_checkbox2_description' ] = $additional_checkbox2_description;
	}

	if ( ! empty( $data[ 'bpmj_eddcm_order_comment' ] ) ) {
		$purchase_data[ 'bpmj_eddcm_order_comment' ] = sanitize_textarea_field( $data[ 'bpmj_eddcm_order_comment' ] );
	}

	if ( ! empty( $data[ 'bpmj_eddcm_buy_as_gift' ] ) ) {
		$purchase_data[ 'bpmj_eddcm_buy_as_gift' ] = true;
	}

	return $purchase_data;
}

add_filter( 'edd_purchase_data_before_gateway', 'bpmj_eddcm_purchase_data_before_gateway' );

/**
 * @param string $subject
 * @param int $payment_id
 *
 * @return string
 */
function bpmj_eddcm_admin_sale_notification_subject( $subject, $payment_id ) {
	$eddcm_purchase_data = edd_get_payment_meta( $payment_id, 'bpmj_eddcm_purchase_data', true );
	if ( ! empty( $eddcm_purchase_data[ 'bpmj_eddcm_order_comment' ] ) ) {
		$subject .= ' [' . __( 'has additional order info', BPMJ_EDDCM_DOMAIN ) . ']';
	}

	return $subject;
}

add_filter( 'edd_admin_sale_notification_subject', 'bpmj_eddcm_admin_sale_notification_subject', 10, 2 );

/**
 * This function adds "additional information" on top of email notification  body
 *
 * @param string $email_body
 * @param int $payment_id
 *
 * @return string
 */
function bpmj_eddcm_admin_sale_notification_body( $email_body, $payment_id ) {
	$eddcm_purchase_data = edd_get_payment_meta( $payment_id, 'bpmj_eddcm_purchase_data' );
	if ( ! empty( $eddcm_purchase_data[ 'bpmj_eddcm_order_comment' ] ) ) {
		$additional_order_info = $eddcm_purchase_data[ 'bpmj_eddcm_order_comment' ];
		$additional_order_info = apply_filters( 'edd_email_template_wpautop', true ) ? wpautop( $additional_order_info ) : $additional_order_info;
        $email_body            = str_replace('{comment}', $additional_order_info, $email_body);
	}

    $email_body = str_replace('{comment}', '-', $email_body);

	return $email_body;
}

add_filter( 'edd_sale_notification', 'bpmj_eddcm_admin_sale_notification_body', 10, 2 );

/**
 * @param string $translation
 * @param string $text
 *
 * @return string
 */
function bpmj_eddcm_edd_change_discount_text( $translation, $text ) {
	if ( 'Have a discount code?' === $text && bpmj_eddcm_is_buy_as_gift_possible() ) {
		return __( 'Do you have a discount code or voucher?', BPMJ_EDDCM_DOMAIN );
	}

	return $translation;
}

add_filter( 'gettext', 'bpmj_eddcm_edd_change_discount_text', 10, 2 );

function bpmj_eddcm_login_login_headertext() {
	return get_bloginfo( 'name', 'display' );
}

add_filter( 'login_headertext', 'bpmj_eddcm_login_login_headertext' );

/**
 * @param string $disposition
 *
 * @return string
 */
function bpmj_eddcm_file_disposition( $disposition ) {
	$wpidea_settings = get_option( WPI()->settings->get_settings_slug() );
	$disposition     = empty( $wpidea_settings[ 'allow_inline_file_download' ] ) ? 'attachment' : $wpidea_settings[ 'allow_inline_file_download' ];

	return $disposition;
}

add_filter( 'bpmj_eddpc_encrypted_url_disposition', 'bpmj_eddcm_file_disposition' );

/**
 * @param array $gateways
 *
 * @return array
 */
function bpmj_eddcm_gateway_checkout_labels($gateways) {
	$edd_settings = edd_get_settings();

	foreach ( $gateways as $gateway => $info ) {
		$gateway_normalized = str_replace( '_gateway', '', $gateway );
		if ( ! empty( $edd_settings[ $gateway_normalized . '_checkout_label' ] ) ) {
			$gateways[ $gateway ][ 'checkout_label' ] = $edd_settings[ $gateway_normalized . '_checkout_label' ];
		}
	}

	return $gateways;
}

add_filter('edd_payment_gateways', 'bpmj_eddcm_gateway_checkout_labels', 999);

/**
 * @param boolean $enable
 *
 * @return bool
 */
function bpmj_eddcm_enable_sell_discounts( $enable ) {
	global $wpidea_settings;

	return ! empty( $wpidea_settings[ 'enable_sell_discount' ] );
}

add_filter( 'bpmj_edd_sell_discount_enabled', 'bpmj_eddcm_enable_sell_discounts' );

/**
 * @param array $args
 *
 * @return array
 */
function bpmj_eddcm_sell_discount_discount_args( $args ) {
	unset( $args[ 'meta_query' ][ 'relation' ] );

	$args = array(
		'meta_query' => array(
			'relation' => 'AND',
			array_merge( array(
				'relation' => 'OR',
			), $args[ 'meta_query' ] ),
			array(
				'relation' => 'OR',
				array(
					'key'     => '_bpmj_eddcm_gift_voucher',
					'value'   => '1',
					'compare' => '!=',
				),
				array(
					'key'     => '_bpmj_eddcm_gift_voucher',
					'compare' => 'NOT EXISTS',
				),
			)
		)
	);

	return $args;
}

add_filter( 'bpmj_edd_sell_discount_discounts_query_args', 'bpmj_eddcm_sell_discount_discount_args' );

add_filter( 'admin_url', 'bpmj_eddcm_change_add_new_link_for_courses', 10, 2 );
function bpmj_eddcm_change_add_new_link_for_courses( $url, $path ){
    if( $path === 'post-new.php?post_type=courses' ) {
        $url = 'admin.php?page=wp-idea-creator';
    }
    return $url;
}

// pricing

/**
 * Individual Price Row
 *
 * Used to output a table row for each price associated with a download.
 * Can be called directly, or attached to an action.
 *
 * @since 1.2.2
 *
 * @param       $key
 * @param array $args
 * @param       $post_id
 */
function bpmj_eddcm_edd_render_price_row($key, $args, $post_id, $index ) {
	remove_action( 'edd_render_price_row', 'edd_render_price_row', 10 );
	$defaults = array(
		'name'   => null,
		'amount' => null
	);

	$args = wp_parse_args( $args, $defaults );

	$default_price_id = edd_get_default_variable_price( $post_id );
	$currency_position = edd_get_option( 'currency_position', 'before' );

?>
	<td>
		<span class="edd_draghandle"></span>
		<input type="hidden" name="edd_variable_prices[<?php echo $key; ?>][index]" class="edd_repeatable_index" value="<?php echo $index; ?>"/>
	</td>
	<td>
		<?php echo EDD()->html->text( array(
			'name'  => 'edd_variable_prices[' . $key . '][name]',
			'value' => esc_attr( $args['name'] ),
			'placeholder' => __( 'Option Name', 'easy-digital-downloads' ),
			'class' => 'edd_variable_prices_name large-text'
		) ); ?>
	</td>

	<td>
		<?php
			$price_args = array(
				'name'  => 'edd_variable_prices[' . $key . '][amount]',
				'value' => $args['amount'],
				'placeholder' => edd_format_amount( 9.99 ),
				'class' => 'edd-price-field'
			);
		?>

		<?php if( $currency_position == 'before' ) : ?>
			<?php echo EDD()->html->text( $price_args ); ?>
		<?php else : ?>
			<?php echo EDD()->html->text( $price_args ); ?>
		<?php endif; ?>
	</td>
	<td class="edd_repeatable_default_wrapper">
		<label class="edd-default-price">
			<input type="radio" <?php checked( $default_price_id, $key, true ); ?> class="edd_repeatable_default_input" name="_edd_default_price_id" value="<?php echo $key; ?>" />
			<span class="screen-reader-text"><?php printf( __( 'Set ID %s as the default price', 'easy-digital-downloads' ), $key ); ?></span>
		</label>
	</td>

	<td>
		<span class="edd_price_id"><?php echo $key; ?></span>
	</td>

	<?php do_action( 'edd_download_price_table_row', $post_id, $key, $args ); ?>

	<td>
		<a href="#" class="edd_remove_repeatable" data-type="price" style="background: url(<?php echo admin_url('/images/xit.gif'); ?>) no-repeat;">&times;</a>
	</td>
<?php
}
add_action( 'edd_render_price_row', 'bpmj_eddcm_edd_render_price_row', 9, 4 );

function bmpj_eddcm_cron_schedules_for_sales_dates( $schedules ) {
    $schedules[ 'bpmj_eddcm_5min' ] = array(
        'interval'	 => 60 * 5,
        'display'	 => __( '5 minutes', BPMJ_EDDCM_DOMAIN )
    );
    return $schedules;
}

add_filter( 'cron_schedules', 'bmpj_eddcm_cron_schedules_for_sales_dates' );

function bpmj_eddcm_search_by_title_filter( $where, $wp_query )
{
    global $wpdb;

    if ( $search_term = $wp_query->get( 'search_prod_title' ) )
        $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( $wpdb->esc_like( $search_term ) ) . '%\'';

    return $where;
}

add_filter( 'posts_where', 'bpmj_eddcm_search_by_title_filter', 10, 2 );

function bpmj_eddcm_renewal_email_subject_for_course( $renewal_subject, $user, $product_id, $renewal_id ) {
    $option = get_option( 'bmpj_eddpc_renewal' );
    $disable_email_subscription = get_post_meta( $product_id, 'disable_email_subscription', true );
    if ( ! empty( $option ) && 'on' === $disable_email_subscription ) {
        return '';
    }

    return $renewal_subject;
}
add_filter( 'bpmj_eddpc_renewal_email_subject', 'bpmj_eddcm_renewal_email_subject_for_course', 10, 4 );

function bpmj_eddcm_notice_email_subject_for_course( $subject, $user, $product_id ) {
    return bpmj_eddcm_renewal_email_subject_for_course( $subject, $user, $product_id, null );
}
add_filter( 'bpmj_eddpc_notice_email_subject', 'bpmj_eddcm_notice_email_subject_for_course', 10, 3 );

function bpmj_eddcm_tax_rate( $tax, $order_id ) {

    $payment = new EDD_Payment( $order_id );
    $payment_meta = $payment->get_meta();
    if (
            bpmj_edd_invoice_data_get_cb_setting( 'edd_id_enable_vat_moss' ) &&
            $payment_meta['bpmj_edd_invoice_type'] === 'company' &&
            $payment_meta['user_info']['address']['country'] !== 'PL'
    ) {
        return 'np';
    }

    return $tax;
}

function tax_rate_for_wfirma( $tax, $order_id ) {

    $payment = new EDD_Payment( $order_id );
    $payment_meta = $payment->get_meta();
    if (
        bpmj_edd_invoice_data_get_cb_setting( 'edd_id_enable_vat_moss' ) &&
        $payment_meta['bpmj_edd_invoice_type'] === 'company' &&
        $payment_meta['user_info']['address']['country'] !== 'PL'
    ) {
        $tax_rate = 'np';

        if ( bpmj_eddcm_is_moss_eu( $payment ) ) {
            $tax_rate = 'npue';
        }

        return $tax_rate;
    }

    return $tax;
}

add_filter( 'bpmj_wpfa_edd_tax_product', 'bpmj_eddcm_tax_rate', 10, 2 );
add_filter( 'bpmj_wpfa_edd_tax_fee', 'bpmj_eddcm_tax_rate', 10, 2 );
add_filter( 'bpmj_wpifirma_edd_tax_product', 'bpmj_eddcm_tax_rate', 10, 2 );
add_filter( 'bpmj_wpifirma_edd_tax_fee', 'bpmj_eddcm_tax_rate', 10, 2 );
add_filter( 'bpmj_wpwf_edd_tax_product', 'tax_rate_for_wfirma', 10, 2 );
add_filter( 'bpmj_wpwf_edd_tax_fee', 'tax_rate_for_wfirma', 10, 2 );
add_filter( 'bpmj_wpinfakt_edd_tax_product', 'bpmj_eddcm_tax_rate', 10, 2 );
add_filter( 'bpmj_wpinfakt_edd_tax_fee', 'bpmj_eddcm_tax_rate', 10, 2 );
add_filter( 'bpmj_wptaxe_edd_tax_product', 'bpmj_eddcm_tax_rate', 10, 2 );
add_filter( 'bpmj_wptaxe_edd_tax_fee', 'bpmj_eddcm_tax_rate', 10, 2 );

function bpmj_eddcm_is_moss_eu( $payment ) {
	$payment_meta = $payment->get_meta();

	return bpmj_edd_invoice_data_get_cb_setting( 'edd_id_enable_vat_moss' ) &&
        $payment_meta['bpmj_edd_invoice_type'] === 'company' &&
        in_array( $payment_meta['user_info']['address']['country'], bpmj_eddcm_get_eu_contries_keys() );
}

function bpmj_eddcm_invoice_data_wpfa( $invoice_data, $purchase_id ) {
    $payment = new EDD_Payment( $purchase_id );
    if( ! empty( $payment->address['country'] ) ) {
        $invoice_data['buyer_country'] = $payment->address['country'];
    }

    if ( bpmj_eddcm_is_moss_eu( $payment ) ) {
        $additional_description = '';
        if ( isset( $invoice_data['description'] ) )
            $additional_description = $invoice_data['description'];

        $invoice_data['description'] = __( 'Reverse charge', BPMJ_EDDCM_DOMAIN ) . "\n" .  $additional_description;
    }

    if ( empty( $payment->payment_meta['bpmj_edd_invoice_check'] ) && ! empty( $payment->payment_meta['bpmj_edd_invoice_data_receipt_nip'] ) ) {
        $invoice_data['buyer_tax_no'] = $payment->payment_meta['bpmj_edd_invoice_data_receipt_nip'];
    }

    if ( ! empty( $payment->payment_meta['bpmj_edd_invoice_receiver_info_set'] ) ) {
        $invoice_data['recipient_name'] = $payment->payment_meta['bpmj_edd_invoice_receiver_name'] ?? '';
        $invoice_data['recipient_street'] = $payment->payment_meta['bpmj_edd_invoice_receiver_street'] ?? '';
        $invoice_data['recipient_post_code'] = $payment->payment_meta['bpmj_edd_invoice_receiver_postcode'] ?? '';
        $invoice_data['recipient_city'] = $payment->payment_meta['bpmj_edd_invoice_receiver_city'] ?? '';
    }

    return $invoice_data;
}
add_filter( 'bpmj_wpfa_edd_invoice_data', 'bpmj_eddcm_invoice_data_wpfa', 10, 2 );

function bpmj_eddcm_invoice_data_wpifirma( $invoice_data, $purchase_id ) {
    $payment = new EDD_Payment( $purchase_id );
    if( ! empty( $payment->address['country'] ) ) {
        $invoice_data['Kontrahent']['Kraj'] = $payment->address['country'];
    }

    if ( bpmj_eddcm_is_moss_eu( $payment ) ) {
        $additional_description = '';
        if ( isset( $invoice_data['Uwagi'] ) )
            $additional_description = $invoice_data['Uwagi'];

        $invoice_data['Uwagi'] = __( 'Reverse charge', BPMJ_EDDCM_DOMAIN ) . "\n" .  $additional_description;
    }

    if ( empty( $payment->payment_meta['bpmj_edd_invoice_check'] ) && ! empty( $payment->payment_meta['bpmj_edd_invoice_data_receipt_nip'] ) ) {
        $invoice_data['NIPKontrahenta'] = sanitize_text_field( $payment->payment_meta['bpmj_edd_invoice_data_receipt_nip'] );
    }

    return $invoice_data;
}
add_filter( 'bpmj_wpifirma_edd_invoice_data', 'bpmj_eddcm_invoice_data_wpifirma', 10, 2 );

function bpmj_eddcm_invoice_data_wpwfirma( $invoice_data, $purchase_id ) {
    $payment = new EDD_Payment( $purchase_id );

    if ( bpmj_eddcm_is_moss_eu( $payment ) ) {
        $additional_description = '';
        if ( isset( $invoice_data['description'] ) )
            $additional_description = $invoice_data['description'];

        $invoice_data['description'] = __( 'Reverse charge', BPMJ_EDDCM_DOMAIN ) . "\n" .  $additional_description;
    }

    if ( empty( $payment->payment_meta['bpmj_edd_invoice_check'] ) && ! empty( $payment->payment_meta['bpmj_edd_invoice_data_receipt_nip'] ) ) {
        $invoice_data['nip'] = sanitize_text_field($payment->payment_meta['bpmj_edd_invoice_data_receipt_nip']);
    }

    return $invoice_data;
}
add_filter( 'bpmj_wpwf_edd_invoice_data', 'bpmj_eddcm_invoice_data_wpwfirma', 10, 2 );

function bpmj_eddcm_contractor_wpwfirma( $contractor, $purchase_id ) {
    $payment = new EDD_Payment( $purchase_id );
    if( ! empty( $payment->address['country'] ) ) {
        $contractor['country'] = $payment->address['country'];
		if( 'PL' != $contractor['country'] && 'nip' == $contractor[ 'tax_id_type' ] ) {

            $tax_id_type = 'custom';

            if ( bpmj_eddcm_is_moss_eu( $payment ) ) {
                $tax_id_type = 'vat';
            }

            $contractor[ 'tax_id_type' ] = $tax_id_type;
		}
    }

	// wfirma requires NIP without spaces
	if( !empty( $contractor['nip'] ) ) {
		$contractor['nip'] = str_replace( ' ', '', $contractor['nip'] );
	}

	return $contractor;
}
add_filter( 'bpmj_wpwf_contractor', 'bpmj_eddcm_contractor_wpwfirma', 10, 2 );


function bpmj_eddcm_invoice_data_wpinfakt( $invoice_data, $purchase_id ) {
    $payment = new EDD_Payment( $purchase_id );
    $payment_meta = $payment->get_meta();
    if( ! empty( $payment->address['country'] ) ) {
        $invoice_data['client_country'] = $payment->address['country'];
		if( is_numeric( substr($invoice_data['client_tax_code'], 0, 2 ) ) ) {
			$invoice_data['client_tax_code'] = $payment_meta['user_info']['address']['country'] . $invoice_data['client_tax_code'];
		}
    }

    if ( bpmj_eddcm_is_moss_eu( $payment ) ) {
        $additional_description = '';
        if ( isset( $invoice_data['notes'] ) )
            $additional_description = $invoice_data['notes'];

        $invoice_data['notes'] = __( 'Reverse charge', BPMJ_EDDCM_DOMAIN ) . "\n" .  $additional_description;
    }

    if ( empty( $payment->payment_meta['bpmj_edd_invoice_check'] ) && ! empty( $payment->payment_meta['bpmj_edd_invoice_data_receipt_nip'] ) ) {
        $invoice_data['client_tax_code'] = sanitize_text_field( $payment->payment_meta['bpmj_edd_invoice_data_receipt_nip'] );
    }

    return $invoice_data;
}
add_filter( 'bpmj_wpinfakt_edd_invoice_data', 'bpmj_eddcm_invoice_data_wpinfakt', 10, 2 );

function bpmj_eddcm_invoice_data_wptaxe( $invoice_data, $purchase_id ) {
    $payment = new EDD_Payment( $purchase_id );
    if( ! empty( $payment->address['country'] ) ) {
        $invoice_data['buyer']['countryCode'] = $payment->address['country'];
    }

    if ( bpmj_eddcm_is_moss_eu( $payment ) ) {
        $invoice_data['invoiceSubType'] = 'ODW';
    }

    return $invoice_data;
}
add_filter( 'bpmj_wptaxe_edd_invoice_data', 'bpmj_eddcm_invoice_data_wptaxe', 10, 2 );

function bpmj_eddcm_vat_moss_fields( $show ) {
    if ( bpmj_edd_invoice_data_get_cb_setting( 'edd_id_enable_vat_moss' ) && WPI()->packages->has_higher_than_plus() ) return true;

	return $show;
}
add_filter( 'eddid_form_show_country', 'bpmj_eddcm_vat_moss_fields' );

function bpmj_eddcm_p24_gateway_args( $args, $purchase_data ) {
    $post_data = $purchase_data['post_data'];
    if ( isset( $post_data['bpmj_edd_invoice_data_invoice_check'] ) && $post_data['bpmj_edd_invoice_data_invoice_check'] == 1 ) {
        if ( isset( $post_data['bpmj_edd_invoice_data_invoice_postcode'] ) && ! empty( $post_data['bpmj_edd_invoice_data_invoice_postcode'] ) ) {
            $args['p24_zip'] = $post_data['bpmj_edd_invoice_data_invoice_postcode'];
        }

        if (isset($post_data['bpmj_edd_invoice_data_invoice_city']) && !empty($post_data['bpmj_edd_invoice_data_invoice_city'])){
            $args['p24_city'] = $post_data['bpmj_edd_invoice_data_invoice_city'];
        }

        $data_invoice_apartment_number = '';

        if (isset($post_data['bpmj_edd_invoice_data_invoice_apartment_number']) && !empty($post_data['bpmj_edd_invoice_data_invoice_apartment_number'])){
            $data_invoice_apartment_number = ' / '.$post_data['bpmj_edd_invoice_data_invoice_apartment_number'];
        }

        $args['p24_address'] = $post_data['bpmj_edd_invoice_data_invoice_street'] .' '. $post_data['bpmj_edd_invoice_data_invoice_building_number'] . $data_invoice_apartment_number;
    }

    return $args;
}
add_filter( 'bpmj_edd_p24_gateway_args', 'bpmj_eddcm_p24_gateway_args', 10, 2 );

add_filter( 'edd_update_payment_meta__edd_payment_total', function ( $meta_value, $id ) {
    if ( System::is_decimal_point_comma() )
        return str_replace( ',', '.', $meta_value );

    return $meta_value;
}, 10, 2 );

add_filter( 'edd_sanitize_amount_amount', function ( $amount ) {
    if ( System::is_decimal_point_comma() ) {
        return str_replace(',', '.', $amount);
    }

    return $amount;
} );