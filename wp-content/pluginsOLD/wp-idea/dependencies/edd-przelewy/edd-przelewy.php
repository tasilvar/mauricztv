<?php
use bpmj\wpidea\helpers\Translator_Static_Helper;

include_once('includes/gateways/przelewy.php');
include_once('includes/actions.php');

function bpmj_prz_edd_register_gateway(array $gateways): array
{
    $gateways['przelewy_gateway'] = array(
        'admin_label' => Translator_Static_Helper::translate('settings.main.payment_methods.traditional_transfer'),
        'checkout_label' => Translator_Static_Helper::translate('settings.main.payment_methods.traditional_transfer'));
    return $gateways;
}

add_filter('edd_payment_gateways', 'bpmj_prz_edd_register_gateway');