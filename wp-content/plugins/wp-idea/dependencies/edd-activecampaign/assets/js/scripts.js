jQuery( document ).ready( function ( $ ) {
	$( '.bpmj-activecampaign-tags' ).tagsInput( {
		'height': '65px',
		'width': 'auto',
		'interactive': true,
		'defaultText': bpmj_eddact_admin.add_tag,
		'removeWithBackspace': true,
		'placeholderColor': '#666666'
	} );
} );