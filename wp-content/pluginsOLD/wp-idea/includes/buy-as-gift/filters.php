<?php

use bpmj\wpidea\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @param array $purchase_data
 *
 * @return array
 */
function bpmj_eddcm_purchase_data_buy_as_gift( $purchase_data ) {
	$data = $purchase_data[ 'post_data' ];
	if ( ! bpmj_eddcm_is_buy_as_gift_possible() || empty( $data[ 'bpmj_eddcm_buy_as_gift' ] ) ) {
		return $purchase_data;
	}

	$purchase_data[ 'bpmj_eddcm_buy_as_gift' ] = true;

	return $purchase_data;
}

add_filter( 'edd_purchase_data_before_gateway', 'bpmj_eddcm_purchase_data_buy_as_gift' );

/**
 * @param string $email_body
 *
 * @return string
 */
function bpmj_eddcm_purchase_receipt_buy_as_gift( $email_body ) {
	global $wpidea_settings;

	$purchase_receipt_template = isset( $wpidea_settings[ 'buy_as_gift_email_body' ] ) ? $wpidea_settings[ 'buy_as_gift_email_body' ] : '';
	if ( empty( $purchase_receipt_template ) ) {
		return $email_body;
	}

	return $purchase_receipt_template;
}

/**
 * @param array $attachments
 *
 * @return array
 */
function bpmj_eddcm_purchase_receipt_buy_as_gift_attachments( $attachments, $payment_id ) {
	global $wpidea_settings;
	global $bpmj_eddcm_process_buy_as_gift_voucher_code;

	if ( empty( $wpidea_settings[ 'enable_gift_pdf_voucher' ] ) || 'on' !== $wpidea_settings[ 'enable_gift_pdf_voucher' ] || empty( $wpidea_settings[ 'gift_pdf_voucher_template' ] ) ) {
		return $attachments;
	}

	ob_start();
	?>
	<html>
	<head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<style type="text/css">
			<?php echo $wpidea_settings['gift_pdf_voucher_styles']; ?>
			#bpmj_eddcm_page {
				background: url("<?= $wpidea_settings['voucher_bg'] ?>") no-repeat center;
                height: 100%; width: 100%;
			}
		</style>
	</head>
	<body>
        <div id="bpmj_eddcm_page" style="position: absolute;">
			<?php echo bpmj_eddcm_prepare_voucher_template_content( edd_do_email_tags( $wpidea_settings[ 'gift_pdf_voucher_template' ], $payment_id ) ); ?>
		</div>
	</body>
	</html>
	<?php
	$pdf_html = ob_get_clean();
	$dompdf   = bpmj_eddcm_render_pdf( $pdf_html );

	$pdf_file_path = get_temp_dir() . $bpmj_eddcm_process_buy_as_gift_voucher_code . '.pdf';
	file_put_contents( $pdf_file_path, $dompdf->output() );

	$attachments[] = $pdf_file_path;

	return $attachments;
}

/**
 * @param bool $show
 * @param int $item_id
 * @param array $receipt_args
 *
 * @return bool
 */
function bpmj_eddcm_hide_download_files_if_bought_as_gift( $show, $item_id, $receipt_args ) {
	$payment_id          = $receipt_args[ 'id' ];
	$eddcm_purchase_data = edd_get_payment_meta( $payment_id, 'bpmj_eddcm_purchase_data', true );
	if ( ! empty( $eddcm_purchase_data[ 'bpmj_eddcm_buy_as_gift' ] ) ) {
		return false;
	}

	return $show;
}

add_filter( 'edd_receipt_show_download_files', 'bpmj_eddcm_hide_download_files_if_bought_as_gift', 10, 3 );

/**
 * @param bool $ret
 * @param int $code_id
 *
 * @return bool
 */
function bpmj_eddcm_check_discount_price_id( $ret, $code_id ) {
	if ( ! $ret ) {
		return $ret;
	}

	$price_id   = get_post_meta( $code_id, '_bpmj_eddcm_price_id', true );
	$product_id = get_post_meta( $code_id, '_bpmj_eddcm_download_id', true );
	if ( ! $price_id || ! $product_id ) {
		return $ret;
	}

	if ( ! edd_item_in_cart( $product_id, array( 'price_id' => $price_id ) ) ) {
		edd_set_error( 'edd-discount-error', __( 'The product requirements for this discount are not met.', 'easy-digital-downloads' ) );
		$ret = false;
	}

	return $ret;
}

add_filter( 'edd_is_discount_products_req_met', 'bpmj_eddcm_check_discount_price_id', 10, 2 );

/**
 * @param bool $ret
 * @param int $payment_id
 * @param int $download_id
 *
 * @return bool
 */
function bpmj_eddcm_ignore_purchase_limits_on_voucher( $ret, $payment_id, $download_id ) {
	if ( $ret ) {
		return $ret;
	}

	$payment   = new EDD_Payment( $payment_id );
	$discounts = $payment->discounts;
	if ( empty( $discounts ) ) {
		return $ret;
	} else if ( ! is_array( $discounts ) ) {
		$discounts = array( $discounts );
	}

	foreach ( $discounts as $discount_code ) {
		$discount_id              = edd_get_discount_id_by_code( $discount_code );
		$discount_is_gift_voucher = get_post_meta( $discount_id, '_bpmj_eddcm_gift_voucher', true );
		if ( ! empty( $discount_is_gift_voucher ) ) {
			$discount_download_id = get_post_meta( $discount_id, '_bpmj_eddcm_download_id', true );
			if ( $download_id == $discount_download_id ) {
				return true;
			}
		}
	}

	return $ret;
}

add_filter( 'bpmj_eddcm_should_ignore_purchase_limits', 'bpmj_eddcm_ignore_purchase_limits_on_voucher', 10, 3 );

/**
 * @param bool $ret
 * @param int $product_id
 * @param int $price_id
 *
 * @return bool
 */
function bpmj_eddcm_can_purchase_product_on_voucher( $ret, $product_id, $price_id ) {
	if ( $ret ) {
		// No need to check a voucher if the product can be purchased
		return $ret;
	}

	if ( bpmj_eddcm_is_item_in_cart( $product_id, $price_id ) ) {
		foreach ( edd_get_cart_discounts() as $discount_code ) {
			$discount_id = edd_get_discount_id_by_code( $discount_code );
			if ( ! $discount_id || ! edd_is_discount_active( $discount_id ) || edd_is_discount_used( $discount_code, '', $discount_id ) ) {
				// The specified discount code is not valid or active or it already has been used
				return $ret;
			}

			$discount_is_gift_voucher = get_post_meta( $discount_id, '_bpmj_eddcm_gift_voucher', true );
			if ( empty( $discount_is_gift_voucher ) ) {
				// The discount code is not a gift voucher
				return $ret;
			}

			$discount_download_id = get_post_meta( $discount_id, '_bpmj_eddcm_download_id', true );
			$discount_price_id    = get_post_meta( $discount_id, '_bpmj_eddcm_price_id', true );
			if ( $discount_download_id != $product_id || $discount_price_id != $price_id ) {
				// The voucher code is for another product
				return $ret;
			}
		}
	} else {
		if ( empty( $_GET[ 'add-to-cart' ] ) || $_GET[ 'add-to-cart' ] != $product_id || $price_id > 0 && ( empty( $_GET[ 'price-id' ] ) || $price_id != $_GET[ 'price-id' ] ) ) {
			// The product is not being added by a link
			return $ret;
		}

		if ( empty( $_GET[ 'discount' ] ) ) {
			// A discount code is not used
			return $ret;
		}

		$discount_id = edd_get_discount_id_by_code( $_GET[ 'discount' ] );
		if ( ! $discount_id || ! edd_is_discount_active( $discount_id ) || edd_is_discount_used( $_GET[ 'discount' ], '', $discount_id ) ) {
			// The specified discount code is not valid or active or it already has been used
			return $ret;
		}

		$discount_is_gift_voucher = get_post_meta( $discount_id, '_bpmj_eddcm_gift_voucher', true );
		if ( empty( $discount_is_gift_voucher ) ) {
			// The discount code is not a gift voucher
			return $ret;
		}

		$discount_download_id = get_post_meta( $discount_id, '_bpmj_eddcm_download_id', true );
		$discount_price_id    = get_post_meta( $discount_id, '_bpmj_eddcm_price_id', true );
		if ( $discount_download_id != $product_id || $discount_price_id != $price_id ) {
			// The voucher code is for another product
			return $ret;
		}
	}

	return true;
}

add_filter( 'bpmj_eddcm_can_purchase_product', 'bpmj_eddcm_can_purchase_product_on_voucher', 10, 3 );

function bpmj_eddcm_modify_purchase_button_text( $purchase_button ) {
    if( ! edd_is_checkout() || ! Helper::is_voucher_page() ) {
        return $purchase_button;
    }
    $pattern = '/value="(.*)"/';
    $redeem_voucher_text = 'value="' . __( 'Redeem voucher', BPMJ_EDDCM_DOMAIN ) . '""';
    $purchase_button = preg_replace( $pattern, $redeem_voucher_text, $purchase_button );

    return $purchase_button;
}

add_filter( 'edd_checkout_button_purchase', 'bpmj_eddcm_modify_purchase_button_text', 10, 1 );

function edd_is_voucher_page( $checkout ) {
    if( Helper::is_voucher_page() )
        return true;
    return $checkout;
}

add_filter( 'edd_is_checkout', 'edd_is_voucher_page', 1, 1 );