/**
 * This file adds some LIVE to the Theme Customizer live preview. To leverage
 * this, set your custom settings to 'postMessage' and then add your handling
 * here. Your javascript should grab settings from customizer controls, and 
 * then make any necessary changes to the page using jQuery.
 */
( function( $ ) {
    var html = document.querySelector('html');
    $.each( bpmj_eddcm_colors_settings, function( index, color ) {
        wp.customize( 'bpmj_eddcm_scarlet_colors_settings[color_' + color.name + ']' , function( value ) {
            value.bind( function( newval ) {
                html.style.setProperty('--' + color.name.replace(/_/g, '-'), newval);

                var next = bpmj_eddcm_colors_settings[ index + 1 ];
                if( next && next.name.indexOf('_inverted') !== -1){
                    var inverted = hexToComplimentary(newval);
                    var newColor = w3color( inverted );
                    var oldColor = w3color( newval );
                    if( oldColor.isDark() ){
                        newColor.lighter(50);
                    } else {
                        newColor.darker(50);
                    }
                    html.style.setProperty('--' + next.name.replace(/_/g, '-'), newColor.toHexString());
                }
            } );
        } );        
    });
} )( jQuery );