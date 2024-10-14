jQuery( window ).scroll( function () {
	if ( jQuery( this ).scrollTop() > 1 ) {
		jQuery( '.top-arrow' ).addClass( "up" );
	}
	else {
		jQuery( '.top-arrow' ).removeClass( "up" );
	}
} );
