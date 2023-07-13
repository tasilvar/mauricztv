<?php

use bpmj\wpidea\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 *
 */
function bpmj_eddcm_checkout_buy_as_gift_option() {
	global $wpidea_settings;

	if ( ! bpmj_eddcm_is_buy_as_gift_possible() ) {
		return;
	}

	if ( ! bpmj_eddcm_is_gift_voucher_applied() ) {
		$bpmj_eddcm_gift = EDD()->session->get( 'bpmj_eddcm_gift' );
		$gift            = ! empty( $bpmj_eddcm_gift );

		?>
		<fieldset class="bpmj-eddcm-custom-checkbox bpmj-eddcm-buy-as-gift">
			<input type="checkbox" name="bpmj_eddcm_buy_as_gift" id="bpmj-eddcm-buy-as-gift"
			       value="1" <?php checked( $gift ); ?> />
			<label
				for="bpmj-eddcm-buy-as-gift"><?php _e( 'Buy as gift', BPMJ_EDDCM_DOMAIN ); ?>
			</label>
		</fieldset>
		<?php
	}
}

add_action( 'edd_purchase_form_top', 'bpmj_eddcm_checkout_buy_as_gift_option' );

function bpmj_eddcm_pre_complete_buy_as_gift( $payment_id ) {
	$eddcm_purchase_data = edd_get_payment_meta( $payment_id, 'bpmj_eddcm_purchase_data', true );
	if ( ! empty( $eddcm_purchase_data[ 'bpmj_eddcm_buy_as_gift' ] ) ) {
		remove_action( 'edd_complete_download_purchase', 'bpmj_eddpc_add_time_on_purchase', 99 );
		remove_action( 'edd_complete_purchase', 'edd_trigger_purchase_receipt', 999 );
		add_action( 'edd_complete_download_purchase', 'bpmj_eddcm_process_buy_as_gift', 90, 2 );
	}
}

add_action( 'edd_pre_complete_purchase', 'bpmj_eddcm_pre_complete_buy_as_gift' );

/**
 * @param int $download_id
 * @param int $payment_id
 */
function bpmj_eddcm_process_buy_as_gift(
	$download_id = 0, $payment_id = 0
) {
	global $bpmj_eddcm_process_buy_as_gift, $bpmj_eddcm_process_buy_as_gift_voucher_code;

	$buy_as_gift_vouchers = edd_get_payment_meta( $payment_id, 'bpmj_eddcm_buy_as_gift_vouchers', true );
	if ( empty( $buy_as_gift_vouchers ) ) {
		$buy_as_gift_vouchers = array();
	}

	if ( empty( $buy_as_gift_vouchers[ $download_id ] ) ) {
		$voucher_details         = bpmj_eddcm_generate_voucher_code_for_download( $download_id, bpmj_eddcm_get_price_id_in_payment( $payment_id, $download_id ) );
		$voucher_code            = $voucher_details[ 'code' ];
		$voucher_expiration_date = $voucher_details[ 'expiration' ];

		$buy_as_gift_vouchers[ $download_id ] = $voucher_code;
		edd_update_payment_meta( $payment_id, 'bpmj_eddcm_buy_as_gift_vouchers', $buy_as_gift_vouchers );
	} else {
		$voucher_code            = $buy_as_gift_vouchers[ $download_id ];
		$discount_id             = edd_get_discount_id_by_code( $voucher_code );
		$voucher_expiration_date = edd_get_discount_expiration( $discount_id );
	}
	bpmj_eddcm_setup_buy_as_gift_tags( $download_id, $voucher_code, $voucher_expiration_date );

	add_filter( 'edd_purchase_receipt', 'bpmj_eddcm_purchase_receipt_buy_as_gift' );
	add_filter( 'edd_receipt_attachments', 'bpmj_eddcm_purchase_receipt_buy_as_gift_attachments', 10, 2 );
	$bpmj_eddcm_process_buy_as_gift              = $download_id;
	$bpmj_eddcm_process_buy_as_gift_voucher_code = $voucher_code;
	edd_email_purchase_receipt( $payment_id, false );
	remove_filter( 'edd_receipt_attachments', 'bpmj_eddcm_purchase_receipt_buy_as_gift_attachments' );
	remove_filter( 'edd_purchase_receipt', 'bpmj_eddcm_purchase_receipt_buy_as_gift' );
}

/**
 * @param array $data
 */
function bpmj_eddcm_resend_purchase_receipt( $data ) {
	$payment_id          = absint( $data[ 'purchase_id' ] );
	$eddcm_purchase_data = edd_get_payment_meta( $payment_id, 'bpmj_eddcm_purchase_data', true );
	if ( ! empty( $eddcm_purchase_data[ 'bpmj_eddcm_buy_as_gift' ] ) ) {
		remove_action( 'edd_email_links', 'edd_resend_purchase_receipt' );
		$payment   = new EDD_Payment( $payment_id );
		$downloads = $payment->downloads;
		if ( ! empty( $downloads ) ) {
			foreach ( $downloads as $download ) {
				bpmj_eddcm_process_buy_as_gift( $download[ 'id' ], $payment_id );
			}
		}
	}
}

function bpmj_eddcm_settings_generate_voucher_preview() {
	if ( empty( $_POST[ 'action' ] ) || 'gift_pdf_voucher_preview' !== $_POST[ 'action' ] || empty( $_POST[ 'type' ] ) || ! isset( $_POST[ 'content' ] ) || ! isset( $_POST[ 'styles' ] ) ) {
		return;
	}
	$type              = $_POST[ 'type' ];
	$content_encoded   = $_POST[ 'content' ];
	$styles_encoded    = $_POST[ 'styles' ];
	$force_orientation = isset( $_POST[ 'orientation' ] ) ? $_POST[ 'orientation' ] : null;
	$apply_static_tags = function ( $content, $tags ) {
		return preg_replace_callback( '/\{(' . implode( '|', array_keys( $tags ) ) . ')\}/', function ( $match ) use ( $tags ) {
			return $tags[ $match[ 1 ] ];
		}, $content );
	};

	if ( isset( $_POST['pdftype'] ) && $_POST['pdftype'] == 'cert' ) {
        $static_tags  = array(
            'course_name'        => 'Test course name',
            'course_price'       => 199.99,
            'student_name'       => 'John Doe',
            'student_first_name' => 'John',
            'student_last_name'  => 'Doe',
            'certificate_date'   => date_i18n( 'Y-m-d', time() ),
        );
    } else {
        $voucher_code = bpmj_eddcm_generate_random_voucher_code();
        $static_tags  = array(
            'voucher_code'            => $voucher_code,
            'voucher_expiration_date' => date_i18n( get_option( 'date_format' ), strtotime( '+14 days' ) ),
            'redeem_link'             => get_permalink( bpmj_eddcm_get_option( 'voucher_page' ) ) . '?add-to-cart=0&discount=' . $voucher_code,
            'product_name'            => 'Lorem ipsum dolor',
            'name'                    => 'John',
            'fullname'                => 'John Smith',
            'username'                => 'johnsmith',
            'user_email'              => 'johnsmith@example.com',
            'date'                    => date_i18n( get_option( 'date_format' ), time() ),
            'payment_id'              => '9999',
            'receipt_id'              => '7777',
            'sitename'                => wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ),
        );
    }

	$page_styles = 'position: absolute;';
	if ( 'html' === $type ) {
        $page_styles = $page_styles . ' left: 50%; margin: 20px -' . ( ( $force_orientation === 'landscape' ) ? '561' : '397' ) . 'px;';
    }

	ob_start();
	?>
	<html>
	<head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<title><?php echo __( 'Preview as HTML', BPMJ_EDDCM_DOMAIN ); ?></title>
		<style type="text/css">
			<?php echo urldecode( base64_decode( $styles_encoded ) ); ?>
		</style>
	</head>
	<body style="background: #ccc;">
        <div id="bpmj_eddcm_page" style="<?php echo $page_styles; ?>">
	        <?php echo bpmj_eddcm_prepare_voucher_template_content( $apply_static_tags( urldecode( base64_decode( $content_encoded ) ), $static_tags ) ); ?>
        </div>
	</body>
	</html>
	<?php
	$pdf_html = ob_get_clean();

	if ( 'html' === $type ) {
		header( 'Content-type: text/html; charset=UTF-8' );
		echo $pdf_html;
	} else if ( 'pdf' === $type ) {
        try {
            $pdf = bpmj_eddcm_render_pdf($pdf_html, $force_orientation);
            $pdf->stream();
        } catch ( Exception $e ) {
            echo '<h1>' . __( 'The certificate could not be retrieved due to invalid settings. Contact support.', BPMJ_EDDCM_DOMAIN ) . '</h1>';
            exit;
        }
	}

	die;
}

add_action( 'load-wp-idea_page_wp-idea-settings', 'bpmj_eddcm_settings_generate_voucher_preview' );

/**
 * @param int $payment_id
 */
function bpmj_eddcm_order_details_gift_vouchers( $payment_id ) {
	$eddcm_purchase_data = edd_get_payment_meta( $payment_id, 'bpmj_eddcm_purchase_data', true );
	if ( empty( $eddcm_purchase_data[ 'bpmj_eddcm_buy_as_gift' ] ) ) {
		return;
	}

	$buy_as_gift_vouchers = edd_get_payment_meta( $payment_id, 'bpmj_eddcm_buy_as_gift_vouchers', true );
	if(!is_array($buy_as_gift_vouchers)) {
	    return;
	}

	?>
    <div class="edd-admin-box-inside">
        <p>
            <span class="label"><?php _e( 'Bought as a gift. List of generated voucher codes:', BPMJ_EDDCM_DOMAIN ); ?></span>
        </p>
        <ul>
			<?php foreach ( $buy_as_gift_vouchers as $download_id => $voucher_code ):
				$download_name = get_the_title( $download_id );
				/** @var WP_Post $voucher */
				$voucher = edd_get_discount_by_code( $voucher_code );
				?>
                <li><?php if ( $voucher instanceof WP_Post ): ?>
                        <a href="<?php echo admin_url('edit.php?post_type=download&page=edd-discounts&edd-action=edit_discount&discount=' . $voucher->ID); ?>"><?php echo esc_html( $voucher_code ); ?></a>
					<?php else: ?>
						<?php echo esc_html( $voucher_code ); ?>
					<?php endif; ?>
                    -
                    <?php echo esc_html($download_name); ?>
                </li>
			<?php endforeach; ?>
        </ul>
    </div>
	<?php

}

add_action( 'edd_view_order_details_totals_after', 'bpmj_eddcm_order_details_gift_vouchers' );

function bpmj_eddcm_add_voucher_holder_div() {
    if( ! edd_is_checkout() || ! Helper::is_voucher_page() ) {
        return;
    }

    echo '<div class="bpmj-eddcm-voucher-table">';
}

add_action( 'edd_before_checkout_cart', 'bpmj_eddcm_add_voucher_holder_div', 10 );

function bpmj_eddcm_close_voucher_holder_div(){
    if( ! edd_is_checkout() || ! Helper::is_voucher_page() ) {
        return;
    }

    echo '</div>';
}

add_action( 'edd_after_purchase_form', 'bpmj_eddcm_close_voucher_holder_div', 10 );