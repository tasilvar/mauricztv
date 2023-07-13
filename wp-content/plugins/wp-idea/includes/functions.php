<?php

use bpmj\wpidea\events\actions\Action_Name;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

include_once __DIR__ . '/buy-as-gift/functions.php';

/**
 * Get Mailer Lists
 *
 * @param string $name
 * @param string $data_type
 *
 * @return array
 */
function bpmj_wpid_get_mailer_data( $name, $data_type = 'list' ) {
	global $edd_options;

	switch ( $name ) {

		// MAILCHIMP
		case 'edd-mailchimp':
			if ( class_exists( 'EDD_MailChimp' ) ) {
				$mailchimp = new EDD_MailChimp();

				return $mailchimp->get_lists();
			}
			break;


		// ActiveCampaign
		case 'edd-activecampaign':
			if ( class_exists( '\bpmj\wp\eddact\Plugin' ) ) {
				$activecampaign = \bpmj\wp\eddact\Plugin::instance()->get_activecampaign_handler();
				$data      = $data_type === 'list' ? $activecampaign->get_lists() : $activecampaign->get_forms();

				return $data;
			}
			break;

		// MAILERLITE
		case 'edd-mailerlite':
			if ( class_exists( 'BPMJ_EDD_Mailerlite' ) ) {
				$mailerlite = new BPMJ_EDD_Mailerlite();

				return $mailerlite->get_lists();
			}
			break;


		// Interspire
		case 'edd-interspire':
			if ( class_exists( 'BPMJ_EDD_Interspire' ) ) {
				$api = new BPMJ_EDD_Interspire();

				return $api->get_lists();
			}
			break;

		// FreshMail
		case 'edd-freshmail':
			if ( class_exists( '\bpmj\wp\eddfm\Plugin' ) ) {
				$freshmail = \bpmj\wp\eddfm\Plugin::instance()->get_freshmail_handler();
				$lists     = $freshmail->get_lists();

				return $lists;
			}
			break;

		// GetResponse
		case 'edd-getresponse':
			if ( class_exists( '\bpmj\wp\eddres\Plugin' ) ) {
				$getresponse = \bpmj\wp\eddres\Plugin::instance()->get_getresponse_handler();
				$data       = $data_type === 'tags' ? $getresponse->get_tags() : $getresponse->get_lists();

				return $data;
			}
			break;

		// ConvertKit
		case 'edd-convertkit':
			/** @var EDD_ConvertKit $edd_convert_kit */
			global $edd_convert_kit;
			if ( isset( $edd_convert_kit ) ) {
			    $data = $data_type === 'tags' ? $edd_convert_kit->get_tags() : $edd_convert_kit->get_lists();

			    return $data;
			}
			break;
	}

	return array();
}

/**
 * @param string $name
 *
 * @return array
 */
function bpmj_wpid_get_mailer_settings( $name ) {
	switch ( $name ) {
		case 'edd-activecampaign':
			if ( class_exists( '\bpmj\wp\eddact\Plugin' ) ) {
				$activecampaign = \bpmj\wp\eddact\Plugin::instance()->get_activecampaign_handler();
				$settings  = $activecampaign->settings( array() );
				if ( isset( $settings[ 'activecampaign' ] ) ) {
					$settings = $settings[ 'activecampaign' ];
				}
				unset( $settings[ 0 ] );
				$ac_settings = array();
				foreach ( $settings as $setting ) {
					if ( isset( $setting[ 'id' ] ) ) {
						$ac_settings[ $setting[ 'id' ] ] = $setting;
					}
				}

				return $ac_settings;
			}
			break;

		case 'edd-freshmail':
			if ( class_exists( '\bpmj\wp\eddfm\Plugin' ) ) {
				$freshmail = \bpmj\wp\eddfm\Plugin::instance()->get_freshmail_handler();
				$settings  = $freshmail->settings( array() );
				if ( isset( $settings[ 'freshmail' ] ) ) {
					$settings = $settings[ 'freshmail' ];
				}
				unset( $settings[ 0 ] );
				$fm_settings = array();
				foreach ( $settings as $setting ) {
					if ( isset( $setting[ 'id' ] ) ) {
						$fm_settings[ $setting[ 'id' ] ] = $setting;
					}
				}

				return $fm_settings;
			}
			break;

		case 'edd-getresponse':
			if ( class_exists( '\bpmj\wp\eddres\Plugin' ) ) {
				$getresponse = \bpmj\wp\eddres\Plugin::instance()->get_getresponse_handler();
				$settings  = $getresponse->settings( array() );
				if ( isset( $settings[ 'getresponse' ] ) ) {
					$settings = $settings[ 'getresponse' ];
				}
				unset( $settings[ 0 ] );
				$gr_settings = array();
				foreach ( $settings as $setting ) {
					if ( isset( $setting[ 'id' ] ) ) {
						$gr_settings[ $setting[ 'id' ] ] = $setting;
					}
				}

				return $gr_settings;
			}
			break;

		case 'edd-ipresso':
			if ( class_exists( '\bpmj\wp\eddip\Plugin' ) ) {
				$ipresso  = \bpmj\wp\eddip\Plugin::instance()->get_ipresso_handler();
				$settings = $ipresso->settings( array() );
				if ( isset( $settings[ 'ipresso' ] ) ) {
					$settings = $settings[ 'ipresso' ];
				}
				unset( $settings[ 0 ] );
				$ip_settings = array();
				foreach ( $settings as $setting ) {
					if ( isset( $setting[ 'id' ] ) ) {
						$ip_settings[ $setting[ 'id' ] ] = $setting;
					}
				}

				return $ip_settings;
			}
			break;

		case 'edd-convertkit':
			/** @var EDD_ConvertKit $edd_convert_kit */
			global $edd_convert_kit;
			if ( isset( $edd_convert_kit ) ) {
				$settings = $edd_convert_kit->settings( array() );
				if ( isset( $settings[ 'convertkit' ] ) ) {
					$settings = $settings[ 'convertkit' ];
				}
				unset( $settings[ 0 ] );
				$ck_settings = array();
				foreach ( $settings as $setting ) {
					if ( isset( $setting[ 'id' ] ) ) {
						$ck_settings[ $setting[ 'id' ] ] = $setting;
					}
				}

				return $ck_settings;
			}
			break;
	}

	return array();
}

function bpmj_courses_struct() {
	global $post;

	$struct = array();

	$ancestors   = get_post_ancestors( $post->ID );
	$top         = ( $ancestors ) ? $ancestors[ count( $ancestors ) - 1 ] : $post->ID;
	$struct[ 0 ] = array(
		'id'    => $top,
		'pid'   => 0,
		'link'  => get_permalink( $top ),
		'title' => get_the_title( $top )
	);
	$struct[ 1 ] = array();
	$struct[ 2 ] = array();

	$course                         = WPI()->courses->get_course_by_page( $top );
	$inaccessible_lesson_visibility = WPI()->courses->get_inaccessible_lesson_display_mode( $course->ID );

	$childpages = new WP_Query( array(
		'post_type'      => 'page',
		'post_parent'    => $top,
		'posts_per_page' => - 1,
		'order'          => 'ASC',
		'orderby'        => 'menu_order'
	) );

	while ( $childpages->have_posts() ) {

		$childpages->the_post();
		$this_subpage   = $post->ID;
		$access         = bpmj_eddpc_user_can_access( false, bpmj_eddpc_is_restricted( $post->ID ), $post->ID );
		$access_valid   = 'valid' === $access[ 'status' ];
		$access_waiting = 'waiting' === $access[ 'status' ];
		if ( $access_valid || $access_waiting && ( 'visible' === $inaccessible_lesson_visibility || 'grayed' === $inaccessible_lesson_visibility ) ) {
			$struct[ 1 ][] = array(
				'id'     => $this_subpage,
				'pid'    => $top,
				'link'   => $access_waiting && 'grayed' === $inaccessible_lesson_visibility ? 'javascript:' : get_the_permalink(),
				'title'  => get_the_title(),
				'grayed' => $access_waiting && 'grayed' === $inaccessible_lesson_visibility,
			);

			$subpages = new WP_Query( array(
				'post_type'      => 'page',
				'post_parent'    => $this_subpage,
				'posts_per_page' => - 1,
				'order'          => 'ASC',
				'orderby'        => 'menu_order'
			) );

			while ( $subpages->have_posts() ) {
				$subpages->the_post();
				$access = bpmj_eddpc_user_can_access( false, bpmj_eddpc_is_restricted( $post->ID ), $post->ID );
				if ( 'valid' === $access[ 'status' ] || 'waiting' === $access[ 'status' ] && ( 'visible' === $inaccessible_lesson_visibility || 'grayed' === $inaccessible_lesson_visibility ) ) {
					$struct[ 2 ][] = array(
						'id'     => $post->ID,
						'pid'    => $this_subpage,
						'link'   => 'waiting' === $access[ 'status' ] && 'grayed' === $inaccessible_lesson_visibility ? 'javascript:' : get_the_permalink(),
						'title'  => get_the_title(),
						'grayed' => 'waiting' === $access[ 'status' ] && 'grayed' === $inaccessible_lesson_visibility,
					);
				}
			}
		}

		wp_reset_query();
	}

	wp_reset_query();

	return $struct;
}

/**
 * @param string $license_key
 * @param string $item_name
 * @param string $license_status_name
 * @param bool $force_check
 *
 * @return bool
 */
function bpmj_eddcm_check_license( $license_key, $item_name, $license_status_name, $force_check = false ) {

	if ( ! ( empty( $license_key ) ) ) {

		$action         = 'activate_license';
		$license_status = get_option( $license_status_name );
		if ( $force_check || 'valid' === $license_status ) {
			$action = 'check_license';
		}

		$license_data = bpmj_eddcm_get_license_data( $license_key, $item_name, $action );
		if ( false === $license_data ) {
			return false;
		}

		if ( isset( $license_data[ 'license' ] ) ) {
		    if('invalid' === $license_data[ 'license' ] && 'expired' === $license_data['error']) {
                $license_data[ 'license' ] = $license_data['error'];
            }

			update_option( $license_status_name, $license_data[ 'license' ] );

			return $license_data[ 'license' ];
		}
	} else {
		update_option( $license_status_name, '' );
	}

	return false;
}

/**
 * @param string $license_key
 * @param string $item_name
 * @param string $action
 *
 * @return array|bool
 */
function bpmj_eddcm_get_license_data( $license_key, $item_name, $action = 'check_license' ) {
	if ( empty( $license_key ) ) {
		return false;
	}

	$api_params = array(
		'edd_action' => $action,
		'license'    => $license_key,
		'item_name'  => urlencode( $item_name ),
	);

	$response = wp_remote_get( add_query_arg( $api_params, BPMJ_UPSELL_STORE_URL ), array(
		'timeout'     => 15,
		'sslverify'   => false,
		'redirection' => 5
	) );

	if ( ! is_wp_error( $response ) ) {
		$license_data = json_decode( wp_remote_retrieve_body( $response ), true );

        do_action( 'activate_telemetry_by_customer_email', $license_data['customer_email'] );

		return $license_data;
	}

	return false;
}

function bpmj_eddcm_reload_layout_template_settings() {
	// Need to grab current template settings somehow
	$wpidea_settings          = get_option( WPI()->settings->get_settings_slug() );
	$layout_settings_slug     = WPI()->settings->get_layout_template_settings_slug();
	$layout_settings          = get_option( $layout_settings_slug );
	$template                 = $wpidea_settings[ 'template' ] ?? 'scarlet';
	$layout_template_settings_original = $layout_settings[ $template ] ?? [];
	$layout_template_settings = apply_filters( 'bpmj_eddcm_layout_filter_settings', $layout_template_settings_original);
	if ( $layout_template_settings_original !== $layout_template_settings ) {
		// Filter changed something - we need to save the options to DB
	    $layout_settings[ $template ] = $layout_template_settings;
		update_option( $layout_settings_slug, $layout_settings );
	}
	do_action( 'bpmj_eddcm_layout_template_settings_save', $layout_template_settings );
	do_action( 'bpmj_eddcm_layout_template_settings_regenerate', $layout_template_settings );
}

/**
 * Check if the logged in user (if any) is a subscriber
 *
 * @return bool
 */
function bpmj_eddcm_is_user_a_subscriber() {
	if ( ! is_user_logged_in() ) {
		return false;
	}
	$user = wp_get_current_user();
	if ( empty( $user->roles ) || 1 === count( $user->roles ) && in_array( current( $user->roles ), array(
            'subscriber'
        ) ) ) {
		return true;
	}

	return false;
}

/**
 * Check if the logged in user (if any) is a subscriber
 *
 * @return bool
 */
function bpmj_eddcm_is_user_a_partner() {
	if ( ! is_user_logged_in() ) {
		return false;
	}
	$user = wp_get_current_user();
	if ( empty( $user->roles ) || 1 === count( $user->roles ) && in_array( current( $user->roles ), array(
			'lms_partner'
	) ) ) {
		return true;
	}
	
	return false;
}

/**
 * @param string $key
 * @param mixed $default
 *
 * @return mixed
 */
function bpmj_eddcm_get_option( $key, $default = null ) {
	$wpidea_settings = get_option( 'wp_idea' );
	if ( isset( $wpidea_settings[ $key ] ) ) {
		return $wpidea_settings[ $key ];
	}

	return $default;
}

/**
 * @return bool
 */
function bpmj_eddcm_enable_edd() {
	return WPI()->packages->has_shop() || 'on' === bpmj_eddcm_get_option( 'enable_edd' );
}

/**
 * Returns payment's user id taking customer into consideration (standard edd_get_payment_user_id returns
 * the user that entered the payment, not necessarily the customer)
 *
 * @param int $payment_id
 *
 * @return string
 */
function bpmj_eddcm_get_payment_user_id( $payment_id ) {
	$customer_id = edd_get_payment_customer_id( $payment_id );
	if ( $customer_id ) {
		$customer = new EDD_Customer( $customer_id );
		if ( $customer->user_id ) {
			return (int)$customer->user_id;
		}
	}

	return (int)edd_get_payment_user_id( $payment_id );
}

/**
 * @param int $product_id
 * @param array|null $variable_prices
 */
function bpmj_eddcm_set_overall_purchase_limits( $product_id, $variable_prices = null ) {
	if ( ! $variable_prices ) {
		$variable_prices = edd_get_variable_prices( $product_id );
	}

	$purchase_limit            = 0;
	$purchase_limit_items_left = 0;
	$any_empty_purchase_limit  = false;

	foreach ( $variable_prices as $price ) {
		if ( ! empty( $price[ 'bpmj_eddcm_purchase_limit' ] ) ) {
			$purchase_limit += (int) $price[ 'bpmj_eddcm_purchase_limit' ];
		}
		if ( ! empty( $price[ 'bpmj_eddcm_purchase_limit_items_left' ] ) ) {
			$purchase_limit_items_left += (int) $price[ 'bpmj_eddcm_purchase_limit_items_left' ];
		}
		if ( empty( $price[ 'bpmj_eddcm_purchase_limit' ] ) ) {
			$any_empty_purchase_limit = true;
		}
	}

	$purchase_limit_unlimited = false;
	if ( $any_empty_purchase_limit && $purchase_limit > 0 ) {
		$purchase_limit_unlimited = true;
	}

	update_post_meta( $product_id, '_bpmj_eddcm_purchase_limit', $purchase_limit );
	update_post_meta( $product_id, '_bpmj_eddcm_purchase_limit_items_left', $purchase_limit_items_left );
	update_post_meta( $product_id, '_bpmj_eddcm_purchase_limit_unlimited', $purchase_limit_unlimited ? '1' : '' );
}

/**
 * @param int $product_id
 * @param int $price_id
 * @param int $quantity
 * @param int $payment_id
 *
 * @return int
 */
function bpmj_eddcm_change_items_left_in_purchase_limit( $product_id, $price_id = 0, $quantity = 1, $payment_id = null ) {
	$has_variable_prices = edd_has_variable_prices( $product_id );
	if ( ! $quantity || $price_id > 0 && ! $has_variable_prices || 0 === $price_id && $has_variable_prices ) {
		return 0;
	}
	if ( bpmj_eddcm_should_ignore_purchase_limits( $payment_id, $product_id, $price_id ) ) {
		return 0;
	}
	if ( $price_id ) {
		$variable_prices = get_post_meta( $product_id, 'edd_variable_prices', true );
		if ( ! is_array( $variable_prices ) ) {
			return 0;
		}
		$variable_price = isset( $variable_prices[ $price_id ] ) ? $variable_prices[ $price_id ] : null;
		if ( ! $variable_price ) {
			return 0;
		}
		$price_purchase_limit            = (int) ( isset( $variable_price[ 'bpmj_eddcm_purchase_limit' ] ) ? $variable_price[ 'bpmj_eddcm_purchase_limit' ] : 0 );
		$price_purchase_limit_items_left = (int) ( isset( $variable_price[ 'bpmj_eddcm_purchase_limit_items_left' ] ) ? $variable_price[ 'bpmj_eddcm_purchase_limit_items_left' ] : 0 );
		if ( $quantity > 0 ) {
			if ( $price_purchase_limit > 0 && $price_purchase_limit > $price_purchase_limit_items_left ) {
				$quantity = min( $quantity, $price_purchase_limit - $price_purchase_limit_items_left );
			} else {
				return 0;
			}
		} else {
			if ( $price_purchase_limit > 0 && $price_purchase_limit_items_left > 0 ) {
				$quantity = - 1 * min( abs( $quantity ), $price_purchase_limit_items_left );
			} else {
				return 0;
			}
		}
		if ( ! $quantity ) {
			return 0;
		}

		$variable_prices[ $price_id ][ 'bpmj_eddcm_purchase_limit_items_left' ] += $quantity;
		update_post_meta( $product_id, 'edd_variable_prices', $variable_prices );

		bpmj_eddcm_set_overall_purchase_limits( $product_id, $variable_prices );

        do_action(Action_Name::PURCHASE_LIMIT_UPDATED);

		return $quantity;
	}


	$product_purchase_limit            = (int) get_post_meta( $product_id, '_bpmj_eddcm_purchase_limit', true );
	$product_purchase_limit_items_left = (int) get_post_meta( $product_id, '_bpmj_eddcm_purchase_limit_items_left', true );
	$product_purchase_limit_unlimited  = '1' === get_post_meta( $product_id, '_bpmj_eddcm_purchase_limit_unlimited', true );
	if ( $product_purchase_limit <= 0 || $product_purchase_limit_unlimited ) {
		return 0;
	}
	if ( $quantity > 0 ) {
		if ( $product_purchase_limit > 0 && $product_purchase_limit > $product_purchase_limit_items_left ) {
			$quantity = min( $quantity, $product_purchase_limit - $product_purchase_limit_items_left );
		} else {
			return 0;
		}
	} else {
		if ( $product_purchase_limit > 0 && $product_purchase_limit_items_left > 0 ) {
			$quantity = - 1 * min( abs( $quantity ), $product_purchase_limit_items_left );
		} else {
			return 0;
		}
	}

	if ( $quantity !== 0 ) {
		update_post_meta( $product_id, '_bpmj_eddcm_purchase_limit_items_left', $product_purchase_limit_items_left + $quantity );
	}

	return $quantity;
}

/**
 * @param int $product_id
 * @param int $price_id
 * @param int $quantity
 * @param int $payment_id
 *
 * @return int
 */
function bpmj_eddcm_decrease_items_left_in_purchase_limit( $product_id, $price_id = 0, $quantity = 1, $payment_id = null ) {
	return abs( bpmj_eddcm_change_items_left_in_purchase_limit( $product_id, $price_id, - $quantity, $payment_id ) );
}

/**
 * @param int $product_id
 * @param int $price_id
 * @param int $quantity
 * @param int $payment_id
 *
 * @return int
 */
function bpmj_eddcm_increase_items_left_in_purchase_limit( $product_id, $price_id = 0, $quantity = 1, $payment_id = null ) {
	return bpmj_eddcm_change_items_left_in_purchase_limit( $product_id, $price_id, $quantity, $payment_id );
}

/**
 * Checks if the product can be purchased
 *
 * @param int $product_id
 * @param int $price_id
 *
 * @return bool
 */
function bpmj_eddcm_can_purchase_product_check( $product_id, $price_id = 0 ) {
	$course = WPI()->courses->get_course_by_product( $product_id );
	
	if(false === $course) {
	    if( 'bundle' === get_post_meta($product_id, '_edd_product_type', true) ) {
	        $product_type = 'bundle';
	    }
	    else {
	        $product_type = 'service_or_digital_prod';
	    }
	    $sales_status = WPI()->courses->get_sales_status( $product_id, $product_id );
	}
	else {
	    $product_type = 'course';
	    $sales_status = WPI()->courses->get_sales_status( $course->ID, $product_id );
	}

	if ( 'disabled' === $sales_status[ 'status' ] ) {
	    return false;
	}
	
	if('service_or_digital_prod' != $product_type) {
    	if ( $price_id ) {
    		$variable_prices = get_post_meta( $product_id, 'edd_variable_prices', true );
    		if ( ! is_array( $variable_prices ) ) {
    			return true;
    		}
    		$variable_price = isset( $variable_prices[ $price_id ] ) ? $variable_prices[ $price_id ] : null;
    		if ( ! $variable_price ) {
    			return true;
    		}
    		$price_purchase_limit            = (int) ( isset( $variable_price[ 'bpmj_eddcm_purchase_limit' ] ) ? $variable_price[ 'bpmj_eddcm_purchase_limit' ] : 0 );
    		$price_purchase_limit_items_left = (int) ( isset( $variable_price[ 'bpmj_eddcm_purchase_limit_items_left' ] ) ? $variable_price[ 'bpmj_eddcm_purchase_limit_items_left' ] : 0 );
    		if ( $price_purchase_limit > 0 && $price_purchase_limit_items_left <= 0 ) {
    			return false;
    		}
    	}
	}

	return true;
}

/**
 * Checks if the product can be purchased
 *
 * @param int $product_id
 * @param int $price_id
 *
 * @return bool
 */
function bpmj_eddcm_can_purchase_product( $product_id, $price_id = 0 ) {
	$ret = bpmj_eddcm_can_purchase_product_check( $product_id, $price_id );

	return apply_filters( 'bpmj_eddcm_can_purchase_product', $ret, $product_id, $price_id );
}

/**
 * @param array $excluded_tags
 *
 * @return array
 */
function bpmj_eddcm_edd_email_tags_without( array $excluded_tags ) {
	$edd_email_tags          = edd_get_email_tags();
    if( empty( $edd_email_tags ) ) {
        return [];
    }
	$edd_email_tags_filtered = [];
	foreach ( $edd_email_tags as $tag ) {
		if ( ! in_array( $tag[ 'tag' ], $excluded_tags ) ) {
			$edd_email_tags_filtered[] = $tag;
		}
	}

	return $edd_email_tags_filtered;
}

/**
 * @param array $tags
 *
 * @return string
 */
function bpmj_eddcm_template_tag_list( array $tags ) {
	$tag_list = array();
	foreach ( $tags as $tag ) {
		$tag_string = sprintf( '<code>{%s}</code> - %s', $tag[ 'tag' ], $tag[ 'description' ] );
		if ( ! empty( $tag[ 'bold' ] ) ) {
			$tag_string = '<strong>' . $tag_string . '</strong>';
		}
		$tag_list[] = $tag_string;
	}

	return implode( '<br />', $tag_list );
}

/**
 * @param int $payment_id
 * @param int $download_id
 *
 * @return int
 */
function bpmj_eddcm_get_price_id_in_payment( $payment_id, $download_id ) {
	$payment  = new EDD_Payment( $payment_id );
	$products = $payment->downloads;
	$price_id = 0;
	if ( ! empty( $products ) ) {
		foreach ( $products as $product ) {
			if ( $product[ 'id' ] == $download_id ) {
				if ( isset( $product[ 'options' ][ 'price_id' ] ) ) {
					$price_id = $product[ 'options' ][ 'price_id' ];
				}
				break;
			}
		}
	}

	return $price_id;
}

/**
 * @param int $payment_id
 * @param int $download_id
 * @param int $price_id
 *
 * @return bool
 */
function bpmj_eddcm_should_ignore_purchase_limits( $payment_id, $download_id, $price_id ) {
	$ret = apply_filters( 'bpmj_eddcm_should_ignore_purchase_limits', false, $payment_id, $download_id, $price_id );

	return $ret;
}

/**
 * @param int $product_id
 * @param int $price_id
 *
 * @return bool|int
 */
function bpmj_eddcm_is_item_in_cart( $product_id, $price_id = null ) {
	foreach ( edd_get_cart_contents() as $cart_key => $item ) {
		$cart_product_id = $item[ 'id' ];
		$cart_price_id   = isset( $item[ 'options' ][ 'price_id' ] ) ? $item[ 'options' ][ 'price_id' ] : 0;
		if ( $cart_product_id == $product_id && ( is_null( $price_id ) || $cart_price_id == $price_id ) ) {
			return $cart_key;
		}
	}

	return false;
}

/**
 * @param string $file
 * @param string $template
 *
 * @return string
 */
function bpmj_eddcm_template_get_file( $file, $template = null ) {
	return WPI()->templates->get_template_url( $template ) . '/' . ltrim( $file, '/' );
}

/**
 * @return string
 */
function bpmj_eddcm_get_current_user_name() {
	$customer = EDD()->session->get( 'customer' );
	$customer = wp_parse_args( $customer, array( 'first_name' => '', 'email' => '' ) );

	if( is_user_logged_in() && empty($customer['first_name']) && empty($customer['email']) ) {
		$user_data = get_userdata( get_current_user_id() );
		$customer['email'] = $user_data->user_email;
	}

	if (!empty($customer['first_name'])) {
		return $customer['first_name'];
	}

	if (false !== ($pos = strpos($customer['email'], '@'))) {
		return substr($customer['email'], 0, $pos);
	}

	return '';
}

function bpmj_eddcm_get_eu_contries_keys() {
    return $ue_countries = array( 'AT', 'BE', 'BG', 'HR', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR', 'GR', 'ES', 'NL', 'IE', 'LT', 'LU', 'LV', 'MT', 'DE', 'PT', 'RO', 'SK', 'SI', 'SE', 'HU', 'IT' );
}
