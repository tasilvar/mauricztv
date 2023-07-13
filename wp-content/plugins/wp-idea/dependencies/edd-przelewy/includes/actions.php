<?php
use bpmj\wpidea\helpers\Translator_Static_Helper;

add_action( 'edd_przelewy_gateway_cc_form', '__return_false' );

function add_transfer_details( string $label): string
{
    $purchase_session = edd_get_purchase_session();

    if(!$purchase_session || $purchase_session['gateway'] !== 'przelewy_gateway'){
      return $label;
    }

    global $edd_options;

    $transfer_details_name = $edd_options['edd_przelewy_name'] ?? null;
    $transfer_details_address = $edd_options['edd_przelewy_address'] ?? null;
    $transfer_details_account_number = $edd_options['edd_przelewy_account_number'] ?? null;

    $transfer_details_text = $transfer_details_name.'<br>'.$transfer_details_address.'<br>'.$transfer_details_account_number;

    if (!$transfer_details_name && !$transfer_details_address && !$transfer_details_account_number) {
        $transfer_details_text = Translator_Static_Helper::translate('settings.main.payment_methods.traditional_transfer.empty');
    }

    $transfer_details= '<p class="traditional-transfer"><strong>'.Translator_Static_Helper::translate('settings.main.payment_methods.traditional_transfer.transfer_details').'</strong><br>'.$transfer_details_text.'</p>';

    return $label.$transfer_details;
}

add_filter( 'edd_gateway_checkout_label', 'add_transfer_details', 10, 1);

