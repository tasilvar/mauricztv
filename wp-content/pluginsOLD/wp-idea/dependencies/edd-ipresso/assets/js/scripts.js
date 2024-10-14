jQuery( document ).ready( function ( $ ) {
	$( '.ipresso-tags' ).tagsInput( {
		'height': '65px',
		'width': 'auto',
		'interactive': true,
		'defaultText': bpmj_eddip_admin.add_tag,
		'removeWithBackspace': true,
		'placeholderColor': '#666666'
	} );
} );