<?php

/**
 * Kod umożliwia przejście po wszystkich zamówieniach 
 * i dopisaniu czasu dostępu do meta użytkownika 
 * jeżeli przez błąd w kodzie nie został dopisany wcześniej
 */
$payments = edd_get_payments( array(
	'offset' => 0,
	'number' => -1,
	'status' => 'publish'
) );

foreach ( $payments as $payment ) {

	$payment_id	 = $payment->ID;
	$user_info	 = edd_get_payment_meta_user_info( $payment_id );
	$user		 = get_user_by( 'email', $user_info[ 'email' ] );

	if ( !$user )
		continue;

	$downloads = edd_get_payment_meta_cart_details( $payment_id );

	foreach ( $downloads as $download ) {
		$download_id = $download[ 'id' ];
		$access		 = get_user_meta( $user->ID, '_bpmj_eddpc_access', true );

		if ( isset( $access[ $download_id ] ) /* && $access[ $download_id ][ 'access_time' ] == NULL */ ) {

			//var_dump($user);
			//var_dump($access);

			//unset( $access[ $download_id ][ 'access_time' ] );
			//update_user_meta( $user->ID, '_bpmj_eddpc_access', $access );

		}
		else
			bpmj_eddpc_add_time_on_purchase( $download_id, $payment_id, $type = 'default', $download );
	}
}