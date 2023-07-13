<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

include_once __DIR__ . '/buy-as-gift/actions.php';


use bpmj\wpidea\Helper;
use bpmj\wpidea\Course_Progress;
use bpmj\wpidea\settings\LMS_Settings;
use bpmj\wpidea\sales\product\core\event\Event_Name;

/**
 * Renders the Checkout Submit section
 *
 * @since 1.3.3
 * @return void
 */
function bpmj_eddcm_checkout_submit() {
	?>
    <div id="edd_purchase_submit">
		<?php do_action( 'edd_purchase_form_before_submit' ); ?>

		<?php edd_checkout_hidden_fields(); ?>

		<?php echo edd_checkout_button_purchase(); ?>

		<?php do_action( 'edd_purchase_form_after_submit' ); ?>

		<?php if ( edd_is_ajax_disabled() ) { ?>
            <p class="edd-cancel"><a
                        href="<?php echo edd_get_checkout_uri(); ?>"><?php _e( 'Go back', 'easy-digital-downloads' ); ?></a>
            </p>
		<?php } ?>
    </div>
	<?php
}

function bpmj_cm_edd_terms_agreement() {
	if ( edd_get_option( 'show_agree_to_terms', false ) ) {
		$agree_text  = edd_get_option( 'agree_text', '' );
		$agree_label = edd_get_option( 'agree_label', __( 'Agree to Terms?', 'easy-digital-downloads' ) );
		?>
        <fieldset id="edd_terms_agreement">
            <div class="edd-terms-agreement">
                <input name="edd_agree_to_terms" class="required" type="checkbox" id="edd_agree_to_terms" value="1"/>
                <label for="edd_agree_to_terms"><?php echo stripslashes( $agree_label ); ?></label>
            </div>
        </fieldset>
		<?php
	}
}

function bpmj_cm_remove_edd_actions() {
	remove_action( 'edd_purchase_form_before_submit', 'edd_terms_agreement' );
    if ( ! defined( 'BPMJ_EDDCM_SHOW_ACCESS_SETTINGS_ON_PAGES' ) ) {
        remove_action( 'add_meta_boxes', 'bpmj_eddpc_add_meta_box' );
    }
	add_action( 'edd_purchase_form_before_submit', 'bpmj_cm_edd_terms_agreement', 998 );
	remove_action( 'edd_purchase_form_after_cc_form', 'edd_checkout_submit', 9999 );
	add_action( 'edd_purchase_form_after_cc_form', 'bpmj_eddcm_checkout_submit', 9999 );
}

add_action( 'plugins_loaded', 'bpmj_cm_remove_edd_actions' );

function bpmj_cm_modify_invoice_check_behaviour() {
	?>
    <script type="text/javascript">
		jQuery( function () {
			jQuery( '#edd_checkout_wrap' ).on( "click", 'input[name="bpmj_edd_invoice_data_invoice_check"]', function () {
				var checked = jQuery( this ).is( ':checked' );
				var label = jQuery( this ).next( 'label' );
				if ( checked ) {
                    jQuery('#bpmj-eddcm-receipt-nip-wrap').slideUp();
					label.addClass( 'checked' );
				} else {
                    jQuery('#bpmj-eddcm-receipt-nip-wrap').slideDown(200);
					label.removeClass( 'checked' );
				}
			} );
		} );
    </script>
	<?php
}

add_action( 'edd_after_checkout_cart', 'bpmj_cm_modify_invoice_check_behaviour' );

function bpmj_eddmp_create_user( $payment_id, $payment_data ) {

	if ( ! class_exists( 'EDD_Auto_Register' ) ) {
		return;
	}

	$data = $_POST;

	if ( isset( $data[ 'edd_create_payment_nonce' ] ) && wp_verify_nonce( $data[ 'edd_create_payment_nonce' ], 'edd_create_payment_nonce' ) && $data[ 'edd-gateway' ] == 'manual_purchases' ) {

		$customer    = new EDD_Customer( $payment_data[ 'user_info' ][ 'email' ] );
		$payment_ids = explode( ',', $customer->payment_ids );
		if ( is_array( $payment_ids ) && ! empty( $payment_ids ) ) {
			$payment_ids = array_map( 'absint', $payment_ids );

			if ( 1 === count( $payment_ids ) && in_array( $payment_id, $payment_ids ) ) {
				remove_action( 'user_register', 'edd_connect_existing_customer_to_new_user', 10 );
				remove_action( 'user_register', 'edd_add_past_purchases_to_new_user', 10 );
			}
		}

		$user_id = edd_auto_register()->create_user( $payment_data, $payment_id );
	}

	if ( empty( $user_id ) || is_wp_error( $user_id ) ) {
		return;
	}
	$payment_meta                        = edd_get_payment_meta( $payment_id );
	$payment_meta[ 'user_info' ][ 'id' ] = $user_id;
	edd_update_payment_meta( $payment_id, '_edd_payment_user_id', $user_id );
	edd_update_payment_meta( $payment_id, '_edd_payment_meta', $payment_meta );
}

add_action( 'edd_insert_payment', 'bpmj_eddmp_create_user', 10, 2 );

function bpmj_cm_checkout_logged_or_identified() {
	$email  = '';
	$fname  = '';
	$lname  = '';
	$status = '';

	if ( is_user_logged_in() ) {
		$wp_user = wp_get_current_user();
		$email   = $wp_user->user_email;
		$fname   = $wp_user->user_firstname;
		$lname   = $wp_user->user_lastname;
		$status  = 'logged';
	} /*else if ( class_exists( 'WP_Tracker_and_Optimizer' ) ) {
		if ( TAO()->users->identified() ) {
			$tao_user = TAO()->users->get( TAO()->users->get_id() );
			$email    = $tao_user->email;
			$fname    = $tao_user->first_name;
			$lname    = $tao_user->last_name;
		}
	}*/
	?>
    <script>
		jQuery( document ).ready( function ( $ ) {
            <?php if ( $status == 'logged' ) { ?>
            $('#edd-email2-wrap').hide();
            $('#edd-email').prop("readonly", true);
            $('#edd-email').css('background-color', '#EBEBE4');
            $('#edd-email').val('<?php echo $email; ?>');
            <?php } ?>

            let cookie = getCookie('publigo_purchase_form_values') ?? {};
            if (Object.keys(cookie).length === 0 || cookie === '{}') {
                $('#edd-email').val('<?php echo $email; ?>');

                if (!$('#edd-first').val()) {
                    $('#edd-first').val('<?php echo $fname; ?>');
                }
                if (!$('#edd-last').val()) {
                    $('#edd-last').val('<?php echo $lname; ?>');
                }
            }
		} );
    </script>
	<?php
}

add_action( 'edd_purchase_form_user_info', 'bpmj_cm_checkout_logged_or_identified' );

function bpmj_eddcm_disable_default_profile_page() {
	global $wpidea_settings;

	if ( ! wp_doing_ajax() && bpmj_eddcm_is_user_a_subscriber() ) {
		// Subscribers get redirected to edd_profile page. If it doesn't exist, they will be redirected to home page
		$edd_profile_page = $wpidea_settings[ 'profile_editor_page' ];
		$permalink        = '';
		if ( $edd_profile_page ) {
			$permalink = get_permalink( $edd_profile_page );
		}
		if ( ! $permalink ) {
			$permalink = '/';
		}
		wp_redirect( $permalink );
		exit;
	}
}

add_action( 'admin_init', 'bpmj_eddcm_disable_default_profile_page' );

function bpmj_eddcm_purchase_form_after_email() {
	$wpidea_settings = get_option( WPI()->settings->get_settings_slug() );
	if ( ! empty( $wpidea_settings[ 'show_email2_on_checkout' ] ) && 'on' === $wpidea_settings[ 'show_email2_on_checkout' ] && ! is_user_logged_in() ):
		?>
        <p id="edd-email2-wrap">
            <label class="edd-label" for="edd-email-2"><?php _e( 'Repeat e-mail address', BPMJ_EDDCM_DOMAIN ); ?> <span
                        class="edd-required-indicator">*</span></label>
            <span
                    class="edd-description"><?php _e( 'Repeat e-mail address for verification.', BPMJ_EDDCM_DOMAIN ); ?></span>
            <input class="edd-input required" required type="text" name="edd_email_2" id="edd-email-2" value=""/>
        </p>
	<?php
	endif;
}

add_action( 'edd_purchase_form_after_email', 'bpmj_eddcm_purchase_form_after_email' );

function bpmj_eddcm_purchase_form_custom_user_fields() {
	$wpidea_settings    = get_option( WPI()->settings->get_settings_slug() );

	if ( ! empty( $wpidea_settings[ 'show_phone_number_field_on_checkout' ] ) && 'on' === $wpidea_settings[ 'show_phone_number_field_on_checkout' ] ):
		$phone_no_required = ! empty( $wpidea_settings[ 'phone_number_required_on_checkout' ] ) && 'on' === $wpidea_settings[ 'phone_number_required_on_checkout' ];
		$phone_no_value = '';
		$user_id        = get_current_user_id();
		if ( $user_id ) {
			$phone_no_raw = get_user_meta( $user_id, 'phone_no_raw', true );
			if ( $phone_no_raw ) {
				$phone_no_value = $phone_no_raw;
			} else {
				$phone_no_value = get_user_meta( $user_id, 'phone_no', true );
			}
		}
		?>
        <p>
            <label class="edd-label"
                   for="bpmj-eddcm-phone-no"><?php _e( 'Phone number', BPMJ_EDDCM_DOMAIN ); ?>
				<?php if ( $phone_no_required ): ?>
                    <span class="edd-required-indicator">*</span>
				<?php endif; ?>
            </label>
            <span
                    class="edd-description"><?php _e( 'Please provide a valid phone number (9-12 digits)', BPMJ_EDDCM_DOMAIN ); ?></span>
            <input class="edd-input <?php if ( $phone_no_required ): ?>required<?php endif; ?>" type="text"
                   <?php if ( $phone_no_required ): ?> required<?php endif; ?>
                   name="bpmj_eddcm_phone_no" id="bpmj-eddcm-phone-no"
                   value="<?php echo esc_attr( $phone_no_value ); ?>"
                   placeholder="+48 600 000 000"/>
        </p>
	<?php
	endif;

	if ( LMS_Settings::get_option('nip_for_receipts')) :
        $checked = false;
        if ( isset( $_COOKIE['edd_purchase_form_data'] ) && $_COOKIE['edd_purchase_form_data'] )
            $checked = true;

        ?>

        <p id="bpmj-eddcm-receipt-nip-wrap" style="<?php echo ( $checked ) ? 'display: none;' : ''; ?>">
            <label class="edd-label" for="bpmj-eddcm-receipt-nip">
                <?php _e( 'NIP (do paragonu)', BPMJ_EDDCM_DOMAIN ); ?>
            </label>
            <span class="edd-description">
                <?php _e( 'Please provide a valid NIP number', BPMJ_EDDCM_DOMAIN ); ?>
            </span>
            <input class="edd-input" type="text" name="bpmj_edd_invoice_data_receipt_nip" id="bpmj-eddcm-receipt-nip"/>
        </p>

    <?php
    endif;
}

add_action( 'edd_purchase_form_user_info_fields', 'bpmj_eddcm_purchase_form_custom_user_fields' );

function bpmj_eddcm_purchase_form_before_cc_form() {
	$wpidea_settings = get_option( WPI()->settings->get_settings_slug() );

	if ( ! empty( $wpidea_settings[ 'show_comment_field_on_checkout' ] ) && 'on' === $wpidea_settings[ 'show_comment_field_on_checkout' ] ):
		?>
        <p>
            <label class="edd-label"
                   for="bpmj-eddcm-order-comment"><?php _e( 'Additional information', BPMJ_EDDCM_DOMAIN ); ?>
            </label>
            <textarea class="edd-input" name="bpmj_eddcm_order_comment" id="bpmj-eddcm-order-comment"
                      placeholder="<?php _e( 'Additional information', BPMJ_EDDCM_DOMAIN ); ?>"></textarea>
        </p>
	<?php
	endif;
}

add_action( 'edd_purchase_form_before_cc_form', 'bpmj_eddcm_purchase_form_before_cc_form' );

function bpmj_eddcm_purchase_form_after_cc_form() {
	$wpidea_settings = get_option( WPI()->settings->get_settings_slug() );

	if ( ! empty( $wpidea_settings[ 'show_additional_checkbox_on_checkout' ] ) && 'on' === $wpidea_settings[ 'show_additional_checkbox_on_checkout' ] ):
		?>
        <fieldset class="bpmj-eddcm-custom-checkbox">
            <input type="checkbox" name="bpmj_eddcm_additional_checkbox" id="bpmj-eddcm-additional-checkbox" value="1"/>
            <label
                    for="bpmj-eddcm-additional-checkbox"><?php echo isset( $wpidea_settings[ 'additional_checkbox_description' ] ) ? $wpidea_settings[ 'additional_checkbox_description' ] : ''; ?>
            </label>
        </fieldset>
	<?php
	endif;

	if ( ! empty( $wpidea_settings[ 'show_additional_checkbox2_on_checkout' ] ) && 'on' === $wpidea_settings[ 'show_additional_checkbox2_on_checkout' ] ):
		?>
        <fieldset class="bpmj-eddcm-custom-checkbox">
            <input type="checkbox" name="bpmj_eddcm_additional_checkbox2" id="bpmj-eddcm-additional-checkbox2"
                   value="1"/>
            <label
                    for="bpmj-eddcm-additional-checkbox2"><?php echo isset( $wpidea_settings[ 'additional_checkbox2_description' ] ) ? $wpidea_settings[ 'additional_checkbox2_description' ] : ''; ?>
            </label>
        </fieldset>
	<?php
	endif;
}

add_action( 'edd_purchase_form_after_cc_form', 'bpmj_eddcm_purchase_form_after_cc_form' );

function bpmj_eddcm_edd_validate_phone_number($phone_no, $is_required)
{
	$phone_no_length   = strlen( $phone_no );
	$phone_no_min_length = 9;
	$phone_no_max_length = 15;

	if ( $is_required && empty( $phone_no ) ) {
		edd_set_error( 'invalid_phone_no', __( 'Please provide your phone number', BPMJ_EDDCM_DOMAIN ) );
		return false;
	} else if ( $phone_no && ( $phone_no_length < $phone_no_min_length || $phone_no_length > $phone_no_max_length ) ) {
		edd_set_error( 'invalid_phone_no', __( 'Please provide a valid phone number', BPMJ_EDDCM_DOMAIN ) );
		return false;
	}

	return true;
}

function bpmj_eddcm_edd_validate_custom_fields( $valid_data, $data ) {

	$wpidea_settings = get_option( WPI()->settings->get_settings_slug() );
	if ( ! empty( $wpidea_settings[ 'show_email2_on_checkout' ] ) && 'on' === $wpidea_settings[ 'show_email2_on_checkout' ] && ! is_user_logged_in() ) {
		if ( ! isset( $data[ 'edd_email_2' ] ) || $data[ 'edd_email_2' ] == '' && edd_field_is_required( 'edd_email' ) ) {
			edd_set_error( 'invalid_email_2', __( 'Please repeat the e-mail address', BPMJ_EDDCM_DOMAIN ) );
		} else if ( $data[ 'edd_email' ] != $data[ 'edd_email_2' ] ) {
			edd_set_error( 'invalid_email_2', __( 'The e-mail address and repeated e-mail address must be the same', BPMJ_EDDCM_DOMAIN ) );
		}
	}

	if ( ! empty( $wpidea_settings[ 'show_phone_number_field_on_checkout' ] ) && 'on' === $wpidea_settings[ 'show_phone_number_field_on_checkout' ] ) {
		$phone_no_required = ! empty( $wpidea_settings[ 'phone_number_required_on_checkout' ] ) && 'on' === $wpidea_settings[ 'phone_number_required_on_checkout' ];
		$phone_no          = isset( $data[ 'bpmj_eddcm_phone_no' ] ) ? preg_replace( '/[^\d]+/', '', $data[ 'bpmj_eddcm_phone_no' ] ) : '';

		bpmj_eddcm_edd_validate_phone_number($phone_no, $phone_no_required);
	}

	if ( ! empty( $wpidea_settings[ 'show_additional_checkbox_on_checkout' ] ) && 'on' === $wpidea_settings[ 'show_additional_checkbox_on_checkout' ] ) {
		$additional_checkbox_required    = ! empty( $wpidea_settings[ 'additional_checkbox_required' ] ) && 'on' === $wpidea_settings[ 'additional_checkbox_required' ];
		$additional_checkbox_checked     = isset( $data[ 'bpmj_eddcm_additional_checkbox' ] ) && '1' === $data[ 'bpmj_eddcm_additional_checkbox' ];
		$additional_checkbox_description = isset( $wpidea_settings[ 'additional_checkbox_description' ] ) ? $wpidea_settings[ 'additional_checkbox_description' ] : '';
		if ( ! $additional_checkbox_checked && $additional_checkbox_required ) {
			edd_set_error( 'invalid_custom_checkbox', sprintf( __( 'Please check "%s"', BPMJ_EDDCM_DOMAIN ), $additional_checkbox_description ) );
		}
	}

	if ( ! empty( $wpidea_settings[ 'show_additional_checkbox2_on_checkout' ] ) && 'on' === $wpidea_settings[ 'show_additional_checkbox2_on_checkout' ] ) {
		$additional_checkbox_required    = ! empty( $wpidea_settings[ 'additional_checkbox2_required' ] ) && 'on' === $wpidea_settings[ 'additional_checkbox2_required' ];
		$additional_checkbox_checked     = isset( $data[ 'bpmj_eddcm_additional_checkbox2' ] ) && '1' === $data[ 'bpmj_eddcm_additional_checkbox2' ];
		$additional_checkbox_description = isset( $wpidea_settings[ 'additional_checkbox2_description' ] ) ? $wpidea_settings[ 'additional_checkbox2_description' ] : '';
		if ( ! $additional_checkbox_checked && $additional_checkbox_required ) {
			edd_set_error( 'invalid_custom_checkbox2', sprintf( __( 'Please check "%s"', BPMJ_EDDCM_DOMAIN ), $additional_checkbox_description ) );
		}
	}

	if ( ! empty( $data[ 'bpmj_eddcm_buy_as_gift' ] ) ) {
		EDD()->session->set( 'bpmj_eddcm_gift', true );
	}
	else {
		EDD()->session->set( 'bpmj_eddcm_gift', false );
	}
}

add_action( 'edd_checkout_error_checks', 'bpmj_eddcm_edd_validate_custom_fields', 10, 2 );

function bpmj_eddcm_insert_payment( $payment_id ) {
	$wpidea_settings = get_option( WPI()->settings->get_settings_slug() );
	$purchase_data   = edd_get_purchase_session();
	if ( ! is_array( $purchase_data ) ) {
		$purchase_data = array();
	}
	$eddcm_purchase_data = array_intersect_key( $purchase_data, array_flip( array(
		'bpmj_eddcm_phone_no',
		'bpmj_eddcm_phone_no_raw',
		'bpmj_eddcm_additional_checkbox_checked',
		'bpmj_eddcm_additional_checkbox_description',
		'bpmj_eddcm_additional_checkbox2_checked',
		'bpmj_eddcm_additional_checkbox2_description',
		'bpmj_eddcm_order_comment',
		'bpmj_eddcm_buy_as_gift',
	) ) );
	edd_update_payment_meta( $payment_id, 'bpmj_eddcm_purchase_data', $eddcm_purchase_data );

	if ( ! empty( $eddcm_purchase_data[ 'bpmj_eddcm_phone_no' ] ) ) {
		$user_id = edd_get_payment_meta( $payment_id, '_edd_payment_user_id' );
		if ( $user_id ) {
			update_user_meta( $user_id, 'phone_no', $eddcm_purchase_data[ 'bpmj_eddcm_phone_no' ] );
			update_user_meta( $user_id, 'phone_no_raw', $eddcm_purchase_data[ 'bpmj_eddcm_phone_no_raw' ] );
		}
	}
}

add_action( 'edd_insert_payment', 'bpmj_eddcm_insert_payment', 99 );


/**
 * @param int $payment_id
 */
function bpmj_eddcm_handle_purchase_limit_on_insert_payment( $payment_id, $payment_data ) {
	$payu_recurrent_sequence_number = get_post_meta( $payment_id, '_payu_recurrent_sequence_number', true );
	if ( ! empty( $payu_recurrent_sequence_number ) && $payu_recurrent_sequence_number > 1 ) {
		// If it's a recurring payment don't decrease purchase limits
		return;
	}

	$cart_details = edd_get_payment_meta_cart_details( $payment_id );
	$product_ids  = array();
	if ( is_array( $cart_details ) ) {
		foreach ( $cart_details as $download ) {
			if ( 'bundle' === edd_get_download_type( $download[ 'id' ] ) ) {
				$bundled_products = edd_get_bundled_products( $download[ 'id' ] );
				if ( ! empty( $bundled_products ) && is_array( $bundled_products ) ) {
					foreach ( $bundled_products as $bundled_product_id ) {
						$product_ids[ $bundled_product_id ] = 0;
					}
				}
				if ( isset( $download[ 'item_number' ][ 'options' ][ 'price_id' ] ) ) {
				    $product_ids[ $download[ 'id' ] ] = (int) $download[ 'item_number' ][ 'options' ][ 'price_id' ];
				}
			} else {
				if ( isset( $download[ 'item_number' ][ 'options' ][ 'price_id' ] ) ) {
					$product_ids[ $download[ 'id' ] ] = (int) $download[ 'item_number' ][ 'options' ][ 'price_id' ];
				}
			}
		}
	}

	if ( ! empty( $product_ids ) ) {
		update_post_meta( $payment_id, '_bpmj_eddcm_purchase_limits', $product_ids );
		if ( 'BEGIN_PAYMENT' === WPI()->settings->get_purchase_limit_behaviour() || ! empty( $payment_data[ 'status' ] ) && in_array( $payment_data[ 'status' ], array(
				'publish',
				'completed',
				'complete'
			) ) ) {
			update_post_meta( $payment_id, '_bpmj_eddcm_purchase_limits_active', true );
			foreach ( $product_ids as $download_id => $price_id ) {
				bpmj_eddcm_decrease_items_left_in_purchase_limit( $download_id, $price_id, 1, $payment_id );
			}
		} else {
			update_post_meta( $payment_id, '_bpmj_eddcm_purchase_limits_active', false );
		}
	}
}

add_action( 'edd_insert_payment', 'bpmj_eddcm_handle_purchase_limit_on_insert_payment', 10, 2 );

/**
 * @param int $payment_id
 * @param string $status
 * @param string $old_status
 */
function bpmj_eddcm_handle_purchase_limit_on_payment_status( $payment_id, $status, $old_status ) {
	$purchase_limits = get_post_meta( $payment_id, '_bpmj_eddcm_purchase_limits', true );
	if ( ! $purchase_limits ) {
		return;
	}

	$purchase_limits_active = (bool) get_post_meta( $payment_id, '_bpmj_eddcm_purchase_limits_active', true );
	$change                 = 0;
	$excluded_statuses      = array( 'failed', 'refunded', 'abandoned', 'revoked' );
	if ( 'COMPLETE_PAYMENT' === WPI()->settings->get_purchase_limit_behaviour() ) {
		$excluded_statuses[] = 'pending';
	}
	if ( in_array( $status, $excluded_statuses ) ) {
		if ( $purchase_limits_active ) {
			$change = 1;
		}
	} else if ( ! $purchase_limits_active ) {
		$change = - 1;
	}
	if ( $change !== 0 ) {
		foreach ( $purchase_limits as $download_id => $price_id ) {
			bpmj_eddcm_change_items_left_in_purchase_limit( $download_id, $price_id, $change, $payment_id );
		}
		update_post_meta( $payment_id, '_bpmj_eddcm_purchase_limits_active', $change > 0 ? false : true );
	}
}

add_action( 'edd_update_payment_status', 'bpmj_eddcm_handle_purchase_limit_on_payment_status', 10, 3 );

/**
 * @param array $valid_data
 */
function bpmj_eddcm_validate_products( $valid_data ) {
	foreach ( edd_get_cart_contents() as $cart_key => $item ) {
		$product_id = $item[ 'id' ];
		$price_id   = isset( $item[ 'options' ][ 'price_id' ] ) ? $item[ 'options' ][ 'price_id' ] : 0;
		if ( ! bpmj_eddcm_can_purchase_product( $product_id, $price_id ) ) {
			edd_set_error( 'bpmj_eddcm_cannot_purchase_product_' . $cart_key, sprintf( __( 'The product \'%s\' cannot be purchased at this time.', BPMJ_EDDCM_DOMAIN ), edd_get_cart_item_name( $item ) ) );
		}
	}
}

add_action( 'edd_checkout_error_checks', 'bpmj_eddcm_validate_products' );

/*
 * dev
 */

function bpmj_eddcm_dev() {
	if ( ! empty( $_GET[ 'bpmj_eddcm_dev' ] ) ) {
		if ( 'on' == $_GET[ 'bpmj_eddcm_dev' ] ) {
			Helper::turn_on_dev();
		} else {
			Helper::turn_off_dev();
		}
	}
}

add_action( 'admin_init', 'bpmj_eddcm_dev' );

/**
 *
 */
function bpmj_eddcm_payment_method_icons_styles() {
	global $wpidea_settings;

	if ( empty( $wpidea_settings[ 'display_payment_methods_as_icons' ] ) || 'off' === $wpidea_settings[ 'display_payment_methods_as_icons' ] ) {
		return;
	}

	wp_register_style( 'bpmj_eddcm_payment_icons_css', BPMJ_EDDCM_URL . 'assets/css/payment-icons.css' );
	wp_enqueue_style( 'bpmj_eddcm_payment_icons_css' );
}

add_action( 'wp_enqueue_scripts', 'bpmj_eddcm_payment_method_icons_styles' );

/**
 *
 */
function bpmj_eddcm_add_more_button() {
	global $wpidea_settings;

	if ( ! empty( $wpidea_settings[ 'list_details_button' ] ) && 'true' === $wpidea_settings[ 'list_details_button' ] ): ?>
        <div class="clearfix">
            <a class="button button-read-more"
               href="<?= get_the_permalink(); ?>"><?php _e( 'Read more', BPMJ_EDDCM_DOMAIN ) ?>
            </a>
        </div>
	<?php
	endif;
}

add_action( 'edd_download_after_content', 'bpmj_eddcm_add_more_button' );

function bpmj_eddcm_continue_course_init() {
    $query_param_name = 'continue_course';

    if (!isset($_GET[$query_param_name])) {
        return;
    }

    $course_id = sanitize_text_field( $_GET[$query_param_name] );

    $user_progress = new Course_Progress( $course_id );
    $progress = $user_progress->get_progress();

    $last_lesson = WPI()->courses->get_first_lesson( $course_id );

    if ( ! empty( $progress ) ) {
        for ( $i = 0; $i <= ( $user_progress->get_course_lesson_count() - 2 ); $i++ ) {
            $course_page = WPI()->courses->get_next_sibling_of( $course_id, $last_lesson->ID );

            if( $course_page ) $last_lesson = $course_page;

            if ( ! array_key_exists( $course_page->ID, $progress ) ) {
                break;
            }
        }
    }

    $redirect_url = $last_lesson ? $last_lesson->get_permalink() : remove_query_arg($query_param_name);

    wp_redirect($redirect_url);
    exit;
}

add_action( 'init', 'bpmj_eddcm_continue_course_init' );
