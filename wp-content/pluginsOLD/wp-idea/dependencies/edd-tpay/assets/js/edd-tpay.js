jQuery( document ).ready( function ( $ ) {
	$( document ).on( 'click', '#edd_purchase_form #edd_purchase_submit [type=submit]', function ( e ) {
		$( '#bpmj-eddtpay-submit-type' ).val( $( this ).val() );
	} );
} );