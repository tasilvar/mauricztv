<?php

use bpmj\wpidea\Packages;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return array
 */
function bpmj_eddcm_get_standard_gift_tags() {
	return array(
		array(
			'tag'         => 'voucher_code',
			'description' => __( 'Voucher code', BPMJ_EDDCM_DOMAIN ),
			'bold'        => true,
		),
		array(
			'tag'         => 'voucher_expiration_date',
			'description' => __( 'Voucher expiration date', BPMJ_EDDCM_DOMAIN ),
			'bold'        => true,
		),
		array(
			'tag'         => 'redeem_link',
			'description' => __( 'Redeem code link', BPMJ_EDDCM_DOMAIN ),
			'bold'        => true,
		),
		array(
			'tag'         => 'product_name',
			'description' => __( 'Product name', BPMJ_EDDCM_DOMAIN ),
			'bold'        => true,
		),
	);
}

/**
 * @return array
 */
function bpmj_eddcm_get_gift_pdf_tags() {
	$gift_pdf_tags = bpmj_eddcm_get_standard_gift_tags();

	return array_merge( $gift_pdf_tags, bpmj_eddcm_edd_email_tags_without( array(
		'download_list',
		'file_urls',
		'billing_address',
		'subtotal',
		'tax',
		'price',
		'payment_method',
		'receipt_link',
		'discount_codes',
		'ip_address',
	) ) );
}

/**
 * @return string
 */
function bpmj_eddcm_get_gift_pdf_tag_list() {
	return bpmj_eddcm_template_tag_list( bpmj_eddcm_get_gift_pdf_tags() );
}

/**
 * @return array
 */
function bpmj_eddcm_get_gift_email_tags() {
	$gift_email_tags = bpmj_eddcm_get_standard_gift_tags();

	return array_merge( $gift_email_tags, bpmj_eddcm_edd_email_tags_without( array(
		'download_list',
		'file_urls',
	) ) );
}

/**
 * @return string
 */
function bpmj_eddcm_get_gift_email_tag_list() {
	return bpmj_eddcm_template_tag_list( bpmj_eddcm_get_gift_email_tags() );
}

/**
 * @param int $download_id
 * @param int $price_id
 * @param string $start_date
 *
 * @return array
 */
function bpmj_eddcm_generate_voucher_code_for_download( $download_id, $price_id = 0, $start_date = null ) {
	global $wpidea_settings;

	$expiration_offset = isset( $wpidea_settings[ 'buy_as_gift_expiration_period' ] ) ? $wpidea_settings[ 'buy_as_gift_expiration_period' ] : '';
	if ( empty( $expiration_offset ) ) {
		$expiration_offset = '14 days';
	}

	if ( empty( $start_date ) ) {
		$start_date = substr( current_time( 'mysql' ), 0, 10 ) . ' 00:00:00';
	}

	$expiration_date = date( 'Y-m-d', strtotime( $expiration_offset, strtotime( $start_date ) ) ) . ' 23:59:59';
	$voucher_code    = bpmj_eddcm_generate_random_voucher_code( $download_id );

	$discount_details = array(
		'name'              => __( 'Gift voucher', BPMJ_EDDCM_DOMAIN ) . ' - ' . $voucher_code,
		'code'              => $voucher_code,
		'max'               => 1,
		'amount'            => 100,
		'start'             => $start_date,
		'expiration'        => $expiration_date,
		'type'              => 'percent',
		'products'          => array( $download_id ),
		'product_condition' => 'all',
		'not_global'        => true,
		'use_once'          => true,
	);

	$discount_id              = edd_store_discount( $discount_details );
	$discount_details[ 'ID' ] = $discount_id;

	update_post_meta( $discount_id, '_bpmj_eddcm_download_id', $download_id );
	update_post_meta( $discount_id, '_bpmj_eddcm_price_id', $price_id );
	update_post_meta( $discount_id, '_bpmj_eddcm_gift_voucher', true );

	return $discount_details;
}

/**
 * @param int $download_id
 *
 * @return string
 */
function bpmj_eddcm_generate_random_voucher_code( $download_id = 0 ) {
	return strtoupper( 'P' . str_pad( base_convert( $download_id, 10, 36 ), 4, 'D', STR_PAD_LEFT ) . wp_generate_password( 8, false, false ) );
}

/**
 * @param int $download_id
 * @param string $voucher_code
 * @param string $voucher_expiration_date
 */
function bpmj_eddcm_setup_buy_as_gift_tags( $download_id, $voucher_code, $voucher_expiration_date ) {
	edd_add_email_tag( 'voucher_code', '', function ( $payment_id ) use ( $voucher_code ) {
		return $voucher_code;
	} );
	edd_add_email_tag( 'voucher_expiration_date', '', function ( $payment_id ) use ( $voucher_expiration_date ) {
		return date_i18n( get_option( 'date_format' ), strtotime( $voucher_expiration_date ) );
	} );
	edd_add_email_tag( 'redeem_link', '', function ( $payment_id ) use ( $download_id, $voucher_code ) {
		$price_id     = bpmj_eddcm_get_price_id_in_payment( $payment_id, $download_id );
		$price_id_arg = '';
		if ( $price_id > 0 ) {
			$price_id_arg = '&price-id=' . $price_id;
		}

		return get_permalink( bpmj_eddcm_get_option( 'voucher_page' ) ) . '?add-to-cart=' . $download_id . '&discount=' . $voucher_code . $price_id_arg;
	} );
	edd_add_email_tag( 'product_name', '', function ( $payment_id ) use ( $download_id ) {
		$price_id = bpmj_eddcm_get_price_id_in_payment( $payment_id, $download_id );

		if ( $price_id > 0 ) {
			return get_the_title( $download_id ) . ' - ' . edd_get_price_option_name( $download_id, $price_id, $payment_id );
		}

		return get_the_title( $download_id );
	} );
}

/**
 * @return bool
 */
function bpmj_eddcm_is_gift_voucher_applied() {
	$discounts = edd_get_cart_discounts();

	if ( ! $discounts ) {
		return false;
	}

	foreach ( $discounts as $discount ) {
		$discount_id     = edd_get_discount_id_by_code( $discount );
		$is_gift_voucher = get_post_meta( $discount_id, '_bpmj_eddcm_gift_voucher', true );
		if ( ! empty( $is_gift_voucher ) ) {
			return true;
		}
	}

	return false;
}

/**
 * @return bool
 */
function bpmj_eddcm_is_buy_as_gift_possible() {
	global $wpidea_settings;

	$ret = true;
	if ( ! isset( $wpidea_settings[ 'enable_buy_as_gift' ] ) || 'on' !== $wpidea_settings[ 'enable_buy_as_gift' ] ) {
		$ret = false;
	}

	if ( WPI()->packages->no_access_to_feature( Packages::FEAT_BUY_AS_GIFT ) ) {
		$ret = false;
	}

	return apply_filters( 'bpmj_eddcm_is_buy_as_gift_possible', $ret );
}

/**
 * @param string $content_html
 *
 * @return string
 */
function bpmj_eddcm_prepare_voucher_template_content( $content_html ) {
    // Fix "aligncenters"
    $content_html = preg_replace( '/<img[^>]+class="[^"]*?aligncenter[^"]*?"[^>]*>/i', '<div style="text-align: center">$0</div>', $content_html );

    $content_html = wpautop( $content_html );

    return apply_filters( 'bpmj_eddcm_prepare_voucher_template_content', $content_html );
}

/**
 * @param string $content_html
 *
 * @return string
 */
function bpmj_eddcm_prepare_certificate_template_content( $content_html ) {
    // Fix "aligncenters"
    $content_html = preg_replace( '/<img[^>]+class="[^"]*?aligncenter[^"]*?"[^>]*>/i', '<div style="text-align: center">$0</div>', $content_html );

    $content_html = wpautop( $content_html );

    return apply_filters( 'bpmj_eddcm_prepare_certificate_template_content', $content_html );
}

/**
 * @param string $pdf_html
 * @param string $force_orientation
 *
 * @return \Dompdf\Dompdf
 */
function bpmj_eddcm_render_pdf( $pdf_html, $force_orientation = null ) {
	global $wpidea_settings;

	$dompdf_options = new \Dompdf\Options();
	$dompdf_options->setIsRemoteEnabled( true );
    $dompdf_options->setTempDir(get_temp_dir());
	$dompdf      = new \Dompdf\Dompdf( $dompdf_options );
	$orientation = $force_orientation ?: (isset($wpidea_settings['gift_pdf_voucher_orientation']) ? $wpidea_settings['gift_pdf_voucher_orientation'] : 'portrait');
	if ( $orientation ) {
		$dompdf->setPaper( 'A4', $orientation );
	}
	$dompdf->loadHtml( $pdf_html );
	$dompdf->render();

	return $dompdf;
}
