<?php

/*
 * Funkcja sprawdza poprawnośc numeru NIP
 */

/**
 * @param string $str
 *
 * @return bool
 */
function bpmj_edd_invoice_data_check_nip( $str ) {
	$str = str_replace( array( ' ', '-' ), '', trim( $str ) );
	if ( strlen( $str ) != 10 ) {
		$cc = substr( $str, 0, 2 );
		if ( ! ctype_alpha( $cc ) ) {
			return false;
		}
		$str = substr( $str, 2 );

		if ( strlen( $str ) != 10 ) {
			return false;
		}
	}

	if ( ! ctype_digit( $str ) ) {
		return false;
	}

	$arrSteps = array( 6, 5, 7, 2, 3, 4, 5, 6, 7 );
	$intSum   = 0;
	for ( $i = 0; $i < 9; $i ++ ) {
		$intSum += $arrSteps[ $i ] * $str[ $i ];
	}
	$int = $intSum % 11;

	$intControlNr = ( $int == 10 ) ? 0 : $int;
	if ( $intControlNr == $str[ 9 ] ) {
		return true;
	}

	return false;
}

/**
 * @param string $option
 *
 * @return bool
 */
function bpmj_edd_invoice_data_get_cb_setting( $option ) {

	global $edd_options;

	if ( isset( $edd_options[ $option ] ) && $edd_options[ $option ] == '1' ) {
		return true;
	}

	return false;
}

/**
 * @param int $payment_id
 * @param string $field
 *
 * @return string
 */
function bpmj_edd_invoice_data_get_payment_meta_field( $payment_id, $field ) {
	static $cache = array();
	if ( ! isset( $cache[ $payment_id ] ) ) {
		$cache[ $payment_id ] = $payment_meta = edd_get_payment_meta( $payment_id );
	} else {
		$payment_meta = $cache[ $payment_id ];
	}

	return isset( $payment_meta[ $field ] ) ? $payment_meta[ $field ] : '';
}

/**
 * @param int $payment_id
 *
 * @return string
 */
function bpmj_edd_invoice_data_email_tag_invoice_type( $payment_id ) {
	$type = bpmj_edd_invoice_data_get_payment_meta_field( $payment_id, 'bpmj_edd_invoice_type' );
	switch ( $type ) {
		case 'person':
			return __( 'Individual', 'bpmj-edd-invoice-data' );
		default:
			return __( 'Company / Organization', 'bpmj-edd-invoice-data' );
	}
}

/**
 * @param int $payment_id
 *
 * @return string
 */
function bpmj_edd_invoice_data_email_tag_invoice_person_name( $payment_id ) {
	return bpmj_edd_invoice_data_get_payment_meta_field( $payment_id, 'bpmj_edd_invoice_person_name' );
}

/**
 * @param int $payment_id
 *
 * @return string
 */
function bpmj_edd_invoice_data_email_tag_invoice_company_name( $payment_id ) {
	return bpmj_edd_invoice_data_get_payment_meta_field( $payment_id, 'bpmj_edd_invoice_company_name' );

}

/**
 * @param int $payment_id
 *
 * @return string
 */
function bpmj_edd_invoice_data_email_tag_invoice_buyer_name( $payment_id ) {
	$person_name = bpmj_edd_invoice_data_get_payment_meta_field( $payment_id, 'bpmj_edd_invoice_person_name' );
	if ( $person_name ) {
		return $person_name;
	}

	return bpmj_edd_invoice_data_email_tag_invoice_company_name( $payment_id );
}

/**
 * @param int $payment_id
 *
 * @return string
 */
function bpmj_edd_invoice_data_email_tag_invoice_nip( $payment_id ) {
	return bpmj_edd_invoice_data_get_payment_meta_field( $payment_id, 'bpmj_edd_invoice_nip' );
}

/**
 * @param int $payment_id
 *
 * @return string
 */
function bpmj_edd_invoice_data_email_tag_invoice_street( $payment_id ) {
	return bpmj_edd_invoice_data_get_payment_meta_field( $payment_id, 'bpmj_edd_invoice_street' );
}

/**
 * @param int $payment_id
 *
 * @return string
 */
function bpmj_edd_invoice_data_email_tag_invoice_building_number( $payment_id ) {
    return bpmj_edd_invoice_data_get_payment_meta_field( $payment_id, 'bpmj_edd_invoice_building_number' );
}

/**
 * @param int $payment_id
 *
 * @return string
 */
function bpmj_edd_invoice_data_email_tag_invoice_apartment_number( $payment_id ) {
    return bpmj_edd_invoice_data_get_payment_meta_field( $payment_id, 'bpmj_edd_invoice_apartment_number' );
}

/**
 * @param int $payment_id
 *
 * @return string
 */
function bpmj_edd_invoice_data_email_tag_invoice_postcode( $payment_id ) {
	return bpmj_edd_invoice_data_get_payment_meta_field( $payment_id, 'bpmj_edd_invoice_postcode' );
}

/**
 * @param int $payment_id
 *
 * @return string
 */
function bpmj_edd_invoice_data_email_tag_invoice_city( $payment_id ) {
	return bpmj_edd_invoice_data_get_payment_meta_field( $payment_id, 'bpmj_edd_invoice_city' );
}

/**
 * @param int $payment_id
 *
 * @return bool
 */
function bpmj_edd_invoice_data_is_receiver_info_set( $payment_id ) {
	$receiver_info_set = bpmj_edd_invoice_data_get_payment_meta_field( $payment_id, 'bpmj_edd_invoice_receiver_info_set' );
	if ( empty( $receiver_info_set ) ) {
		return false;
	}

	return true;
}

/**
 * @param int $payment_id
 * @param string $receiver_meta_key
 * @param string $fallback_function_name
 *
 * @return string
 */
function bpmj_edd_invoice_data_email_tag_invoice_receiver_tag( $payment_id, $receiver_meta_key, $fallback_function_name ) {
	if ( bpmj_edd_invoice_data_is_receiver_info_set( $payment_id ) ) {
		return bpmj_edd_invoice_data_get_payment_meta_field( $payment_id, $receiver_meta_key );
	}

	return call_user_func( $fallback_function_name, $payment_id );
}

/**
 * @param int $payment_id
 *
 * @return string
 */
function bpmj_edd_invoice_data_email_tag_invoice_receiver_name( $payment_id ) {
	return bpmj_edd_invoice_data_email_tag_invoice_receiver_tag( $payment_id, 'bpmj_edd_invoice_receiver_name', 'bpmj_edd_invoice_data_email_tag_invoice_buyer_name' );
}

/**
 * @param int $payment_id
 *
 * @return string
 */
function bpmj_edd_invoice_data_email_tag_invoice_receiver_street( $payment_id ) {
	return bpmj_edd_invoice_data_email_tag_invoice_receiver_tag( $payment_id, 'bpmj_edd_invoice_receiver_street', 'bpmj_edd_invoice_data_email_tag_invoice_street' );
}

/**
 * @param int $payment_id
 *
 * @return string
 */
function bpmj_edd_invoice_data_email_tag_invoice_receiver_building_number( $payment_id ) {
    return bpmj_edd_invoice_data_email_tag_invoice_receiver_tag( $payment_id, 'bpmj_edd_invoice_receiver_building_number', 'bpmj_edd_invoice_data_email_tag_invoice_building_number' );
}

/**
 * @param int $payment_id
 *
 * @return string
 */
function bpmj_edd_invoice_data_email_tag_invoice_receiver_apartment_number( $payment_id ) {
    return bpmj_edd_invoice_data_email_tag_invoice_receiver_tag( $payment_id, 'bpmj_edd_invoice_receiver_apartment_number', 'bpmj_edd_invoice_data_email_tag_invoice_apartment_number' );
}

/**
 * @param int $payment_id
 *
 * @return string
 */
function bpmj_edd_invoice_data_email_tag_invoice_receiver_postcode( $payment_id ) {
	return bpmj_edd_invoice_data_email_tag_invoice_receiver_tag( $payment_id, 'bpmj_edd_invoice_receiver_postcode', 'bpmj_edd_invoice_data_email_tag_invoice_postcode' );
}

/**
 * @param int $payment_id
 *
 * @return string
 */
function bpmj_edd_invoice_data_email_tag_invoice_receiver_city( $payment_id ) {
	return bpmj_edd_invoice_data_email_tag_invoice_receiver_tag( $payment_id, 'bpmj_edd_invoice_receiver_city', 'bpmj_edd_invoice_data_email_tag_invoice_city' );
}
