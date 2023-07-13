/*
 * Skrypt pokazuje/ukrywa pola ustawień dla Przelewy24.pl
 * 
 * Skrypt po kliknięciu checkboxa Bramka przelewy24.pl pokazuje lub ukrywa wszystkie ustawienia
 * dla bramki Przelewy24.pl widoczne w Karta ustawień |  EDD -> ustawienia -> Bramki płatności
 */


jQuery( document ).ready( function () {

    // checbox do sterowania
    var checkbox = "input[name='edd_settings[gateways][przelewy24_gateway]']";

    // elementy do ukrycia lub pokazania
    var item1 = jQuery( "#przelewy24_area" ).parent().parent();
    var item2 = jQuery( "#przelewy24_area" ).parent().parent().next();
    var item3 = jQuery( "#przelewy24_area" ).parent().parent().next().next();

    if ( jQuery( checkbox ).is( ':checked' ) ) {

        item1.show();
        item2.show();
        item3.show();

    } else {

        item1.hide();
        item2.hide();
        item3.hide();

    }

    jQuery( checkbox ).on( "click", function () {
        if ( jQuery( checkbox + ':checked' ).val() == '1' ) {

            item1.show();
            item2.show();
            item3.show();

        } else {

            item1.hide();
            item2.hide();
            item3.hide();

        }
    } );


} );