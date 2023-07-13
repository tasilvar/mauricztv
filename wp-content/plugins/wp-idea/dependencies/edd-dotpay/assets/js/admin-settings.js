
/*
 * Skrypt pokazuje/ukrywa pola ustawień dla Dotpay
 * 
 * Skrypt po kliknięciu checkboxa Bramka transferuj.pl pokazuje lub ukrywa wszystkie ustawienia
 * dla bramki Dotpay widoczne w Karta ustawień |  EDD -> ustawienia -> Bramki płatności
 */

jQuery(document).ready(function() {

    // checbox do sterowania
    var checkbox = "input[name='edd_settings[gateways][dotpay_gateway]']";

    // elementy do ukrycia lub pokazania
    var item1 = jQuery("#dotpay_area").parent().parent();
    var item2 = jQuery("#dotpay_area").parent().parent().next();
    var item3 = jQuery("#dotpay_area").parent().parent().next().next();
    var item4 = jQuery("#dotpay_area").parent().parent().next().next().next();

    if (jQuery(checkbox).is(':checked')) {
        
        item1.show();
        item2.show();
        item3.show();
        item4.show();
        
    } else {
        
        item1.hide();
        item2.hide();
        item3.hide();
        item4.hide();
        
    }

    jQuery(checkbox).on("click", function() {
        if (jQuery(checkbox + ':checked').val() == '1') {

            item1.show();
            item2.show();
            item3.show();
            item4.show();

        } else {

            item1.hide();
            item2.hide();
            item3.hide();
            item4.hide();

        }
    });


});


