+ function ( $ ) {
	'use strict';
	$( document ).ready( function () {
		$( '.payu-return-url' ).focus( function () {
			$( this ).get( 0 ).select();
		} );
		$( 'input[name="edd_settings[payu_api_type]"]' ).click( function () {
			var show_hide = 'hide';
			if ( 'classic' === $( this ).val() ) {
				show_hide = 'show';
			}
			$( '.payu-return-url' ).each( function () {
				$( this ).closest( 'tr' )[ show_hide ]();
			} );
		} );
		$( 'input[name="edd_settings[payu_api_type]"]:checked' ).click();
	} );
}( jQuery );